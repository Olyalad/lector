<?php

use trntv\aceeditor\AceEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Pages */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="pages-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= Html::activeHiddenInput($model, 'module_id'); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->widget(CKEditor::className(), [
        'options' => [
            'rows' => 10,
        ],
        'preset' => 'custom',
        'clientOptions' => [
            'allowedContent' => true,
            'enterMode' => 2,
            'shiftEnterMode' => 1,
            'forcePasteAsPlainText' => true,
            'language' => 'ru',
            'height' => 400,
            'extraPlugins' => 'colorbutton,colordialog,find,justify,tableresize,tabletools,mathjax',
            'toolbarGroups' => [
                ['name' => 'document', 'groups' => ['mode', 'document', 'doctools']], //источник
                ['name' => 'clipboard', 'groups' => ['clipboard', 'undo']],
                ['name' => 'editing', 'groups' => ['find', 'selection', 'spellchecker']], //проверка правописания
                '/',
                ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                ['name' => 'paragraph', 'groups' => ['templates', 'list', 'indent', 'align', 'blocks' ]],
                '/',
                ['name' => 'links'],
                ['name' => 'insert'],
                '/',
                ['name' => 'styles'],
                ['name' => 'colors', 'groups' => ['colorbutton']],
                ['name' => 'forms'],
                ['name' => 'others', 'groups' => ['tools']],
            ],
            'mathJaxLib' => '//cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?config=TeX-AMS_HTML',
            'removeButtons' => '',
            'removePlugins' => '',
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'css_code')->widget(AceEditor::className(), [
                'mode' => 'css',
                'theme' => 'textmate', // crimson_editor dreamweaver eclipse github tomorrow textmate
                'containerOptions' => [
                    'style' => 'width: 100%; min-height: 300px;'
                ]
            ]); ?>
        </div>
        <div class="col-md-6">

            <?= $form->field($model, 'js_code')->widget(AceEditor::className(), [
                'mode' => 'javascript',
                'theme' => 'textmate',
                'containerOptions' => [
                    'style' => 'width: 100%; min-height: 300px;'
                ]
            ]); ?>
        </div>

    </div>


    <?= $form->field($model, 'imageFile')->widget(FileInput::className(), ['options' => ['accept' => 'image/*'],
        'language' => 'ru',
        'pluginOptions' => ['initialPreview' => [$model->image ? $model->image : NULL],
            'initialPreviewAsData' => true,
            'overwriteInitial' => true,
            'showUpload' => false,],
        'pluginEvents' => ['fileclear' => "function() { $('#pages-image').val(null); }",],]); ?>

    <?= $form->field($model, 'image')->hiddenInput()->label(false)->error(false); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?= Html::submitButton('Сохранить и вставить новый слайд', ['class' => 'btn btn-success', 'name' => 'submit-type', 'value' => 'save-create',]) ?>

        <?= Html::a('Назад', ['/module/update', 'id' => $model->module_id], ['class' => 'btn btn-danger']) ?>

        <?= Html::submitButton('<span class="glyphicon glyphicon-forward"></span> Сохранить и запустить', ['class' => 'btn btn-info pull-right', 'name' => 'submit-type', 'value' => 'save-run',]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
