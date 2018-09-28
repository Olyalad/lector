<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\LibsAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

LibsAsset::register($this);

$this->registerCss('
body {
    overflow-x: hidden !important;
    height: auto !important;
}')

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--    <?//= Html::csrfMetaTags() ?>-->
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
<!--    <div class="container">-->
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
<!--    </div>-->
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
