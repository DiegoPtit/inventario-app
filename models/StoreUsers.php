<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_users".
 *
 * @property int $id
 * @property string $email
 * @property string $password_hash
 * @property string|null $nombre
 * @property string|null $telefono
 * @property string $created_at
 * @property string|null $updated_at
 */
class StoreUsers extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'telefono', 'updated_at'], 'default', 'value' => null],
            [['email', 'password_hash'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['email', 'password_hash'], 'string', 'max' => 255],
            [['nombre'], 'string', 'max' => 150],
            [['telefono'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'nombre' => 'Nombre',
            'telefono' => 'Telefono',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
