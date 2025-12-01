<?php

namespace app\controllers;

use app\models\HistoricoInventarios;
use app\models\HistoricoInventariosSearch;
use app\models\Entradas;
use app\models\Productos;
use app\models\HistoricoPreciosDolar;
use app\models\HistoricoMovimientos;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * HistoricoInventariosController implements the CRUD actions for HistoricoInventarios model.
 */
class HistoricoInventariosController extends Controller
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
     * Lists all HistoricoInventarios models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new HistoricoInventariosSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Displays a single HistoricoInventarios model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Obtener todas las entradas del período usando datetime completo
        $entradas = Entradas::find()
            ->where(['>=', 'created_at', $model->fecha_inicio])
            ->where(['<=', 'created_at', $model->fecha_cierre])
            ->with(['producto', 'proveedor'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
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
            'model' => $model,
            'entradas' => $entradas,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Creates a new HistoricoInventarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new HistoricoInventarios();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HistoricoInventarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HistoricoInventarios model.
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
     * Obtiene los datos calculados para el cierre de inventario
     * @return array JSON response
     */
    public function actionGetDataCierre()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            // Obtener el último inventario cerrado
            $ultimoInventario = HistoricoInventarios::find()
                ->orderBy(['fecha_cierre' => SORT_DESC])
                ->one();
            
            // Determinar fecha-hora de inicio
            if ($ultimoInventario) {
                // Usar el datetime completo del último cierre
                $fechaInicio = $ultimoInventario->fecha_cierre;
            } else {
                // Si no hay inventario cerrado, tomar el datetime de la primera entrada
                $primeraEntrada = Entradas::find()
                    ->orderBy(['created_at' => SORT_ASC])
                    ->one();
                
                $fechaInicio = $primeraEntrada ? $primeraEntrada->created_at : date('Y-m-d H:i:s');
            }
            
            // Fecha-hora de cierre es el momento actual
            $fechaCierre = date('Y-m-d H:i:s');
            
            // Calcular cantidad de productos y valor total
            // Filtrar entradas desde la hora exacta del último cierre hasta ahora
            $entradas = Entradas::find()
                ->where(['>=', 'created_at', $fechaInicio])
                ->where(['<=', 'created_at', $fechaCierre])
                ->with('producto')
                ->all();
            
            $cantidadProductos = 0;
            $valorTotal = 0;
            
            // Sumar valor de las entradas
            foreach ($entradas as $entrada) {
                $cantidadProductos += $entrada->cantidad;
                if ($entrada->producto) {
                    $valorTotal += ($entrada->cantidad * $entrada->producto->costo);
                }
            }
            
            // Restar valor de las salidas (solo aquellas con accion='SALIDA' y lugar_destino=NULL)
            $salidas = HistoricoMovimientos::find()
                ->where(['>=', 'created_at', $fechaInicio])
                ->where(['<=', 'created_at', $fechaCierre])
                ->andWhere(['accion' => HistoricoMovimientos::ACCION_SALIDA])
                ->andWhere(['id_lugar_destino' => null])
                ->with('producto')
                ->all();
            
            foreach ($salidas as $salida) {
                if ($salida->producto) {
                    $valorTotal -= ($salida->cantidad * $salida->producto->costo);
                }
            }
            
            return [
                'success' => true,
                'data' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_cierre' => $fechaCierre,
                    'cantidad_productos' => $cantidadProductos,
                    'valor' => round($valorTotal, 2)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al calcular los datos: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Registra un nuevo cierre de inventario
     * @return array JSON response
     */
    public function actionRegistrarCierre()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (!$this->request->isPost) {
            return [
                'success' => false,
                'message' => 'Método no permitido'
            ];
        }
        
        try {
            $data = $this->request->post();
            
            // Validar que se confirme el cierre
            if (empty($data['confirmar_cierre']) || $data['confirmar_cierre'] !== '1') {
                return [
                    'success' => false,
                    'message' => 'Debe confirmar que desea cerrar el inventario'
                ];
            }
            
            $model = new HistoricoInventarios();
            $model->fecha_inicio = $data['fecha_inicio'];
            $model->fecha_cierre = $data['fecha_cierre'];
            $model->cantidad_productos = $data['cantidad_productos'];
            $model->valor = $data['valor'];
            $model->nota = $data['nota'] ?? null;
            
            // Log de datos recibidos para debugging
            Yii::info('Datos del cierre de inventario: ' . json_encode([
                'fecha_inicio' => $model->fecha_inicio,
                'fecha_cierre' => $model->fecha_cierre,
                'cantidad_productos' => $model->cantidad_productos,
                'valor' => $model->valor,
                'nota' => $model->nota,
            ]), 'inventario');
            
            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Cierre de inventario registrado exitosamente',
                    'inventario_id' => $model->id
                ];
            } else {
                // Log de errores de validación
                Yii::error('Errores de validación al guardar cierre: ' . json_encode($model->errors), 'inventario');
                
                // Formatear errores para mostrar al usuario
                $errorMessages = [];
                foreach ($model->errors as $attribute => $errors) {
                    $errorMessages[] = $attribute . ': ' . implode(', ', $errors);
                }
                
                return [
                    'success' => false,
                    'message' => 'Error al guardar el cierre de inventario: ' . implode('; ', $errorMessages),
                    'errors' => $model->errors
                ];
            }
            
        } catch (\Exception $e) {
            Yii::error('Excepción al registrar cierre: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString(), 'inventario');
            return [
                'success' => false,
                'message' => 'Error al registrar el cierre: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Finds the HistoricoInventarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return HistoricoInventarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HistoricoInventarios::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
