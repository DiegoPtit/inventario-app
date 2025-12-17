<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_methods".
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $titular
 * @property string|null $banco
 * @property string|null $telefono
 * @property string|null $cedula
 * @property string|null $numero_cuenta
 * @property string $created_at
 * @property string|null $updated_at
 */
class PaymentMethods extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_methods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titular', 'banco', 'telefono', 'cedula', 'numero_cuenta', 'updated_at'], 'default', 'value' => null],
            [['nombre'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['nombre', 'titular', 'banco', 'numero_cuenta'], 'string', 'max' => 255],
            [['telefono', 'cedula'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'titular' => 'Titular',
            'banco' => 'Banco',
            'telefono' => 'Telefono',
            'cedula' => 'Cedula',
            'numero_cuenta' => 'Numero Cuenta',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
