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
/* @var $execModel common\models\ModuleExecution */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модули', 'url' => ['modules']];
$this->params['breadcrumbs'][] = $this->title;
?>


<!-- конечная страница-->
<div class="jumbotron">
    <h1>Вы прошли модуль: <?= Html::encode($this->title) ?> </h1>
    <?php
    if ($execModel) {
        $count = $execModel->getWrongAnswers();

        $span = $count ?
            '<span class="text-danger">' . $count . '</span>' :
            '<span class="text-success">' . $count . '</span>';
        $pages = $count ? '<br>(Ошибки в вопросах: ' . $execModel->getPagesWithWrongAnswers() . ')' : '';

        echo "<p>Количество ошибок в модуле: " . $span . $pages . "</p>";

        echo "<p>" . Html::a('Назад к списку модулей', [Url::to(['modules'])], ['class' => 'btn btn-primary']) . "</p>";
    } else {
        echo "<p>" . Html::a('Назад к списку модулей', Yii::$app->urlManagerBackend->createUrl(['module/index']), ['class' => 'btn btn-primary']) . "</p>";
    } ?>
</div>
