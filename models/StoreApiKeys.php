<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_api_keys".
 *
 * @property int $id
 * @property string $api_key
 * @property string|null $description
 * @property int $active
 * @property string $created_at
 * @property string|null $last_used_at
 */
class StoreApiKeys extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_api_keys';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'last_used_at'], 'default', 'value' => null],
            [['active'], 'default', 'value' => 1],
            [['api_key'], 'required'],
            [['active'], 'integer'],
            [['created_at', 'last_used_at'], 'safe'],
            [['api_key', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_key' => 'Api Key',
            'description' => 'Description',
            'active' => 'Active',
            'created_at' => 'Created At',
            'last_used_at' => 'Last Used At',
        ];
    }

}
