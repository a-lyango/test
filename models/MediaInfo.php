<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media_info".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $media_id
 * @property string $photo_url
 * @property string $photo_caption
 * @property integer $count_likes
 * @property integer $count_comments
 * @property string $date_update
 *
 * @property User $owner
 */
class MediaInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_info';
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
                    'photo_url',
                    'date_update',
                    'count_likes',
                    'count_comments'
                ],
                'required'
            ],
            [
                [
                    'owner_id',
                    'count_likes',
                    'count_comments'
                ],
                'integer'
            ],
            [
                ['date_update'],
                'safe'
            ],
            [
                ['media_id'],
                'string',
                'max' => 70
            ],
            [
                [
                    'photo_url',
                    'photo_caption'
                ],
                'string',
                'max' => 150
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
            'photo_url' => Yii::t('app', 'Photo Url'),
            'photo_caption' => Yii::t('app', 'Photo Caption'),
            'count_likes' => Yii::t('app', 'Count Likes'),
            'count_comments' => Yii::t('app', 'Count Comments'),
            'date_update' => Yii::t('app', 'Date Update'),
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
