<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_sessions".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $session_token
 * @property string|null $data
 * @property string $created_at
 * @property string|null $expires_at
 */
class StoreSessions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'data', 'expires_at'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['session_token'], 'required'],
            [['data'], 'string'],
            [['created_at', 'expires_at'], 'safe'],
            [['session_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'session_token' => 'Session Token',
            'data' => 'Data',
            'created_at' => 'Created At',
            'expires_at' => 'Expires At',
        ];
    }

}
