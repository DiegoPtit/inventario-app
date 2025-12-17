<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_addresses".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $label
 * @property string|null $destinatario
 * @property string|null $linea1
 * @property string|null $linea2
 * @property string|null $ciudad
 * @property string|null $estado
 * @property string|null $pais
 * @property string|null $postal
 * @property string|null $telefono
 * @property string $created_at
 */
class StoreAddresses extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_addresses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'label', 'destinatario', 'linea1', 'linea2', 'ciudad', 'estado', 'pais', 'postal', 'telefono'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['label', 'ciudad', 'estado', 'pais'], 'string', 'max' => 100],
            [['destinatario'], 'string', 'max' => 150],
            [['linea1', 'linea2'], 'string', 'max' => 255],
            [['postal', 'telefono'], 'string', 'max' => 50],
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
            'label' => 'Label',
            'destinatario' => 'Destinatario',
            'linea1' => 'Linea1',
            'linea2' => 'Linea2',
            'ciudad' => 'Ciudad',
            'estado' => 'Estado',
            'pais' => 'Pais',
            'postal' => 'Postal',
            'telefono' => 'Telefono',
            'created_at' => 'Created At',
        ];
    }

}
