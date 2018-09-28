<?php

use common\models\Groups;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Module */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$js = <<<JS

function showGroups() {
    if ($("input#module-allow").prop('checked')) {
        $("div.field-module-groupsid").hide();
    } else {
         $("div.field-module-groupsid").show();
    }
}

$("input#module-allow").change(function () {
    showGroups();
});
showGroups();

JS;

$this->registerJs($js);
?>


<div class="module-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'status')->checkbox() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'allow')->checkbox() ?>


    <?php

    $items = ArrayHelper::map(Groups::find()->all(), 'id', 'name');
    $selected = [];
    if ($all = $model->groups) {
        foreach ($all as $grp) {
            $selected[$grp->id] = ['selected' => true];
        }
    }
    $params = [
        'multiple' => true,
        'options' => $selected,
    ];
    ?>
    <?= $form->field($model, 'groupsId')->widget(Select2::className(), [
        'data' => $items,
        'language' => 'ru',
        'options' => $params,
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Cоздать' : 'Cохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?= Html::a('Назад', ['/module/index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
