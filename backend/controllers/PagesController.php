<?php

namespace backend\controllers;

use common\models\Module;
use Yii;
use common\models\Pages;
use common\models\PagesSearch;
use common\models\QuestionsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * PagesController implements the CRUD actions for Pages model.
 */
class PagesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['createModule'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Creates a new Pages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($module_id)
    {
        $model = new Pages();
        $model->module_id = $module_id;

        if ($model->load(Yii::$app->request->post()) &&
            $model->upload() &&
            $model->save()
        ) {
            $valueSave = Yii::$app->request->post('submit-type');
            $this->afterSaveRedirect($model, $valueSave);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Pages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchModel = new QuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where('page_id = ' . $id);


        if ($model->load(Yii::$app->request->post()) &&
            $model->upload() &&
            $model->save()
        ) {
            $valueSave = Yii::$app->request->post('submit-type');
            $this->afterSaveRedirect($model, $valueSave);
        } else {
            return $this->render('update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'questions' => $dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing Pages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $module_id = $model->module_id;
        $model->delete();

        return $this->redirect(['module/update', 'id' => $module_id]);
    }


    /**
     * Запуск модуля в тестовом режиме во фронтенде
     * @param $id int - id of Pages
     * @return \yii\web\Response
     */
    public function actionRun($id)
    {
        $model = $this->findModel($id);

        $page = $model->getNumberPage();

        return $this->redirect(Yii::$app->urlManagerFrontEnd->createUrl(['test',
            'id' => $model->module_id,
            'page' => $page + 1,
        ]));
    }


    /**
     * Finds the Pages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    /**
     * @param $model Pages model
     * @param $valueSave string
     * @return \yii\web\Response
     */
    protected function afterSaveRedirect($model, $valueSave)
    {
        switch ($valueSave) {
            case 'save-run':
                return $this->redirect(['run', 'id' => $model->id]);
                break;
            case 'save-create':
                return $this->redirect(['create','module_id' => $model->module_id]);
                break;
            default:
                Yii::$app->session->setFlash('success', 'Сохранено');
                return $this->redirect(['update', 'id' => $model->id]);
                break;
        }
    }

}
