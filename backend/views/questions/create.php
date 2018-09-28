<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Questions */

$this->title = 'Добавление вопроса';
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['module/index']];
$this->params['breadcrumbs'][] = ['label' => $model->page->module->name, 'url' => ['module/update', 'id' => $model->page->module->id]];
$this->params['breadcrumbs'][] = ['label' => "Слайд: " . $model->page->name, 'url' => ['pages/update', 'id' => $model->page->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questions-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
