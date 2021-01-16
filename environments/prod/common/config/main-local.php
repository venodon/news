<?php
return [
    'components' => [
        'log'   => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                'all'         => [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    'logFile' => '@common/runtime/logs/app.log',
                    'except' => ['yii\web\HttpException:404']
                ],
                'info' => [
                    'class'   => 'yii\log\FileTarget',
                    'categories' => ['problem', 'application', 'info'],
                    'logFile' => '@common/runtime/logs/info.log',
                    'logVars' => ['_COOKIE', '_SESSION'],
                ]
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer'               => [
            'class'            => 'yii\swiftmailer\Mailer',
            'viewPath'         => '@frontend/mail',
            'htmlLayout'       => '@frontend/mail/layout',
            'transport'        => [
                'class' => 'Swift_SmtpTransport',
                'host'       => 'smtp.yandex.ru',
                'username'   => '',
                'password'   => '',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
            'useFileTransport' => false,
        ],
        'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => [

            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache'
        ],
    ],
];
