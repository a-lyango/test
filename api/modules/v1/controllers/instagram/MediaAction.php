<?php
/**
 * MediaAction �������� �������� ������ ��� ��������� ����������� � ���������.
 *
 */
namespace app\api\modules\v1\controllers\instagram;

use app\models\CommentsPerDay;
use app\models\LikesPerDay;
use app\models\MediaInfo;
use Yii;
use yii\rest\Action;

class MediaAction extends Action
{
    /**
     * @return array
     */
    public function run($id)
    {
        if ($this->checkAccess)
        {
            call_user_func($this->checkAccess, $this->id);
        }

        // �������� ������ � ���� ���� �� ��
        $mediaInfo = MediaInfo::find()
            ->where(['in', 'owner_id', array_keys($this->controller->users)])
            ->andWhere(['media_id' => $id])
            ->asArray()
            ->one();

        // ���� ������� �� ������, ���������� ������ ���������
        if (empty($mediaInfo))
        {
            return [];
        }

        // ��������� �������������� ������
        $result = [
            'photo_url' => $mediaInfo['photo_url'],
            'photo_caption' => $mediaInfo['photo_caption'],
            'count_likes' => $mediaInfo['count_likes'],
            'count_comments' => $mediaInfo['count_comments'],
        ];

        // ���� ��� ������� �������� �� ����������� ������ �� ���� - �������� ��
        if (Yii::$app->request->get('groupByDay', 0) == 1)
        {
            // ���������� ������ �� ����
            $result['count_likes'] = LikesPerDay::find()
                ->select('date , count_likes')
                ->where(['media_id' => $mediaInfo['media_id']])
                ->all();

            // ���������� ������������ �� ����
            $result['count_comments'] = CommentsPerDay::find()
                ->select('date , count_comments')
                ->where(['media_id' => $mediaInfo['media_id']])
                ->all();
        }

        return $result;
    }
}
