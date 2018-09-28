<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход для преподавателей';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <div class="jumbotron">
        <div class="row flexbox-wrapper">
            <div class="col-md-2 col-md-offset-2">
                <img src="/images/logo.png" class="img-responsive img-logo">
            </div>
            <div class="col-md-6">
                <h3 class="text-left">Новосибирский государственный технический университет</h3>
            </div>
        </div>
        <h2>Автоматизированная обучающая система "Электронный лектор".</h2>
        <p class="lead">Для работы с системой необходимо ввести логин и пароль от личного кабинета сайта НГТУ</p>
    </div>


    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-sm-10',
                    'error' => '',
                    'hint' => '',
                ],
            ],
            'options' => [
                'class' => 'col-md-offset-4 col-md-4'
            ],
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
