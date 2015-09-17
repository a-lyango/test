<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\User;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

class InstagramController extends ActiveController
{
    public $modelClass = 'app\models\MediaInfo';

    public $users = [];

    public $limit;
    public $offset;

    public function init()
    {
        $request = Yii::$app->request;

        $accounts = explode(',', $request->get('account'));

        // Определим лимит (1-1000) и смещение (>=0)
        $this->limit = min(max((int)$request->get('limit', 1000), 1), 1000);
        $this->offset = max((int)$request->get('offset', 0), 0);

        // Получаем информацию о пользователях из БД
        $this->users = User::find()->where(['username' => $accounts])->indexBy('id')->asArray()->all();
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'mediaList' => [
                'class' => 'app\api\modules\v1\controllers\instagram\MediaListAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'media' => [
                'class' => 'app\api\modules\v1\controllers\instagram\MediaAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'grabMediaInfo' => [
                'class' => 'app\api\modules\v1\controllers\instagram\GrabMediaInfoAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);
    }

    /**
     * В данном методе установим формат ответа виде json
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ]

        ];

        return $behaviors;
    }
}
