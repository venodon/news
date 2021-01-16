<?php

use common\models\User;
use modules\news\News;
use yii\rbac\DbManager;
use dmstr\web\AdminLteAsset;
use modules\config\Config;
use modules\users\Users;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-backend',
    'name'                => 'TestWork',
    'sourceLanguage'      => 'ru',
    'language'            => 'ru',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap'           => ['log'],
    'defaultRoute'        => 'news',
    'modules'             => [
        'users' => [
            'class' => Users::class,
        ],
        'config' => [
            'class' => Config::class,
        ],
        'news' => [
            'class' => News::class,
        ],
    ],
    'components'          => [
        'view'         => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@backend/views'
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                AdminLteAsset::class => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],
        'authManager'  => [
            'class' => DbManager::class,
        ],
        'request'      => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user'         => [
            'identityClass'   => User::class,
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity', 'httpOnly' => true],
        ],
        'session'      => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced',
            'cookieParams' =>[
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
            ],
        ],
    ],
    'params'              => $params,
];
