<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\LikesPerDay;

/**
 * Это команда получает количество лайков для каждой медиа. Затем анализирует о том сколько проставлено за текущий день
 * и сохраняет в БД
 */
class GetCountLikesPerDayController extends Controller
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
                // Пытаемся получить количество лайков, который были проставлены до текущего дня
                $likesSum = LikesPerDay::find()
                    ->select('SUM(count_likes) AS ct')
                    ->where('date < :date', ['date' => $date])
                    ->andWhere([
                        'media_id' => $mediaInfo->id,
                        'owner_id' => $user['id'],
                    ])
                    ->asArray()
                    ->groupBy(['date'])
                    ->one();

                // Вычисляем количество проставленных лайков за текущий день
                $countLikesNow = $mediaInfo->likes->count - (!empty($likesSum['ct']) ? $likesSum['ct'] : 0);

                // Удаляем старые записи за текущий день
                LikesPerDay::deleteAll([
                    'date' => $date,
                    'media_id' => $mediaInfo->id,
                    'owner_id' => $user['id'],
                ]);

                // Если есть новые - сохраняем
                if (!empty($countLikesNow))
                {
                    $likesPerDay = new LikesPerDay();

                    $likesPerDay->date = $date;
                    $likesPerDay->media_id = $mediaInfo->id;
                    $likesPerDay->owner_id = $user['id'];
                    $likesPerDay->count_likes = (int)$countLikesNow;

                    $likesPerDay->save();
                }
            }
        }
    }
}
