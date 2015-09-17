<?php
/**
 *  UserInfoAction является конечной точкой получения информации о пользователях из Instagram
 */
namespace app\api\modules\v1\controllers\instagram;

use app\models\MediaInfo;
use Yii;
use yii\rest\Action;

class GrabMediaInfoAction extends Action
{
    // Установим лимит выборки записей за раз
    const LIMIT = 1000;

    /**
     * @return array
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /**
         * Получаем хелпер, который поможет получить информацию из Instagram
         *
         * @var \MetzWeb\Instagram\Instagram $instagramHelper
         */
        $instagramHelper = Yii::$app->instagram->getHelper();

        $errors = [];

        // Пробегаемся по всем доступным аккаунтам и получаем сводную информацию по пользователям
        foreach ($this->controller->users as $user)
        {
            // Устанавливаем access token пользователя для доступа к api Instagram
            $instagramHelper->setAccessToken($user['access_token']);

            /**
             * Будем получать информацию порциями, чтобы не превысить лимиты Instagram
             * и иметь возможность сохранять только необходимые данные для экономии памяти (здесь не реализовано, поэтому бонуса от экономии памяти нету)
             */
            $userFeed = $instagramHelper->getUserFeed(self::LIMIT);

            // Сохраняем в массив первую часть полученной информации
            $data = $userFeed->data;

            /**
             * Повторяем выборку данных, пока они есть
             */
            do
            {
                $userFeed = $instagramHelper->pagination($userFeed, self::LIMIT);

                // объединяем полученные данные
                $data = array_merge($data, (array)$userFeed->data);
            }
            while (!empty($userFeed));

            // Удаляем старые данные из БД
            MediaInfo::deleteAll(['owner_id' => $user['id']]);

            // Пробегаемся по массиву и сохраняем новые данные
            foreach($data as $item)
            {
                $mediaInfo = new MediaInfo();

                $mediaInfo->date_update = date('Y-m-d H:i:s');
                $mediaInfo->media_id = $item->id;
                $mediaInfo->owner_id = $user['id'];
                $mediaInfo->photo_url = $item->link;
                $mediaInfo->photo_caption = isset($item->caption->text) ? $item->caption->text : '';
                $mediaInfo->count_comments = isset($item->comments->count) ? $item->comments->count : '';
                $mediaInfo->count_likes = isset($item->likes->count) ? $item->likes->count : '';

                if (!$mediaInfo->save())
                {
                    $errors[] = $mediaInfo->errors;
                }
            }
        }

        return [
            'errors' => $errors,
        ];
    }
}
