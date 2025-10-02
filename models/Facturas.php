<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facturas".
 *
 * @property int $id
 * @property int|null $id_cliente
 * @property string $codigo
 * @property string|null $concepto
 * @property float $monto_calculado
 * @property float $monto_final
 * @property string $fecha
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Clientes $cliente
 * @property HistoricoCobros[] $historicoCobros
 * @property ItemsFactura[] $itemsFacturas
 */
class Facturas extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cliente', 'concepto'], 'default', 'value' => null],
            [['monto_final'], 'default', 'value' => 0.00],
            [['fecha'], 'default', 'value' => 'curdate()'],
            [['id_cliente'], 'integer'],
            [['codigo'], 'required'],
            [['monto_calculado', 'monto_final'], 'number'],
            [['fecha', 'created_at', 'updated_at'], 'safe'],
            [['codigo'], 'string', 'max' => 120],
            [['concepto'], 'string', 'max' => 255],
            [['codigo'], 'unique'],
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::class, 'targetAttribute' => ['id_cliente' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_cliente' => Yii::t('app', 'Id Cliente'),
            'codigo' => Yii::t('app', 'Codigo'),
            'concepto' => Yii::t('app', 'Concepto'),
            'monto_calculado' => Yii::t('app', 'Monto Calculado'),
            'monto_final' => Yii::t('app', 'Monto Final'),
            'fecha' => Yii::t('app', 'Fecha'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * Gets query for [[HistoricoCobros]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoCobros()
    {
        return $this->hasMany(HistoricoCobros::class, ['id_factura' => 'id']);
    }

    /**
     * Gets query for [[ItemsFacturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemsFacturas()
    {
        return $this->hasMany(ItemsFactura::class, ['id_factura' => 'id']);
    }

}
