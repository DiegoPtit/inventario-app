<?php

namespace app\controllers;

use app\models\Clientes;
use app\models\ClientesSearch;
use app\models\HistoricoPreciosDolar;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientesController implements the CRUD actions for Clientes model.
 */
class ClientesController extends Controller
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
     * Lists all Clientes models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientesSearch();
        
        // Manejar búsqueda general
        $queryParams = $this->request->queryParams;
        if (isset($queryParams['search']) && !empty($queryParams['search'])) {
            $queryParams['ClientesSearch']['search'] = $queryParams['search'];
        }
        
        $dataProvider = $searchModel->search($queryParams);
        
        // Obtener valores únicos para los filtros
        $ubicaciones = Clientes::find()
            ->select('ubicacion')
            ->distinct()
            ->where(['IS NOT', 'ubicacion', null])
            ->andWhere(['<>', 'ubicacion', ''])
            ->orderBy(['ubicacion' => SORT_ASC])
            ->column();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    /**
     * Displays a single Clientes model.
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
     * Creates a new Clientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Clientes();

        if ($this->request->isPost) {
            // Si es una petición AJAX, manejar de forma diferente
            if ($this->request->isAjax) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                
                $data = $this->request->post();
                $model->nombre = $data['nombre'] ?? '';
                $model->documento_identidad = $data['documento_identidad'] ?? null;
                $model->edad = $data['edad'] ?? null;
                $model->telefono = $data['telefono'] ?? null;
                $model->ubicacion = $data['ubicacion'] ?? null;
                $model->status = $data['status'] ?? 'Solvente';
                
                if ($model->save()) {
                    return [
                        'success' => true,
                        'message' => 'Cliente registrado exitosamente',
                        'cliente_id' => $model->id
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al guardar el cliente',
                        'errors' => $model->errors
                    ];
                }
            }
            
            // Si no es AJAX, procesar normalmente
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
     * Updates an existing Clientes model.
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
     * Deletes an existing Clientes model.
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
     * Finds the Clientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Clientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clientes::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreateAjax()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new Clientes();
        
        $data = $this->request->post();
        $model->nombre = $data['nombre'] ?? '';
        $model->documento_identidad = $data['documento_identidad'] ?? null;
        $model->edad = $data['edad'] ?? null;
        $model->telefono = $data['telefono'] ?? null;
        $model->ubicacion = $data['ubicacion'] ?? null;
        $model->status = $data['status'] ?? 'Solvente';
        
        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Cliente registrado exitosamente',
                'cliente_id' => $model->id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al guardar el cliente: ' . implode(', ', $model->getFirstErrors()),
                'errors' => $model->errors
            ];
        }
    }
}
