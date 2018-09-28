<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.04.2017
 * Time: 12:31
 */
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Module */


$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['modules']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- стартовая страница-->
<div class="jumbotron">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Начать', [Url::to(['start', 'id' => $model->id, 'start' => true])], ['class' => 'btn btn-success']); ?></p>
</div>
