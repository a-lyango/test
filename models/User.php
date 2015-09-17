<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property string $refresh_token
 *
 * @property InstagramInfo[] $instagramInfo
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'username',
                ],
                'required'
            ],
            [
                [
                    'username',
                    'password',
                    'auth_key',
                ],
                'string',
                'max' => 50
            ],
            [
                [
                    'access_token',
                    'refresh_token'
                ],
                'string',
                'max' => 100
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
            'username' => Yii::t('app', 'Ëîãèí'),
            'password' => Yii::t('app', 'Ïàğîëü'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'access_token' => Yii::t('app', 'Access Token'),
            'refresh_token' => Yii::t('app', 'Refresh Token'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstagramInfo()
    {
        return $this->hasMany(InstagramInfo::className(), ['owner_id' => 'id']);
    }
}
