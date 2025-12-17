<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_orders".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $address_id
 * @property int|null $method_id
 * @property float $total
 * @property string $currency
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $external_payment_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class StoreOrders extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'address_id', 'method_id', 'payment_method', 'external_payment_id', 'updated_at'], 'default', 'value' => null],
            [['total'], 'default', 'value' => 0.00],
            [['currency'], 'default', 'value' => 'USD'],
            [['status'], 'default', 'value' => 'AWAITING_PAYMENT'],
            [['user_id', 'address_id', 'method_id'], 'integer'],
            [['total'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['currency'], 'string', 'max' => 10],
            [['status'], 'string', 'max' => 30],
            [['payment_method'], 'string', 'max' => 50],
            [['external_payment_id'], 'string', 'max' => 255],
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
            'address_id' => 'Address ID',
            'method_id' => 'Method ID',
            'total' => 'Total',
            'currency' => 'Currency',
            'status' => 'Status',
            'payment_method' => 'Payment Method',
            'external_payment_id' => 'External Payment ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
