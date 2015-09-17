<?php

namespace app\api\components;

use yii\base\Component;

class InstagramHelper extends Component
{
    public $className;

    public $config;


    public function getHelper()
    {
        return new $this->className($this->config);
    }
}