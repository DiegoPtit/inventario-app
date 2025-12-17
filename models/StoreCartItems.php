<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_cart_items".
 *
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $cantidad
 * @property string $added_at
 */
class StoreCartItems extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_cart_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cantidad'], 'default', 'value' => 1],
            [['cart_id', 'product_id'], 'required'],
            [['cart_id', 'product_id', 'cantidad'], 'integer'],
            [['added_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cart_id' => 'Cart ID',
            'product_id' => 'Product ID',
            'cantidad' => 'Cantidad',
            'added_at' => 'Added At',
        ];
    }

}
