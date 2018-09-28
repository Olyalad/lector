<?php

namespace common\components;


use yii\base\Model;

class OpenAmConfig extends Model
{
    // default openam configurations
    public $name = 'default-openam';
    public $url = 'http://openam.example.com:80/openam';
    public $cookie = 'iPlanetDirectoryPro';
    public $enabled = true;
    public $redirect = true;
    public $domain = '.example.com';
    public $realm = '';
    public $module = '';
    public $service = '';
    public $attributes = 'uid,mail';
    public $timeout = '300';
    public $debug = false;
    public $file = '';
}