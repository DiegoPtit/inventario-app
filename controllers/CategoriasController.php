<?php

namespace app\controllers;

use app\models\Categorias;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * CategoriasController implements the CRUD actions for Categorias model.
 */
class CategoriasController extends Controller
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
     * Creates a new Categorias model via AJAX.
     * @return \yii\web\Response JSON response
     */
    public function actionCreateAjax()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new Categorias();
        
        if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
            return [
                'success' => true,
                'categoria' => [
                    'id' => $model->id,
                    'titulo' => $model->titulo,
                    'descripcion' => $model->descripcion,
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al guardar la categoría',
            'errors' => $model->errors
        ];
    }

    /**
     * Finds the Categorias model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Categorias the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Categorias::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
