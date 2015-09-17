<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\CommentsPerDay;

/**
 * Это команда получает количество лайков для каждой медиа. Затем анализирует о том сколько проставлено за текущий день
 * и сохраняет в БД
 */
class GetCountCommentsPerDayController extends Controller
{
    const LIMIT = 1000;

    public function actionIndex()
    {
        /**
         * Получаем хелпер, который поможет получить информацию из Instagram
         *
         * @var \MetzWeb\Instagram\Instagram $instagramHelper
         */
        $instagramHelper = Yii::$app->instagram->getHelper();

        // Получаем информацию о всех пользователях из БД
        $users = User::find()->asArray()->all();

        $date = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 day"));

        foreach ($users as $user)
        {
            // Устанавливаем access token пользователя для доступа к api Instagram
            $instagramHelper->setAccessToken($user['access_token']);

            $userFeed = $instagramHelper->getUserFeed(self::LIMIT);

            $data = $userFeed->data;

            /**
             * Повторяем выборку данных, пока они есть
             */
            do
            {
                $userFeed = $instagramHelper->pagination($userFeed, self::LIMIT);
                $data = array_merge($data, (array)$userFeed->data);
            }
            while (!empty($userFeed));

            // Пробегаемся по массиву и сохраняем данные
            foreach($data as $mediaInfo)
            {
                $countCommentsNow = 0;

                // Если есть комментарии, пробегаемся по ним и проверяем дату. В случае верного условия, увеличиваем счетчик комментариев
                if (isset($mediaInfo->comments->data) && !empty($mediaInfo->comments->data))
                {
                    foreach ($mediaInfo->comments->data as $comment)
                    {
                        if ($comment->created_time > strtotime($date))
                        {
                            $countCommentsNow++;
                        }
                    }

                    // Для недопущения записей с одинаковой датой, очищаем предварительно от старых записей
                    CommentsPerDay::deleteAll([
                        'date' => $date,
                        'media_id' => $mediaInfo->id,
                        'owner_id' => $user['id'],
                    ]);

                    // Сохраняем только, если комментарии были в этот день
                    if (!empty($countCommentsNow))
                    {
                        $likesPerDay = new CommentsPerDay();

                        $likesPerDay->date = $date;
                        $likesPerDay->media_id = $mediaInfo->id;
                        $likesPerDay->owner_id = $user['id'];
                        $likesPerDay->count_comments = (int)$countCommentsNow;

                        $likesPerDay->save();
                    }
                }
            }
        }
    }
}
