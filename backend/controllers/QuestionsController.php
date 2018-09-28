<?php

namespace backend\controllers;

use common\models\Answers;
use Yii;
use common\models\Questions;
use common\models\QuestionsSearch;
use common\models\AnswersSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuestionsController implements the CRUD actions for Questions model.
 */
class QuestionsController extends Controller
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
                        'allow' =>true,
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
     * Creates a new Questions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($page_id)
    {
        $model = new Questions();
        $model->page_id = $page_id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save())
                return $this->redirect(['questions/update', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Questions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        $modelAnswer = new Answers();
        $modelAnswer->question_id = $id;
        if ($modelAnswer->load(Yii::$app->request->post()) && $modelAnswer->save()) {
            $modelAnswer = new Answers();
            $modelAnswer->question_id = $id;
        }

        $searchModel = new AnswersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where('question_id = ' . $id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['pages/update', 'id' => $model->page_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelAnswer' => $modelAnswer,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing Questions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['pages/update', 'id' => $model->page->id]);
    }

    /**
     * Finds the Questions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Questions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Questions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
