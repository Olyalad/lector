<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Pages */

$this->title = 'Создание слайда';
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['module/index']];
$this->params['breadcrumbs'][] = ['label' => $model->module->name, 'url' => ['module/update', 'id' => $model->module_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
