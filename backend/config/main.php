<?php

use mdm\admin\Module;

require_once(__DIR__ . '/const.php');
$rules = require(__DIR__ . '/rules.php');
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'layout' => 'main.blade',
    'modules' => [
        'gii' => [
            'class' => \yii\gii\Module::class,
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'rbac' => [
            'class' => Module::class,
            'layout' => 'left-menu',
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'QqhWhK8-MJKqCGbUInK5J2UjWMgFIvWB',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        '/theme/js/jquery.js',
                        '/theme/libs/moment/min/moment.min.js',
                        '/theme/js/handlebars.js',
                        '/theme/js/HandlebarsHelper.js',
                        '/theme/libs/chart.js/Chart.bundle.min.js'
                    ]
                ],
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'css' => [
                        '/theme/css/bootstrap.min.css',
                        #'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'
                    ],
                    'js' => [
                        '/theme/js/bootstrap.js'
                    ]
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],
    ],

    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'rbac/*',
            'site/logout',
            'site/login'
        ]
    ],
    'params' => $params,
];
