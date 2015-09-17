<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\CommentsPerDay;

/**
 * ��� ������� �������� ���������� ������ ��� ������ �����. ����� ����������� � ��� ������� ����������� �� ������� ����
 * � ��������� � ��
 */
class GetCountCommentsPerDayController extends Controller
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
        $yesterday = date('Y-m-d', strtotime("-1 day"));

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
                $countCommentsNow = 0;

                // ���� ���� �����������, ����������� �� ��� � ��������� ����. � ������ ������� �������, ����������� ������� ������������
                if (isset($mediaInfo->comments->data) && !empty($mediaInfo->comments->data))
                {
                    foreach ($mediaInfo->comments->data as $comment)
                    {
                        if ($comment->created_time > strtotime($date))
                        {
                            $countCommentsNow++;
                        }
                    }

                    // ��� ����������� ������� � ���������� �����, ������� �������������� �� ������ �������
                    CommentsPerDay::deleteAll([
                        'date' => $date,
                        'media_id' => $mediaInfo->id,
                        'owner_id' => $user['id'],
                    ]);

                    // ��������� ������, ���� ����������� ���� � ���� ����
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
