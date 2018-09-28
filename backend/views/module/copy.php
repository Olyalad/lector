<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.04.2017
 * Time: 14:24
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Module */

$this->title = 'Копирование страниц в модуль';
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['module/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="slides">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
<!--    --><?//= Html::activeHiddenInput($model, 'id'); ?>

    <?php if (isset($dataProvider))
        echo GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],
                ['class' => 'yii\grid\SerialColumn'],

//            'id',
                ['attribute' => 'name',
                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
                ],
                [
                    'attribute' => 'text',
                    'format' => 'html',
                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
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
//                [
//                    'attribute' => 'questions',
//                    'format' => 'html',
//                    'value' => function ($data) {
//                        $code = '<ul>';
//                        foreach ($data->questions as $question) {
//                            $code .= '<li>' . $question->question . '<ul>';
//                            foreach ($question->answers as $answer) {
//                                $code .= '<li>' . ($question->answer && $question->answer->id == $answer->id ? '<b>' : '') . $answer->answer . ($question->answer && $question->answer->id == $answer->id ? '<b>' : '') . '</li>';
//                            }
//                            $code .= '</ul></li>';
//                        };
//                        $code .= '</ul>';
//                        return $code;
//                    },
//                    'contentOptions' => ['style' => ['max-width' => '30%', 'white-space' => 'normal ']],
//                ],
            ],
        ]); ?>

    <p>
        <?= Html::submitButton('Скопировать', ['class' => 'btn btn-success']) ?>
    </p>

    <?php ActiveForm::end(); ?>

</div>
