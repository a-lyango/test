<?php
/**
 * MediaAction является конечной точкой для получения информациия о медиафайлах.
 *
 */
namespace app\api\modules\v1\controllers\instagram;

use app\models\CommentsPerDay;
use app\models\LikesPerDay;
use app\models\MediaInfo;
use Yii;
use yii\rest\Action;

class MediaListAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        if ($this->checkAccess)
        {
            call_user_func($this->checkAccess, $this->id);
        }

        $result = [];

        // Получаем данные о всех фото из БД
        $mediaInfo = MediaInfo::find()
            ->where(['in', 'owner_id', array_keys($this->controller->users)])
            ->limit($this->controller->limit)
            ->offset($this->controller->offset)
            ->asArray()
            ->all();

        foreach ($mediaInfo as $key => $item)
        {
            $result[$key] = [
                'photo_url' => $item['photo_url'],
                'photo_caption' => $item['photo_caption'],
                'count_likes' => $item['count_likes'],
                'count_comments' => $item['count_comments'],
            ];

            if (Yii::$app->request->get('groupByDay', 0) == 1)
            {
                $result[$key]['count_likes'] = LikesPerDay::find()
                    ->select('date , count_likes')
                    ->where(['media_id' => $item['media_id']])
                    ->all();

                $result[$key]['count_comments'] = CommentsPerDay::find()
                    ->select('date , count_comments')
                    ->where(['media_id' => $item['media_id']])
                    ->all();
            }
        }

        return $result;
    }
}
