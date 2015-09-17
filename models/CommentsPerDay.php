<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comments_per_day".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $media_id
 * @property integer $count_comments
 * @property string $date
 *
 * @property User $owner
 */
class CommentsPerDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comments_per_day';
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
                    'count_comments',
                    'date'
                ],
                'required'
            ],
            [
                [
                    'owner_id',
                    'count_comments'
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
            'count_comments' => Yii::t('app', 'Count Comments'),
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
