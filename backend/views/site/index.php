<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Обучающая система "Электронный лектор"';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать</h1>

        <p class="lead">Автоматизированная обучающая система "Электронный лектор".</p>

        <p><a class="btn btn-lg btn-success" href="<?=Url::to(['module/index'])?>">Начало работы</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h2>Создание учебного модуля</h2>

                <p>Модули состоят из страниц с заголовком, текстом и иллюстацией. Страница может содержать программируемый блок, обеспечивающий интерактивное отображение информации. Страница оканчивается тестом, обеспечивающим контроль усвоения материала.</p>

                <p><a class="btn btn-default" href="<?=Url::to(['module/index'])?>">Редактор модулей &raquo;</a></p>
            </div>
            <div class="col-lg-6">
                <h2>Освоение учебного модуля</h2>

                <p>Освоение учебных модулей позволяет изучить теоретический материал с текушим контролем усвоения материала и итоговым тестом по модулю.</p>

                <p><a class="btn btn-default" href="<?=Yii::$app->urlManagerFrontEnd->createUrl(['modules'])?>">Начать обучение &raquo;</a></p>
            </div>

        </div>

    </div>
</div>
