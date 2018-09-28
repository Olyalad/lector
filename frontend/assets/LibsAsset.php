<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LibsAsset extends AssetBundle
{
    public $basePath = '@webroot/libs';
    public $baseUrl = '@web/libs';
    public $css = [
        'chartist/chartist.min.css',


    ];
    public $js = [
        'd3/d3.min.js',
        'https://www.gstatic.com/charts/loader.js',
        'chartist/chartist.min.js',
        'https://code.highcharts.com/highcharts.src.js',
        'flot/jquery.flot.min.js',
        'flot/jquery.flot.axislabels.js',

    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}