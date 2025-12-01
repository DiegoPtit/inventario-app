<?php

namespace app\controllers;

use app\models\Salidas;
use app\models\SalidasSearch;
use app\models\Productos;
use app\models\Stock;
use app\models\Lugares;
use app\models\HistoricoMovimientos;
use app\models\HistoricoPreciosDolar;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SalidasController implements the CRUD actions for Salidas model.
 */
class SalidasController extends Controller
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
     * Lists all Salidas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SalidasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Salidas model.
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
     * Creates a new Salidas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Salidas();

        if ($this->request->isPost) {
            $postData = $this->request->post();
            
            if ($model->load($postData)) {
                // Validaciones adicionales antes de guardar
                $errors = [];
                
                if (empty($model->id_producto)) {
                    $errors[] = 'Debe seleccionar un producto';
                }
                
                if (empty($model->cantidad) || $model->cantidad <= 0) {
                    $errors[] = 'Debe ingresar una cantidad válida';
                }
                
                if ($model->is_movimiento == 1) {
                    // Es traspaso
                    if (empty($model->id_lugar_origen)) {
                        $errors[] = 'Debe seleccionar un lugar origen';
                    }
                    if (empty($model->id_lugar_destino)) {
                        $errors[] = 'Debe seleccionar un lugar destino';
                    }
                    if ($model->id_lugar_origen == $model->id_lugar_destino) {
                        $errors[] = 'El lugar origen y destino deben ser diferentes';
                    }
                }
                
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        \Yii::$app->session->addFlash('error', $error);
                    }
                } else {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        // Guardar la salida
                        if ($model->save()) {
                            // Actualizar stock según el tipo de salida
                            $this->actualizarStock($model);
                            
                            // Registrar en histórico de movimientos
                            $this->registrarHistorico($model);
                            
                            $transaction->commit();
                            \Yii::$app->session->setFlash('success', 'Salida registrada exitosamente');
                            return $this->redirect(['view', 'id' => $model->id]);
                        } else {
                            $transaction->rollBack();
                            foreach ($model->errors as $field => $fieldErrors) {
                                foreach ($fieldErrors as $error) {
                                    \Yii::$app->session->addFlash('error', $field . ': ' . $error);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        \Yii::$app->session->setFlash('error', 'Error al registrar la salida: ' . $e->getMessage());
                    }
                }
            } else {
                \Yii::$app->session->setFlash('error', 'Error al procesar los datos del formulario');
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Actualiza el stock según el tipo de salida
     * @param Salidas $model
     */
    private function actualizarStock($model)
    {
        if ($model->is_movimiento == 1) {
            // Es un traspaso: restar del lugar origen y sumar al lugar destino
            $this->actualizarStockPorLugar($model->id_producto, $model->id_lugar_origen, -$model->cantidad);
            $this->actualizarStockPorLugar($model->id_producto, $model->id_lugar_destino, $model->cantidad);
        } else {
            // Es descarte: restar del lugar específico seleccionado
            if ($model->id_lugar_origen) {
                // Si se especificó lugar origen, descartar de ahí
                $this->actualizarStockPorLugar($model->id_producto, $model->id_lugar_origen, -$model->cantidad);
            } else {
                // Fallback: restar del lugar con más stock
                $stockMaximo = Stock::find()
                    ->where(['id_producto' => $model->id_producto])
                    ->andWhere(['>', 'cantidad', 0])
                    ->orderBy(['cantidad' => SORT_DESC])
                    ->one();
                
                if ($stockMaximo) {
                    $this->actualizarStockPorLugar($model->id_producto, $stockMaximo->id_lugar, -$model->cantidad);
                }
            }
        }
    }

    /**
     * Actualiza la cantidad de stock en un lugar específico
     * @param int $idProducto
     * @param int $idLugar
     * @param int $cantidad (positiva para sumar, negativa para restar)
     */
    private function actualizarStockPorLugar($idProducto, $idLugar, $cantidad)
    {
        $stock = Stock::find()
            ->where(['id_producto' => $idProducto, 'id_lugar' => $idLugar])
            ->one();

        if ($stock) {
            $stock->cantidad += $cantidad;
            if ($stock->cantidad < 0) {
                $stock->cantidad = 0;
            }
            $stock->save();
        } elseif ($cantidad > 0) {
            // Crear nuevo registro de stock si no existe y la cantidad es positiva
            $nuevoStock = new Stock();
            $nuevoStock->id_producto = $idProducto;
            $nuevoStock->id_lugar = $idLugar;
            $nuevoStock->cantidad = $cantidad;
            $nuevoStock->save();
        }
    }

    /**
     * Registra el movimiento en el histórico
     * @param Salidas $model
     */
    private function registrarHistorico($model)
    {
        $historico = new HistoricoMovimientos();
        $historico->id_producto = $model->id_producto;
        $historico->accion = HistoricoMovimientos::ACCION_SALIDA;
        $historico->cantidad = $model->cantidad;
        $historico->referencia_id = $model->id;
        
        if ($model->is_movimiento == 1) {
            // Es traspaso
            $historico->id_lugar_origen = $model->id_lugar_origen;
            $historico->id_lugar_destino = $model->id_lugar_destino;
        } else {
            // Es descarte - registrar lugar origen específico donde se descartó
            $historico->id_lugar_origen = $model->id_lugar_origen;
            $historico->id_lugar_destino = null;
        }
        
        $historico->save();
    }

    /**
     * Updates an existing Salidas model.
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
     * Deletes an existing Salidas model.
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
     * Retorna productos que tienen stock disponible en formato JSON
     * Devuelve cada combinación producto-ubicación por separado
     * @return Response
     */
    public function actionProductosConStock()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Obtener todos los registros de stock con cantidad > 0
        $stocks = Stock::find()
            ->with(['producto', 'lugar'])
            ->where(['>', 'cantidad', 0])
            ->orderBy(['id_producto' => SORT_ASC, 'cantidad' => SORT_DESC])
            ->all();
        
        $result = [];
        foreach ($stocks as $stock) {
            if ($stock->producto && $stock->lugar) {
                $nombreProducto = trim(($stock->producto->marca ?: '') . ' ' . ($stock->producto->modelo ?: 'Sin modelo'));
                
                $result[] = [
                    'id' => $stock->id_producto,
                    'id_stock' => $stock->id,
                    'id_lugar' => $stock->id_lugar,
                    'cantidad' => $stock->cantidad,
                    'texto' => $nombreProducto . ' - ' . $stock->lugar->nombre . ' (' . $stock->cantidad . ' unid.)'
                ];
            }
        }
        
        return $result;
    }

    /**
     * Retorna información de stock para un producto específico
     * @param int $id ID del producto
     * @return Response
     */
    public function actionStockInfo($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!$id) {
            return [];
        }
        
        // Obtener todo el stock del producto en todas las ubicaciones, ordenado por cantidad DESC
        $stocks = Stock::find()
            ->with(['lugar'])
            ->where(['id_producto' => $id])
            ->andWhere(['>', 'cantidad', 0])
            ->orderBy(['cantidad' => SORT_DESC])
            ->all();
        
        $result = [];
        foreach ($stocks as $stock) {
            $result[] = [
                'id_lugar' => $stock->id_lugar,
                'lugar_nombre' => $stock->lugar ? $stock->lugar->nombre : 'Sin nombre',
                'cantidad' => (int)$stock->cantidad
            ];
        }
        
        return $result;
    }

    /**
     * Finds the Salidas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Salidas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Salidas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
