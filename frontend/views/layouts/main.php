<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
//        'brandLabel' => Html::img('/images/logo.png', ['width' => '60px']),
//        'brandUrl' => Url::to('/'),
        'options' => [
            'class' => 'navbar-nstu navbar-fixed-top',
        ],
    ]);
    $menuItems = [];
    if (!Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Модули', 'url' => ['/modules']];
        if (Yii::$app->user->can('createModule')) {
            $menuItems[] = ['label' => 'Редактирование модулей', 'url' => '/backend'];
        }
    } else {
        $menuItems[] = ['label' => 'Главная', 'url' => ['/site/index']];
    }
    $menuItems[] = ['label' => 'О системе. Контактная информация', 'url' => ['/site/about']];

    if (!Yii::$app->user->isGuest) {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->getUserFio() . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            &copy; <?= Html::a("Новосибирский государственный технический университет", 'http://nstu.ru') ?><br>
            &copy; <?= Html::a("Кафедра электротехнических комплексов", 'http://ciu.nstu.ru/kaf/etk/') ?>,
            2016-<?= date('Y') ?>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
