<?php

namespace app\controllers;

use app\models\Facturas;
use app\models\FacturasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FacturasController implements the CRUD actions for Facturas model.
 */
class FacturasController extends Controller
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
     * Lists all Facturas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FacturasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Facturas model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get invoice items with product information
        $items = $model->getItemsFacturas()
            ->with(['producto'])
            ->all();
        
        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Función helper para convertir entre monedas
        $convertCurrency = function($amount, $fromCurrency, $toCurrency) use ($precioParalelo, $precioOficial) {
            if (!$precioParalelo || !$precioOficial) {
                return $amount;
            }
            
            $value = floatval($amount);
            
            if ($fromCurrency === $toCurrency) {
                return $value;
            }
            
            // Convertir a VES
            $amountInVES = 0;
            if ($fromCurrency === 'USDT') {
                $amountInVES = $value * $precioParalelo->precio_ves;
            } elseif ($fromCurrency === 'BCV') {
                $amountInVES = $value * $precioOficial->precio_ves;
            } elseif ($fromCurrency === 'VES') {
                $amountInVES = $value;
            }
            
            // Convertir de VES a moneda destino
            if ($toCurrency === 'USDT') {
                return $amountInVES / $precioParalelo->precio_ves;
            } elseif ($toCurrency === 'BCV') {
                return $amountInVES / $precioOficial->precio_ves;
            } elseif ($toCurrency === 'VES') {
                return $amountInVES;
            }
            
            return $value;
        };
        
        // Calculate total paid - convertir cada cobro a la moneda de la factura
        $totalPagado = 0;
        $cobros = $model->getHistoricoCobros()->all();
        foreach ($cobros as $cobro) {
            $totalPagado += $convertCurrency($cobro->monto, $cobro->currency, $model->currency);
        }
        $totalPagado = round($totalPagado, 2);
        
        return $this->render('view', [
            'model' => $model,
            'items' => $items,
            'totalPagado' => $totalPagado,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Displays payment report for printing.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPaymentReport($id)
    {
        $model = $this->findModel($id);
        
        // Get invoice items with product information
        $items = $model->getItemsFacturas()
            ->with(['producto'])
            ->all();
        
        // Calculate total paid (sum of all cobros for this invoice)
        $totalPagado = $model->getHistoricoCobros()
            ->sum('monto');
        
        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Render without layout
        $this->layout = false;
        
        return $this->render('payment-report', [
            'model' => $model,
            'items' => $items,
            'totalPagado' => $totalPagado ?? 0,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Creates a new Facturas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Facturas();

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
     * Updates an existing Facturas model.
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
     * Deletes an existing Facturas model.
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
     * Finds the Facturas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Facturas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Facturas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
