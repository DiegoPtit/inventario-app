<?php

namespace app\controllers;

use app\models\HistoricoCobros;
use app\models\HistoricoCobrosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HistoricoCobrosController implements the CRUD actions for HistoricoCobros model.
 */
class HistoricoCobrosController extends Controller
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
     * Lists all HistoricoCobros models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new HistoricoCobrosSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HistoricoCobros model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HistoricoCobros model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new HistoricoCobros();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Capturar currency del POST
                $currency = \Yii::$app->request->post('currency', 'USDT');
                
                // Asignar currency usando métodos del modelo
                if ($currency === 'BCV') {
                    $model->setCurrencyToBCV();
                } elseif ($currency === 'VES') {
                    $model->setCurrencyToVES();
                } else {
                    $model->setCurrencyToUSDT();
                }
                
                if ($model->save()) {
                    // Después de guardar el cobro, evaluar y actualizar el status del cliente
                    if ($model->cliente) {
                        $model->cliente->evaluarYActualizarStatus();
                    }
                    
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return $this->render('create', [
            'model' => $model,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Updates an existing HistoricoCobros model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // Después de actualizar el cobro, evaluar y actualizar el status del cliente
            if ($model->cliente) {
                $model->cliente->evaluarYActualizarStatus();
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return $this->render('update', [
            'model' => $model,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Deletes an existing HistoricoCobros model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $cliente = $model->cliente; // Guardar referencia al cliente antes de eliminar
        
        $model->delete();

        // Después de eliminar el cobro, evaluar y actualizar el status del cliente
        if ($cliente) {
            $cliente->evaluarYActualizarStatus();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the HistoricoCobros model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return HistoricoCobros the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HistoricoCobros::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
