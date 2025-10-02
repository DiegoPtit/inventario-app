<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salidas".
 *
 * @property int $id
 * @property int $id_producto
 * @property int $cantidad
 * @property int $is_movimiento
 * @property int|null $id_lugar_origen
 * @property int|null $id_lugar_destino
 * @property int|null $id_cliente
 * @property string $created_at
 *
 * @property Clientes $cliente
 * @property Lugares $lugarDestino
 * @property Lugares $lugarOrigen
 * @property Productos $producto
 */
class Salidas extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salidas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_lugar_origen', 'id_lugar_destino', 'id_cliente'], 'default', 'value' => null],
            [['is_movimiento'], 'default', 'value' => 0],
            [['id_producto', 'cantidad'], 'required'],
            [['id_producto', 'cantidad', 'is_movimiento', 'id_lugar_origen', 'id_lugar_destino', 'id_cliente'], 'integer'],
            [['created_at'], 'safe'],
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::class, 'targetAttribute' => ['id_cliente' => 'id']],
            [['id_lugar_destino'], 'exist', 'skipOnError' => true, 'targetClass' => Lugares::class, 'targetAttribute' => ['id_lugar_destino' => 'id']],
            [['id_lugar_origen'], 'exist', 'skipOnError' => true, 'targetClass' => Lugares::class, 'targetAttribute' => ['id_lugar_origen' => 'id']],
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
            'cantidad' => Yii::t('app', 'Cantidad'),
            'is_movimiento' => Yii::t('app', 'Is Movimiento'),
            'id_lugar_origen' => Yii::t('app', 'Id Lugar Origen'),
            'id_lugar_destino' => Yii::t('app', 'Id Lugar Destino'),
            'id_cliente' => Yii::t('app', 'Id Cliente'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Cliente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::class, ['id' => 'id_cliente']);
    }

    /**
     * Gets query for [[LugarDestino]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLugarDestino()
    {
        return $this->hasOne(Lugares::class, ['id' => 'id_lugar_destino']);
    }

    /**
     * Gets query for [[LugarOrigen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLugarOrigen()
    {
        return $this->hasOne(Lugares::class, ['id' => 'id_lugar_origen']);
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
