<?php

use common\models\Module;
use common\models\User;
use himiklab\sortablegrid\SortableGridView;
use yii\helpers\ArrayHelper;
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

    <p>
        <?= Html::a('Создать модуль', ['create'], ['class' => 'btn btn-success']) ?>
    </p><br>
    <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Модули можно менять местами</div>
    <?= SortableGridView::widget([
        'sortableAction' => ['sortModule'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'creator',
                'value' => function ($model, $key, $index, $column) {
                    return $model->creator ? $model->creator->getUserFio() : '';
                }
            ],
            'allowName',
            'pagesCount',
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    /** @var Module $model */
                    /** @var \yii\grid\DataColumn $column */
                    $value = $model->{$column->attribute};
                    switch ($value) {
                        case Module::STATUS_ACTIVE:
                            $class = 'success';
                            break;
                        case Module::STATUS_NO_ACTIVE: //no break
                        default:
                            $class = 'default';
                    };
                    $html = Html::tag('span', Html::encode($model->getStatusName()), ['class' => 'label label-' . $class]);
                    return $value === null ? $column->grid->emptyCell : $html;
                },
                'filter' => Module::getStatuses(),
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {stat} {copy} {delete} {run}',
                'buttons' => [
                    'stat' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-stats"></span>', Url::to(['/stats/index', 'id' => $model->id]), ['title' => 'Статистика', 'aria-label' => 'Статистика', 'data-pjax' => 0]);
                    },
                    'run' => function ($url, $model) {
                        $pages = $model->pages;
                        if ($pages) {
                            return Html::a('<span class="glyphicon glyphicon-play"></span>', ['pages/run', 'id' => $pages[0]->id], [
                                'title' => 'Запустить тестовый просмотр',
                            ]);
                        }
                    },
                    'copy' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-copy"></span>', Url::to(['copy-module', 'id' => $model->id]), ['title' => 'Копировать модуль', 'aria-label' => 'Копировать модуль', 'data-pjax' => 0]);
                    }
                ],
            ],

        ],
    ]); ?>
</div>
