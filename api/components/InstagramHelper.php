<?php
/**
 * ������, ������� ������� �������� ���������� �� Instagram
 */

namespace app\api\components;

use yii\base\Component;

class InstagramHelper extends Component
{
    public $className;

    public $config;

    /**
     * ���������� ������ ��� ������ � ��� ����������, �������� � ��������. �������������� ����������� �� ��������
     *
     * @return mixed
     */
    public function getHelper()
    {
        return new $this->className($this->config);
    }
}