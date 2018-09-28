<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Answers;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Questions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="questions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'page_id'); ?>

    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <?php
    if (!$model->isNewRecord) {
        yii\widgets\Pjax::begin(['id' => 'right_answer']);
        $items = ArrayHelper::map(Answers::find()->where(['question_id' => $model->id])->all(), 'id', 'answer');
        $params = [
            'prompt' => 'Укажите правильный ответ'
        ];
        echo $form->field($model, 'answer_id')->dropDownList($items, $params);
        Pjax::end();
    } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?= Html::a('Назад', ['/pages/update', 'id' => $model->page_id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<div class="modal fade" id="answerEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование ответа</h4>
            </div>
            <div class="modal-body">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
