<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Answers */

$this->title = 'Редактирование ответа: ' . $model->answer;

$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['module/index']];
$this->params['breadcrumbs'][] = ['label' => $model->question->page->module->name, 'url' => ['module/update', 'id' => $model->question->page->module_id]];
$this->params['breadcrumbs'][] = ['label' => "Слайд: " . $model->question->page->name, 'url' => ['pages/update', 'id' => $model->question->page_id]];
$this->params['breadcrumbs'][] = ['label' => "Вопрос: " . $model->question->question, 'url' => ['questions/update', 'id' => $model->question_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="answers-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
<br>
