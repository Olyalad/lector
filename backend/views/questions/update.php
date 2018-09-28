<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Questions */

$this->title = 'Редактирование вопроса: ' . $model->question;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['module/index']];
$this->params['breadcrumbs'][] = ['label' => $model->page->module->name, 'url' => ['module/update', 'id' => $model->page->module->id]];
$this->params['breadcrumbs'][] = ['label' => "Слайд: " . $model->page->name, 'url' => ['pages/update', 'id' => $model->page->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="questions-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

    <br>
    <h2>Добавление ответов</h2>

    <div class="answers-index">
        <?= $this->render('_form_answers', [
            'model' => $modelAnswer,
        ]) ?>

        <?php Pjax::begin(['id' => 'answers']) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

//      'id',
//      'question_id',
                'answer',

                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('app', 'Update'),
                                'class' => 'answer-update',
                                'data-pjax' => 0,
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['answers/delete-ajax', 'id' => $model['id']], [
                                'title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this Record?'), 'data-method' => 'post']);
                        }
                    ],

                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url = Url::To(['answers/update', 'id' => $model['id']]);
                            return $url;
                        }
                    }

                ],
            ],
        ]); ?>

        <?php Pjax::end() ?>
    </div>
