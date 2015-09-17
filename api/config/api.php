<?php

use yii\helpers\ArrayHelper;

$params = require(__DIR__ . '/params.php');
$baseConfig = require(__DIR__ . '/../../config/base.php');

$config = ArrayHelper::merge($baseConfig, [
    'name' => 'TestAPI',

    'basePath' => dirname(__DIR__).'/..',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/instagram',
                    ],
                    'pluralize' => false,
                    'except' => ['delete', 'create', 'update'],
                    'extraPatterns' => [
                        'GET media' => 'mediaList',
                        'GET media/grab' => 'grabMediaInfo',
                        "GET media/<id:\w+>" => 'media',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\api\modules\v1\ApiModule',
        ],
    ],
    'params' => $params,
]);

return $config;