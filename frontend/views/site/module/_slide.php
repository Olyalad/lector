<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.04.2017
 * Time: 14:29
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $id integer */
/* @var $slide common\models\Pages */
/* @var $pagination \yii\data\Pagination */


$js = <<<JS
$(function() {
    $('#ourframe').load(function() {
        $(this).height($(this).contents().find('body').height());
    });
});
JS;
$this->registerJs($js);



?>

    <div class="row">
        <?php if ($slide->js_code || $slide->css_code) { ?>

            <div class="col-sm-12">
                <iframe id="ourframe" src='<?= Url::to(['show-frame', 'id' => $slide->id]) ?>'
                        width='100%'
                        height="500px"
                        frameborder='0'
                        allowfullscreen></iframe>
            </div>

        <?php } else { ?>

            <div class="col-sm-12">
                <?= $slide->text; ?>
                <?php if ($slide->image)
                    echo Html::img($slide->image, ['class' => 'img-responsive']); ?>
            </div>

        <?php } ?>
    </div>


<?php
echo Html::a('Продолжить', [
    Url::to([
        Yii::$app->controller->action->id,
        'id' => $id,
        'page' => $pagination->page + 1,
        'question' => true
    ])], ['class' => 'btn btn-primary']);


if (Yii::$app->controller->action->id == 'test') {
    echo Html::a('Редактировать этот слайд',
        Yii::$app->urlManagerBackend->createUrl(['pages/update',
            'id' => $slide->id,
        ]), ['class' => 'btn btn-info pull-right']);
}

//$this->registerJsFile('//cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?config=TeX-AMS_HTML');
?>