<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stock".
 *
 * @property int $id
 * @property int $id_producto
 * @property int $id_lugar
 * @property int $cantidad
 * @property string $updated_at
 *
 * @property Lugares $lugar
 * @property Productos $producto
 */
class Stock extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cantidad'], 'default', 'value' => 0],
            [['id_producto', 'id_lugar'], 'required'],
            [['id_producto', 'id_lugar', 'cantidad'], 'integer'],
            [['updated_at'], 'safe'],
            [['id_producto', 'id_lugar'], 'unique', 'targetAttribute' => ['id_producto', 'id_lugar']],
            [['id_lugar'], 'exist', 'skipOnError' => true, 'targetClass' => Lugares::class, 'targetAttribute' => ['id_lugar' => 'id']],
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
            'id_producto' => Yii::t('app', 'Id Producto'),
            'id_lugar' => Yii::t('app', 'Id Lugar'),
            'cantidad' => Yii::t('app', 'Cantidad'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Lugar]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLugar()
    {
        return $this->hasOne(Lugares::class, ['id' => 'id_lugar']);
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
