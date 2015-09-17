<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "likes_per_day".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $media_id
 * @property integer $count_likes
 * @property string $date
 *
 * @property User $owner
 */
class LikesPerDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'likes_per_day';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'owner_id',
                    'media_id',
                    'count_likes',
                    'date'
                ],
                'required'
            ],
            [
                [
                    'owner_id',
                    'count_likes'
                ],
                'integer'
            ],
            [
                [
                    'media_id',
                ],
                'string',
                'max' => 70
            ],
            [
                ['date'],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'owner_id' => Yii::t('app', 'Owner ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'count_likes' => Yii::t('app', 'Count Likes'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }
}
