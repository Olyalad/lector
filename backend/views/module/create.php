<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Module */

$this->title = 'Создание модуля';
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
