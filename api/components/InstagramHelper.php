<?php
/**
 * Хелпер, который поможет получить информацию из Instagram
 */

namespace app\api\components;

use yii\base\Component;

class InstagramHelper extends Component
{
    public $className;

    public $config;

    /**
     * Возвращаем объект для работы с АПИ инстаграма, заданный в конфигах. Инициализируем настройками из конфигов
     *
     * @return mixed
     */
    public function getHelper()
    {
        return new $this->className($this->config);
    }
}