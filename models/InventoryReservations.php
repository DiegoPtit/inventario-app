<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory_reservations".
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $lugar_id
 * @property int $quantity
 * @property string $status
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string $expires_at
 * @property string $created_at
 * @property string|null $created_by
 */
class InventoryReservations extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_reservations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lugar_id', 'reference_type', 'reference_id', 'created_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'ACTIVE'],
            [['product_id', 'quantity', 'expires_at'], 'required'],
            [['product_id', 'lugar_id', 'quantity', 'reference_id'], 'integer'],
            [['expires_at', 'created_at'], 'safe'],
            [['status'], 'string', 'max' => 20],
            [['reference_type'], 'string', 'max' => 50],
            [['created_by'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'lugar_id' => 'Lugar ID',
            'quantity' => 'Quantity',
            'status' => 'Status',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

}
