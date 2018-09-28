<?php

namespace common\components;


use yii\base\Model;

class OpenAmDesc extends Model
{
    // openam configurations descriptions
    public $name = array('Name of instance', 'Name for Site Identification');
    public $enabled = array('REST enabled', 'Enabled or disabled OpenAM REST Authentication');
    public $cookie = array('Session cookie', 'Name of OpenAM session cookie, by default is <b>iPlanetDirectoryPro</b>, but can be something different');
    public $domain = array('Cookie domain', 'The Domain where OpenAM cookie will be set, once the user authenticates, default is the last 2 components of the domain');
    public $url = array('Base URL', 'OpenAM base deployment URL, for example: <b>http://openam.example.com:80/openam</b>');
    public $realm = array('Authentication Realm', 'OpenAM Realm where users reside, by default <b>/</b> or may be <b>/myrealm</b>');
    public $module = array('Authentication Module', 'The Authentication Module to be used in the OpenAM, for example: <b>DataStore</b> or <b>LDAP</b><br/>This option can be left empty, in which case the default module configured in OpenAM wil be used <br/><i>Note: Module and Service Chain can not be used at the same time</i>');
    public $service = array('Authentication Service', 'The Authentication Service (Chain) to be used in the OpenAM, for example: <b>ldapService</b> or <b>myChain</b><br/>This option can be left empty, in which case the default service configured in OpenAM wil be used <br/><i>Note: Module and Service Chain can not be used at the same time</i>');
    public $redirect = array('Redirected Auth', 'Redirect to OpenAM pages for login\logout actions, authentication chains and modules with a complex workflow');
    public $attributes = array('User Attributes', 'Comma separated name of the OpenAM attributes to map user login name, for example: <b>uid,mail</b>');
    public $timeout = array('Cache Timeout', 'During this time in seconds the OpenAM token stored in php session is considered valid');
    public $debug = array('Debug Enable', 'Enables debug in the OpenAM REST Authentication. Remember to turn-off in production environment');
    public $file = array('Debug File', 'If debug enabled, the destination filename may be specified, if filename is empty - debug messages forwarded to <b>error_log</b>');

}