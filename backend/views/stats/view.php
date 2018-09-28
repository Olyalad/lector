<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ModuleExecution */

$this->title = 'Ответы пользователя: ' . $model->user->userFio;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['/module']];
$this->params['breadcrumbs'][] = ['label' => 'Статистика: ' . $model->module->name, 'url' => ['index', 'id' => $model->module->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="module-execution-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => $model->right ? 'bg-success' : 'bg-danger'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'exec_id',
            [
                'attribute' => 'page',
                'label' => 'Номер слайда',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    $page = $model->page;
                    return $page->numberPage + 1 . " ($page->name)";
                },
                'contentOptions' => ['style' => ['max-width' => '25%', 'white-space' => 'normal ']],
            ],
            [
                'attribute' => 'question_id',
                'value' => function ($model, $key, $index, $column) {
                    return $model->question->question;
                },
                'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
            ],
            [
                'attribute' => 'useranswer',
                'value' => function ($model, $key, $index, $column) {
                    return $model->userAnswer->answer;
                },
                'contentOptions' => ['style' => ['max-width' => '25%', 'white-space' => 'normal ']],
            ],
//            'right',
            'time:datetime',

        ],
    ]); ?>

</div>
