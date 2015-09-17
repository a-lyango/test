<?php
/**
 *  UserInfoAction �������� �������� ������ ��������� ���������� � ������������� �� Instagram
 */
namespace app\api\modules\v1\controllers\instagram;

use app\models\MediaInfo;
use Yii;
use yii\rest\Action;

class GrabMediaInfoAction extends Action
{
    // ��������� ����� ������� ������� �� ���
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
         * �������� ������, ������� ������� �������� ���������� �� Instagram
         *
         * @var \MetzWeb\Instagram\Instagram $instagramHelper
         */
        $instagramHelper = Yii::$app->instagram->getHelper();

        $errors = [];

        // ����������� �� ���� ��������� ��������� � �������� ������� ���������� �� �������������
        foreach ($this->controller->users as $user)
        {
            // ������������� access token ������������ ��� ������� � api Instagram
            $instagramHelper->setAccessToken($user['access_token']);

            /**
             * ����� �������� ���������� ��������, ����� �� ��������� ������ Instagram
             * � ����� ����������� ��������� ������ ����������� ������ ��� �������� ������ (����� �� �����������, ������� ������ �� �������� ������ ����)
             */
            $userFeed = $instagramHelper->getUserFeed(self::LIMIT);

            // ��������� � ������ ������ ����� ���������� ����������
            $data = $userFeed->data;

            /**
             * ��������� ������� ������, ���� ��� ����
             */
            do
            {
                $userFeed = $instagramHelper->pagination($userFeed, self::LIMIT);

                // ���������� ���������� ������
                $data = array_merge($data, (array)$userFeed->data);
            }
            while (!empty($userFeed));

            // ������� ������ ������ �� ��
            MediaInfo::deleteAll(['owner_id' => $user['id']]);

            // ����������� �� ������� � ��������� ����� ������
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
