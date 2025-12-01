<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "items_factura".
 *
 * @property int $id
 * @property int $id_factura
 * @property int $id_producto
 * @property int $cantidad
 * @property float $precio_unitario
 * @property float|null $subtotal
 * @property string $created_at
 *
 * @property Facturas $factura
 * @property Productos $producto
 */
class ItemsFactura extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'items_factura';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subtotal'], 'default', 'value' => null],
            [['cantidad'], 'default', 'value' => 1],
            [['precio_unitario'], 'default', 'value' => 0.00],
            [['id_factura', 'id_producto'], 'required'],
            [['id_factura', 'id_producto', 'cantidad'], 'integer'],
            [['precio_unitario', 'subtotal'], 'number'],
            [['created_at'], 'safe'],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::class, 'targetAttribute' => ['id_factura' => 'id']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::class, 'targetAttribute' => ['id_producto' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_factura' => Yii::t('app', 'Id Factura'),
            'id_producto' => Yii::t('app', 'Id Producto'),
            'cantidad' => Yii::t('app', 'Cantidad'),
            'precio_unitario' => Yii::t('app', 'Precio Unitario'),
            'subtotal' => Yii::t('app', 'Subtotal'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Factura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturas::class, ['id' => 'id_factura']);
    }

    /**
     * Gets query for [[Producto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Productos::class, ['id' => 'id_producto']);
    }

}
