<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Обучающая система "Электронный лектор"';
?>
<div class="site-index">

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

        <p><a class="btn btn-lg btn-success" href="<?=Url::to(['login'])?>">Вход для студентов</a></p>
        <p><a class="btn btn-lg btn-success" href="<?=Url::to(Yii::$app->urlManagerBackend->createUrl('/'))?>">Вход для преподавателей</a></p>

    </div>

</div>
