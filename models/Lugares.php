<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lugares".
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property string|null $ubicacion
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Entradas[] $entradas
 * @property HistoricoMovimientos[] $historicoMovimientos
 * @property HistoricoMovimientos[] $historicoMovimientos0
 * @property Productos[] $productos
 * @property Productos[] $productos0
 * @property Salidas[] $salidas
 * @property Salidas[] $salidas0
 * @property Stock[] $stocks
 */
class Lugares extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lugares';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'ubicacion'], 'default', 'value' => null],
            [['nombre'], 'required'],
            [['descripcion'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nombre'], 'string', 'max' => 200],
            [['ubicacion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'ubicacion' => Yii::t('app', 'Ubicacion'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Entradas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntradas()
    {
        return $this->hasMany(Entradas::class, ['id_lugar' => 'id']);
    }

    /**
     * Gets query for [[HistoricoMovimientos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoMovimientos()
    {
        return $this->hasMany(HistoricoMovimientos::class, ['id_lugar_destino' => 'id']);
    }

    /**
     * Gets query for [[HistoricoMovimientos0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoMovimientos0()
    {
        return $this->hasMany(HistoricoMovimientos::class, ['id_lugar_origen' => 'id']);
    }

    /**
     * Gets query for [[Productos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Productos::class, ['id_lugar' => 'id']);
    }

    /**
     * Gets query for [[Productos0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductos0()
    {
        return $this->hasMany(Productos::class, ['id' => 'id_producto'])->viaTable('stock', ['id_lugar' => 'id']);
    }

    /**
     * Gets query for [[Salidas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalidas()
    {
        return $this->hasMany(Salidas::class, ['id_lugar_destino' => 'id']);
    }

    /**
     * Gets query for [[Salidas0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalidas0()
    {
        return $this->hasMany(Salidas::class, ['id_lugar_origen' => 'id']);
    }

    /**
     * Gets query for [[Stocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::class, ['id_lugar' => 'id']);
    }

}
