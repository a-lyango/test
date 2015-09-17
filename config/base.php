<?php

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'db' => require(__DIR__ . '/db.php'),
        'instagram' => [
            'class' => 'app\api\components\InstagramHelper',
            'className' => 'MetzWeb\Instagram\Instagram',
            'config' => [
                'apiKey' => 'ff8a1d0784c043e1afc06c46ec5ba40e',
                'apiSecret' => '3b7e612bd4284a78818bb5015c7ca3d0',
                'apiCallback' => 'http://test.loc?r=site%2Foauthcallback'
            ]
        ],
    ],
    'params' => [],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
