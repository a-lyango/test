<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\LikesPerDay;

/**
 * ��� ������� �������� ���������� ������ ��� ������ �����. ����� ����������� � ��� ������� ����������� �� ������� ����
 * � ��������� � ��
 */
class GetCountLikesPerDayController extends Controller
{
    const LIMIT = 1000;

    public function actionIndex()
    {
        /**
         * �������� ������, ������� ������� �������� ���������� �� Instagram
         *
         * @var \MetzWeb\Instagram\Instagram $instagramHelper
         */
        $instagramHelper = Yii::$app->instagram->getHelper();

        // �������� ���������� � ���� ������������� �� ��
        $users = User::find()->asArray()->all();

        $date = date('Y-m-d');

        foreach ($users as $user)
        {
            // ������������� access token ������������ ��� ������� � api Instagram
            $instagramHelper->setAccessToken($user['access_token']);

            $userFeed = $instagramHelper->getUserFeed(self::LIMIT);

            $data = $userFeed->data;

            /**
             * ��������� ������� ������, ���� ��� ����
             */
            do
            {
                $userFeed = $instagramHelper->pagination($userFeed, self::LIMIT);
                $data = array_merge($data, (array)$userFeed->data);
            }
            while (!empty($userFeed));

            // ����������� �� ������� � ��������� ������
            foreach($data as $mediaInfo)
            {
                // �������� �������� ���������� ������, ������� ���� ����������� �� �������� ���
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

                // ��������� ���������� ������������� ������ �� ������� ����
                $countLikesNow = $mediaInfo->likes->count - (!empty($likesSum['ct']) ? $likesSum['ct'] : 0);

                // ������� ������ ������ �� ������� ����
                LikesPerDay::deleteAll([
                    'date' => $date,
                    'media_id' => $mediaInfo->id,
                    'owner_id' => $user['id'],
                ]);

                // ���� ���� ����� - ���������
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
