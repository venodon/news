<?php

use yii\rbac\DbManager;
use yii\log\FileTarget;
use yii\console\controllers\MigrateController;
use yii\console\controllers\FixtureController;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap'       => [
        'fixture' => [
            'class'     => FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
        'migrate' => [
            'class'         => MigrateController::class,
            'migrationPath' => [
                Yii::getAlias('@console') . '/migrations',
                Yii::getAlias('@modules') . '/users/migrations',
                Yii::getAlias('@modules') . '/config/migrations',
                Yii::getAlias('@modules') . '/news/migrations'
            ],
        ],
    ],
    'components'          => [
        'log'         => [
            'targets' => [
                [
                    'class'  => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
    ],
    'params'              => $params,
];
