<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Модули';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->complete())
                return ['class' => 'bg-success'];
        },
        'tableOptions' => [
            'class' => 'table table-bordered'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-play"></span>', Url::to(['start', 'id' => $model->id]));
                    },
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->can('createModule')) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManagerBackend->createUrl(['/module/update',
                                'id' => $model->id,
                            ]));
                        }
                    }
                ],
            ],
        ],
    ]); ?>
</div>
