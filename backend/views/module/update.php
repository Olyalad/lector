<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use himiklab\sortablegrid\SortableGridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Module */

$this->title = 'Редактирование модуля: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
<br><br>

<div class="slides">
    <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Слайды можно менять местами</div>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить слайд', ['pages/create', 'module_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-copy"></span> Скопировать слайд', ['copy', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
    </p>

    <?php if (isset($dataProvider))
        echo SortableGridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

//            'id',
                [
                    'attribute' => 'name',
                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'text',
                    'format' => 'html',
                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => function ($data) {
                        if ($data->image)
                            return Html::img($data->imageurl, ['width' => '200px']);
                        else return '';
                    },
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'questions',
                    'format' => 'html',
                    'value' => function ($data) {
                        $code = '<ul>';
                        foreach ($data->questions as $question) {
                            $code .= '<li>' . $question->question . '<ul>';
                            foreach ($question->answers as $answer) {
                                $code .= '<li>' . ($question->answer && $question->answer->id == $answer->id ? '<b>' : '') . $answer->answer . ($question->answer && $question->answer->id == $answer->id ? '<b>' : '') . '</li>';
                            }
                            $code .= '</ul></li>';
                        };
                        $code .= '</ul>';
                        return $code;
                    },
                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete} {run}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('app', 'Update'),]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['pages/delete', 'id' => $model['id']], [
                                'title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this Record?'), 'data-method' => 'post']);
                        },
                        'run' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-play"></span>', ['pages/run', 'id' => $model->id], [
                                'title' => 'Запустить с этой страницы',
                            ]);
                        },
                    ],

                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url = Url::to(['pages/update', 'id' => $model['id']]);
                            return $url;
                        }
                    }

                ],
            ],
        ]); ?>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить слайд', ['pages/create', 'module_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-copy"></span> Скопировать слайд', ['copy', 'id' => $model->id], ['class' => 'btn btn-info']) ?>

        <?= Html::a('Назад', ['/module/index'], ['class' => 'btn btn-danger pull-right']) ?>
    </p>
</div>



