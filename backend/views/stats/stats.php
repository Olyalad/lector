<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ModuleExecutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['/module']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="module-execution-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Очистить все результаты', ['clear', 'module_id' => $model->id], [
        'class' => 'btn btn-danger pull-right',
        'data-confirm' => 'Вы уверены, что хотите удалить все результаты?',
    ]) ?>

   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'module_id',
            [
                'attribute' => 'user',
                'value' => function ($model, $key, $index, $column) {
                    return $model->user->getUserFio();
                }
            ],
            [
                'attribute' => 'user_group',
                'value' => function ($model, $key, $index, $column) {
                    if ($group = $model->user->group)
                        return $group->name;
                    return '';
                }
            ],
            'finish:datetime',

            'timeSpent',
            'wrongAnswers',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{answers} {delete}',
                'buttons' => [
                    'answers' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/stats/answers', 'id' => $model->id]));
                    },
                ]
            ],

        ],
    ]); ?>
   </div>
