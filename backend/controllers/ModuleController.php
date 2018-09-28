<?php

namespace backend\controllers;

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
class ModuleController extends Controller
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => Pages::className(),
            ],
            'sortModule' => [
                'class' => SortableGridAction::className(),
                'modelName' => Module::className(),
            ],
        ];
    }

    /**
     * Lists all Module models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Module model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Module();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Сохранено');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Module model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchModel = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where('module_id = ' . $id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save())
                Yii::$app->session->setFlash('success', 'Сохранено');
        }


        return $this->render('update', [
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Module model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Module the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Module::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Копирование слайдов в модуль
     * @param $id
     * @return mixed|\yii\web\Response
     */
    public function actionCopy($id)
    {
        $modelModule = $this->findModel($id);

        $searchModel = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            if ($selection = Yii::$app->request->post('selection'))
                Pages::copyPages($id, $selection);
            return $this->redirect(['update', 'id' => $modelModule->id]);
        }

        return $this->render('copy', [
            'model' => $modelModule,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Копировать модуль
     * @param $id
     * @return mixed|\yii\web\Response
     */
    public function actionCopyModule($id)
    {
        $baseModel = $this->findModel($id);

        $newModel = new Module();
        $newModel->attributes = [
            'name' => $baseModel->name,
            'allow' => $baseModel->allow,
            'status' => Module::STATUS_NO_ACTIVE,
        ];

        if ($newModel->save()) {
            Pages::copyPages($newModel->id, $baseModel->getPages()->select('id')->asArray()->all());
            return $this->redirect(['index']);
        } else
            print_r($newModel->errors);

    }
}
