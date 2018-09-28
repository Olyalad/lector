<?php

use frontend\assets\AppAsset;
use frontend\assets\LibsAsset;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $id integer */
/* @var $slide common\models\Pages */
/* @var $pagination \yii\data\Pagination */


if ($model->js_code)
    $this->registerJs($model->js_code);
if ($model->css_code)
    $this->registerCss($model->css_code);
?>


<div class="row">
    <div class="col-sm-12">
        <?= $model->text; ?>
        <?php if ($model->image)
            echo Html::img($model->image, ['class' => 'img-responsive']); ?>
    </div>
</div>
