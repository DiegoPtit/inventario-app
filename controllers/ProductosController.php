<?php

namespace app\controllers;

use app\models\Productos;
use app\models\ProductosSearch;
use app\models\Entradas;
use app\models\Lugares;
use app\models\Stock;
use app\models\HistoricoPreciosDolar;
use app\models\Categorias;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * ProductosController implements the CRUD actions for Productos model.
 */
class ProductosController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Productos models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductosSearch();
        
        // Manejar búsqueda general
        $queryParams = $this->request->queryParams;
        if (isset($queryParams['search']) && !empty($queryParams['search'])) {
            $queryParams['ProductosSearch']['search'] = $queryParams['search'];
        }
        
        $dataProvider = $searchModel->search($queryParams);

        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Obtener valores únicos para los filtros
        $colores = Productos::find()
            ->select('color')
            ->distinct()
            ->where(['IS NOT', 'color', null])
            ->andWhere(['<>', 'color', ''])
            ->orderBy(['color' => SORT_ASC])
            ->column();
        
        $unidadesMedida = Productos::find()
            ->select('unidad_medida')
            ->distinct()
            ->where(['IS NOT', 'unidad_medida', null])
            ->andWhere(['<>', 'unidad_medida', ''])
            ->orderBy(['unidad_medida' => SORT_ASC])
            ->column();
        
        // Obtener categorías
        $categorias = ArrayHelper::map(
            Categorias::find()->orderBy(['titulo' => SORT_ASC])->all(),
            'id',
            'titulo'
        );
        
        // Obtener lugares únicos que tienen stock
        $lugares = ArrayHelper::map(
            Lugares::find()
                ->innerJoin('stock', 'stock.id_lugar = lugares.id')
                ->where(['>', 'stock.cantidad', 0])
                ->groupBy(['lugares.id', 'lugares.nombre'])
                ->orderBy(['lugares.nombre' => SORT_ASC])
                ->all(),
            'id',
            'nombre'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
            'colores' => $colores,
            'unidadesMedida' => $unidadesMedida,
            'categorias' => $categorias,
            'lugares' => $lugares,
        ]);
    }

    /**
     * Displays a single Productos model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Creates a new Productos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Productos();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Capturar archivos de imagen solo si se subieron
                $uploadedFiles = UploadedFile::getInstances($model, 'imageFiles');
                if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                    $model->imageFiles = $uploadedFiles;
                } else {
                    // Si no hay imágenes subidas, asignar null para evitar errores
                    $model->imageFiles = null;
                }
                
                // Iniciar una transacción
                $transaction = Yii::$app->db->beginTransaction();
                
                try {
                    // Subir imágenes solo si hay archivos válidos
                    if (!empty($model->imageFiles) && is_array($model->imageFiles)) {
                        $fotosArray = $model->uploadImages();
                        if ($fotosArray !== false && !empty($fotosArray)) {
                            $model->fotos = json_encode($fotosArray);
                        }
                    }
                    
                    // Guardar el producto
                    if (!$model->save()) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Error al guardar el producto.');
                        return $this->render('create', ['model' => $model]);
                    }
                    
                    // Si hay cantidad inicial, crear entrada y actualizar stock
                    if (!empty($model->cantidad) && $model->cantidad > 0 && !empty($model->id_lugar)) {
                        // Crear entrada
                        $entrada = new Entradas();
                        $entrada->id_producto = $model->id;
                        $entrada->cantidad = $model->cantidad;
                        $entrada->id_lugar = $model->id_lugar;
                        $entrada->tipo_entrada = 'Inventario Inicial';
                        $entrada->created_at = date('Y-m-d H:i:s');
                        
                        if (!$entrada->save()) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Error al crear la entrada de inventario.');
                            return $this->render('create', ['model' => $model]);
                        }
                        
                        // Actualizar o crear stock
                        $stock = Stock::findOne(['id_producto' => $model->id, 'id_lugar' => $model->id_lugar]);
                        if (!$stock) {
                            $stock = new Stock();
                            $stock->id_producto = $model->id;
                            $stock->id_lugar = $model->id_lugar;
                            $stock->cantidad = 0;
                        }
                        $stock->cantidad += $model->cantidad;
                        $stock->updated_at = date('Y-m-d H:i:s');
                        
                        if (!$stock->save()) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Error al actualizar el stock.');
                            return $this->render('create', ['model' => $model]);
                        }
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Producto creado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                    
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Productos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Capturar archivos de imagen solo si se subieron
                $uploadedFiles = UploadedFile::getInstances($model, 'imageFiles');
                if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                    $model->imageFiles = $uploadedFiles;
                    
                    // Subir nuevas imágenes si existen
                    $fotosArray = $model->uploadImages();
                    if ($fotosArray !== false && !empty($fotosArray)) {
                        $model->fotos = json_encode($fotosArray);
                    }
                } else {
                    // Si no hay imágenes subidas, asignar null
                    $model->imageFiles = null;
                }
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Producto actualizado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Productos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Genera un reporte de inventario
     * @return string
     */
    public function actionReporte()
    {
        $tipo = Yii::$app->request->get('tipo', 'general');
        $idLugar = Yii::$app->request->get('id_lugar');
        
        // Configurar el layout para la vista de impresión
        $this->layout = false;
        
        // Obtener precios del dólar para conversiones
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Obtener los datos según el tipo de reporte
        if ($tipo === 'por-lugar' && !empty($idLugar)) {
            // Reporte por lugar específico
            $lugares = [\app\models\Lugares::findOne($idLugar)];
            if (!$lugares[0]) {
                throw new NotFoundHttpException('El almacén no existe.');
            }
        } else {
            // Reporte general (todos los lugares)
            $lugares = \app\models\Lugares::find()->orderBy(['nombre' => SORT_ASC])->all();
        }
        
        // Preparar datos del reporte agrupados por lugar
        $datosReporte = [];
        
        foreach ($lugares as $lugar) {
            // Obtener productos que tienen stock en este lugar
            $stocks = Stock::find()
                ->where(['id_lugar' => $lugar->id])
                ->andWhere(['>', 'cantidad', 0])
                ->with('producto')
                ->all();
            
            if (empty($stocks)) {
                continue; // Saltar lugares sin stock
            }
            
            $productos = [];
            $totalCosto = 0;
            $totalPrecioVenta = 0;
            $totalCantidad = 0;
            
            foreach ($stocks as $stock) {
                $producto = $stock->producto;
                if (!$producto) {
                    continue;
                }
                
                $productos[] = [
                    'marca' => $producto->marca,
                    'modelo' => $producto->modelo,
                    'color' => $producto->color,
                    'contenido_neto' => $producto->contenido_neto,
                    'unidad_medida' => $producto->unidad_medida,
                    'costo' => $producto->costo,
                    'precio_venta' => $producto->precio_venta,
                    'cantidad' => $stock->cantidad,
                    'subtotal_costo' => $producto->costo * $stock->cantidad,
                    'subtotal_venta' => $producto->precio_venta * $stock->cantidad,
                ];
                
                $totalCosto += $producto->costo * $stock->cantidad;
                $totalPrecioVenta += $producto->precio_venta * $stock->cantidad;
                $totalCantidad += $stock->cantidad;
            }
            
            $datosReporte[] = [
                'lugar' => $lugar,
                'productos' => $productos,
                'total_costo' => $totalCosto,
                'total_precio_venta' => $totalPrecioVenta,
                'total_cantidad' => $totalCantidad,
            ];
        }
        
        return $this->render('reporte', [
            'datosReporte' => $datosReporte,
            'tipo' => $tipo,
            'fecha' => date('d/m/Y H:i:s'),
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Finds the Productos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Productos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Productos::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
