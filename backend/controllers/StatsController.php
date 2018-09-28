<?php

namespace backend\controllers;

use common\models\ModuleExecution;
use common\models\ModuleExecutionAnswersSearch;
use common\models\ModuleExecutionSearch;
use Yii;
use common\models\Module;
use common\models\ModuleSearch;
use common\models\Pages;
use common\models\PagesSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use himiklab\sortablegrid\SortableGridAction;

/**
 * ModuleController implements the CRUD actions for Module model.
 */
class StatsController extends Controller
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
     * Статистика по модулю.
     * Отображаются кто-когда проходил, затраченное время, кол-во неправильных ответов
     * @param $id_module
     * @return mixed
     */
    public function actionIndex($id)
    {
        $model = $this->findModule($id);

        $searchModel = new ModuleExecutionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('stats', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single ModuleExecution model.
     * @param integer $id
     * @return mixed
     */
    public function actionAnswers($id)
    {
        $model = $this->findExec($id);

        $searchModel = new ModuleExecutionAnswersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Deletes an existing Module model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findExec($id);
        $id = $model->module_id;
        $model->delete();

        return $this->redirect(['index', 'id' => $id]);
    }



    /**
     * Очистка результатов прохождения модуля
     * @param $module_id
     * @return \yii\web\Response
     */
    public function actionClear($module_id)
    {
        $model = $this->findModule($module_id);
        if ($execs = $model->executions) {
            foreach ($execs as $exec)
                $exec->delete();
        }

        return $this->redirect(['index', 'id' => $module_id]);
    }



    /**
     * Finds the Module model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Module the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModule($id)
    {
        if (($model = Module::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the ModuleExecution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModuleExecution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findExec($id)
    {
        if (($model = ModuleExecution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
