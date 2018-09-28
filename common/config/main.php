<?php
return [
    'name' => 'ФГБОУ ВО НГТУ',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost:3306;dbname=lector_db',
            'username' => 'lector_duserb',
            'password' => 'wVij4_87',
            'charset' => 'utf8',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm:ss',
//            'decimalSeparator' => ',',
//            'thousandSeparator' => ' ',
//            'currencyCode' => 'EUR',
//            'locale' => 'ru-RU',
            'timeZone' => 'Asia/Novosibirsk'
        ],

        /* Авторизация */
        'openAm' => [
            'class' => 'common\components\NstuAuthSso',
            'config' => [
                'class' => 'common\components\OpenAmConfig',
                'url' => 'https://login.nstu.ru/ssoservice',
                'cookie' => 'NstuSsoToken',
                'domain' => '.nstu.ru',
                'name' => 'el-nstu',
                'redirect' => true,

                'debug' => false,
                'file' => dirname(dirname(__DIR__)) . '/log/openam.log',
            ]
        ],

        /* ActiveDirectory */
        'ad' => [
            'class' => 'Edvlerblog\Adldap2\Adldap2Wrapper',
            'providers' => [
                'default' => [
                    'autoconnect' => true,
                    'config' => [
                        'domain_controllers'    => ['nstu-dc1.corp.nstu.ru', 'nstu-dc2.corp.nstu.ru'],
                        'base_dn'               => 'dc=openam,dc=ciu,dc=nstu,dc=ru',

                        'account_suffix'        => '@stud.nstu.ru',

                        'admin_username'        => 'ladygina.2012@stud.nstu.ru',
                        'admin_password'        => 'wind0fChange',
                        'admin_account_suffix'  => '@stud.nstu.ru',
                    ]
                ],
            ],
        ],


    ],

    // set target language to be Russian
    'language' => 'ru-RU',
    // set source language to be English
    'sourceLanguage' => 'en-US',
];
