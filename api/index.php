<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
 
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
 
// Use a distinct configuration for the API
$config = require(__DIR__ . '/config/api.php');

function vd($var, $exit = true, $print = true)
{
    $dump = yii\helpers\BaseVarDumper::dumpAsString($var, 10, true);

    if (empty($print))
    {
        return $dump;
    }

    echo $dump;
    if ($exit)
        exit;
}

(new yii\web\Application($config))->run();
