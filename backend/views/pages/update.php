<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Pages */

$this->title = 'Редактирование слайда: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['module/index']];
$this->params['breadcrumbs'][] = ['label' => $model->module->name, 'url' => ['module/update', 'id' => $model->module_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pages-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
<br>


<div class="questions">
    <?= GridView::widget([
        'dataProvider' => $questions,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'page_id',
            [
                'attribute' => 'question',
                'format' => 'html',
                'value' => function ($data) {
                    $code = '<ul>';
                    $code .= '<li>' . $data->question . '<ul>';
                    foreach ($data->answers as $answer) {
                        $code .= '<li>' . ($data->answer && $data->answer->id == $answer->id ? '<b>' : '') . $answer->answer . ($data->answer && $data->answer->id == $answer->id ? '<b>' : '') . '</li>';
                    }
                    $code .= '</ul></li>';

                    $code .= '</ul>';
                    return $code;
                },
                'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
                'enableSorting' => false,
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'Update'),]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['questions/delete', 'id' => $model['id']], [
                            'title' => Yii::t('app', 'Delete'), 'data-confirm' => Yii::t('app', 'Are you sure you want to delete this Record?'), 'data-method' => 'post']);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['questions/update', 'id' => $model['id']]);
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить вопрос', ['questions/create', 'page_id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
</div>


