<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "methods_restrictions".
 *
 * @property int $id
 * @property int $method_id
 * @property int $has_banco
 * @property int $has_titular
 * @property int $has_numero_cuenta
 * @property int $has_telefono
 * @property int $has_cedula
 * @property string $created_at
 */
class MethodsRestrictions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'methods_restrictions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['has_cedula'], 'default', 'value' => 0],
            [['method_id'], 'required'],
            [['method_id', 'has_banco', 'has_titular', 'has_numero_cuenta', 'has_telefono', 'has_cedula'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'method_id' => 'Method ID',
            'has_banco' => 'Has Banco',
            'has_titular' => 'Has Titular',
            'has_numero_cuenta' => 'Has Numero Cuenta',
            'has_telefono' => 'Has Telefono',
            'has_cedula' => 'Has Cedula',
            'created_at' => 'Created At',
        ];
    }

}
