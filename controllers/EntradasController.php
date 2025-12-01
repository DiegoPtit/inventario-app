<?php

namespace app\controllers;

use app\models\Entradas;
use app\models\EntradasSearch;
use app\models\HistoricoMovimientos;
use app\models\Stock;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;

/**
 * EntradasController implements the CRUD actions for Entradas model.
 */
class EntradasController extends Controller
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
     * Lists all Entradas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EntradasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Entradas model.
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
     * Creates a new Entradas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Entradas();

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                if ($model->load($this->request->post())) {
                    // Manejar la subida del documento
                    $model->documentFile = UploadedFile::getInstance($model, 'documentFile');
                    if ($model->documentFile) {
                        $rutaDocumento = $model->uploadDocument();
                        if ($rutaDocumento) {
                            $model->ruta_documento_respaldo = $rutaDocumento;
                        }
                    }
                    
                    // Validar y guardar la entrada
                    if ($model->validate() && $model->save()) {
                        // 1. Registrar en HistoricoMovimientos
                        $historico = new HistoricoMovimientos();
                        $historico->id_producto = $model->id_producto;
                        $historico->setAccionToEntrada();
                        $historico->id_lugar_origen = null;
                        $historico->id_lugar_destino = $model->id_lugar;
                        $historico->cantidad = $model->cantidad;
                        $historico->referencia_id = $model->id;
                        
                        if (!$historico->save()) {
                            throw new \Exception('Error al guardar el histórico de movimientos');
                        }
                        
                        // 2. Actualizar o crear Stock
                        $stock = Stock::findOne([
                            'id_producto' => $model->id_producto,
                            'id_lugar' => $model->id_lugar
                        ]);
                        
                        if ($stock) {
                            // Actualizar stock existente
                            $stock->cantidad += $model->cantidad;
                            if (!$stock->save()) {
                                throw new \Exception('Error al actualizar el stock');
                            }
                        } else {
                            // Crear nuevo registro de stock
                            $stock = new Stock();
                            $stock->id_producto = $model->id_producto;
                            $stock->id_lugar = $model->id_lugar;
                            $stock->cantidad = $model->cantidad;
                            if (!$stock->save()) {
                                throw new \Exception('Error al crear el stock');
                            }
                        }
                        
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Entrada registrada exitosamente');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error al procesar la entrada: ' . $e->getMessage());
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Entradas model.
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
     * Deletes an existing Entradas model.
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
     * Finds the Entradas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Entradas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Entradas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
