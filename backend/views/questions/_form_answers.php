<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Answers */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$js = <<<JS
$("#new_answer").on("pjax:end", function () {
    $.pjax.reload({container: "#answers"}).done(function () {
        $.pjax.reload({container: "#right_answer"});
    });
});
JS;
$this->registerJs($js);
?>

<div class="answers-form">
    <?php yii\widgets\Pjax::begin(['id' => 'new_answer']) ?>
    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true, 'id' => 'form-answers-update']]); ?>

    <?= Html::activeHiddenInput($model, 'question_id'); ?>

    <?= $form->field($model, 'answer')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
