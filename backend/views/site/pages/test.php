<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Начало работы - Обучающая система "Электронный лектор"';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать!</h1>

        <p class="lead">Автоматизированная обучающая система "Электронный лектро".</p>

        <p><a class="btn btn-lg btn-success" href="<?php Url::to(['site/pages', 'view' => 'test'])?>">Начало работы</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Создание учебного модуля</h2>

                <p>Модули состоят из страниц с заголовком, текстом и иллюстацией. Страница может содержать программируемый блок, обеспечивающий интерактивное отображение информации. Страница оканчивается тестом, обеспечивающим контроль усвоения материала.</p>

                <p><a class="btn btn-default" href="/backend/web/">Редактор модулей &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Освоение учебного модуля</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Анализ</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
