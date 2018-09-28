<?php

namespace frontend\controllers;

use common\models\Pages;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\ContactForm;
use common\models\Module;
use common\models\ModuleSearch;
use common\models\ModuleExecution;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['index', 'error', 'login', 'about'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['modules', 'view', 'start', 'finish', 'show-frame'],
                        'allow' => true,
                        'roles' => ['viewModule'],
                    ],
                    [
                        'actions' => ['test'],
                        'allow' => true,
                        'roles' => ['createModule'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->render('index');
        return $this->redirect(['modules']);
    }

    /**
     * Lists all Module models.
     * @return mixed
     */
    public function actionModules()
    {
        $searchModel = new ModuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true, true);

        return $this->render('modules', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Просмотр страниц модуля
     * @param integer $id id of ModuleExecution
     * @param integer $page int
     * @return mixed
     */
    public function actionView($id, $page = 1, $question = false)
    {
        $modelExec = $this->findModelExecution($id);
        $modelModule = $modelExec->module;

        $slide = $modelModule->pages[$page - 1];

        if ($question) {
            if (!$slide->questions) {
                if ($page == $modelModule->getPagesCount())
                    return $this->redirect(['finish', 'id' => $id]);
                return $this->redirect(['view', 'id' => $id, 'page' => $page + 1]);
            }
        }

        if (Yii::$app->request->isPost) {
            if (isset(Yii::$app->request->post('Questions')['useranswer'])) {

                $questionId = Yii::$app->request->post('Questions')['id'];
                $useranswer = Yii::$app->request->post('Questions')['useranswer'];

                if ($useranswer != '') {

                    if ($modelExec->setAnswer($questionId, $useranswer)) {
                        Yii::$app->session->setFlash('success', 'Правильный ответ');

                        //если это был последний вопрос и ответ правильный
                        if ($page == $modelModule->getPagesCount())
                            return $this->redirect(['finish', 'id' => $id]);
                        else
                            return $this->redirect(['view', 'id' => $id, 'page' => $page + 1]);
                    } else {
                        Yii::$app->session->setFlash('error', 'Неправильный ответ');
                        $question = false;
                    }
                } else {
                    Yii::$app->session->setFlash('info', 'Вы не дали ответ на вопрос');
                }
            }
        }


        return $this->render('module/view', [
            'id' => $id,
            'modelExec' => $modelExec,
            'model' => $modelModule,
            'question' => $question,
            'slide' => $slide,
        ]);

    }


    /**
     * Tестовый просмотр для создателей модуля
     * Проверка ответов отключена!
     * @param integer $id id of Module
     * @param integer $page int
     * @param integer $id id of Pages
     * @return mixed
     */
    public function actionTest($id, $page = 1, $question = false)
    {
        //тестовый просмотр для создателей модуля
        $modelModule = $this->findModel($id);

        $slide = $modelModule->pages[$page - 1];

        if ($question) {
            if (!$slide->questions) {
                if ($page == $modelModule->getPagesCount())
                    return $this->redirect(['finish', 'id' => $id, 'test' => true]);
                return $this->redirect(['test', 'id' => $id, 'page' => $page + 1]);
            }
        }

        if (Yii::$app->request->isPost) {
            //если это был последний вопрос
            if ($page == $modelModule->getPagesCount())
                return $this->redirect(['finish', 'id' => $id, 'test' => true]);
            return $this->redirect(['test', 'id' => $id, 'page' => $page + 1]);
        }


        return $this->render('module/view', [
            'id' => $id,
            'modelExec' => false,
            'model' => $modelModule,
            'question' => $question,
            'slide' => $slide,
        ]);

    }


    /**
     * Старт модуля
     * @param $id int Module
     * @param bool $start
     * @return string|\yii\web\Response
     */
    public function actionStart($id, $start = false)
    {
        $modelModule = $this->findModel($id);

        if ($start) {
            $exec = new ModuleExecution();
            $exec->module_id = $modelModule->id;
            if ($exec->save())
                return $this->redirect(['view', 'id' => $exec->id]);
        }

        return $this->render('module/start', [
            'model' => $modelModule,
        ]);
    }


    /**
     * Модуль пройден
     * @param $id int ModuleExecution|Module
     * @return mixed
     */
    public function actionFinish($id, $test = false)
    {
        if (!$test) {

            $exec = $this->findModelExecution($id);

            // проверка, что студент ответил на все вопросы
            $check = $exec->checkAllDone();

            if ($check !== true) {
                Yii::$app->session->setFlash('error', 'Вы не ответили на все вопросы');
                return $this->redirect(['view', 'id' => $id, 'page' => $check + 1]);
            }

            $exec->finish = time();
            $exec->save();

            $model = $exec->module;
        } else
            $model = Module::findOne($id);


        return $this->render('module/finish', [
            'model' => $model,
            'execModel' => isset($exec) ? $exec : null,
        ]);
    }


    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!Yii::$app->openAm->authenticate()) {
            throw new Exception('Произошла ошибка в авторизации', 500);
        }


        if (Yii::$app->openAm->loginUser()) {
            return $this->goBack();
        }
        return $this->goHome();


//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->openAm->logout();

        return $this->goHome();
    }


    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
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
     * Finds the ModuleExecution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModuleExecution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelExecution($id)
    {
        if (($model = ModuleExecution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * @param $id integer Pages
     * @return mixed
     */
    public function actionShowFrame($id)
    {
        $this->layout = 'frame';
        $model = Pages::findOne($id);

        return $this->render('module/_frame', [
            'model' => $model,
        ]);
    }

}
