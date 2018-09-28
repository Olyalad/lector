<?php

namespace common\components;


use yii\base\Model;

class OpenAmParams extends Model
{
    // default openam parameters
    public $auth = '/json/authenticate';
    public $attr = '/json/users/';
    public $sess = '/json/sessions/';
    public $login = '/UI/Login';
    public $logout = '/UI/Logout';
    public $realm = 'realm';
    public $service = 'service';
    public $module = 'module';
    public $goto = 'goto';
    public $auth_type = 'authIndexType';
    public $auth_value = 'authIndexValue';
}