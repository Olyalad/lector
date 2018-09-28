<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\Pagination;
use frontend\widgets\LinkPager;
//use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $id integer */
/* @var $modelExec common\models\ModuleExecution|false */
/* @var $model common\models\Module */
/* @var $slide common\models\Pages */
/* @var $question bool */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['modules']];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="module-view">
    <?php
//    Pjax::begin();
    $pagination = new Pagination([
        'totalCount' => $model->getPagesCount(),
        'pageSize' => 1,
        'params' => [
            'id' => $id,
            'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1,
        ],
    ]);
    echo LinkPager::widget([
        'pagination' => $pagination,
        'maxButtonCount' => $pagination->totalCount,
        'resultTest' => $modelExec ? $modelExec->getUserAnswers() : [],
        'disabledLink' => $modelExec ? true : false,
    ]); ?>

    <h1><?= Html::encode($slide->name) ?></h1>

    <?php

    if (!$question) {
        // Текст слайда
        echo $this->render('_slide', [
            'id' => $id,
            'slide' => $slide,
            'pagination' => $pagination,
        ]);
    } else {  // Вопрос

        //если вопросы к слайду есть
        if ($slide->questions) {
            echo $this->render('_question', [
                'id' => $id,
                'slide' => $slide,
                'pagination' => $pagination,
            ]);
        }
    }

//    Pjax::end();

    if (Yii::$app->controller->action->id == 'test') {
        echo Html::a('Назад к списку модулей', Yii::$app->urlManagerBackend->createUrl(['module/index']), ['class' => 'btn btn-danger pull-right']);

        echo Html::a('Добавить вопрос к странице', Yii::$app->urlManagerBackend->createUrl(['questions/create',
            'page_id' => $slide->id]), ['class' => 'btn btn-success pull-right']);
    }
    ?>
</div>
