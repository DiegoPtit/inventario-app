<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "historico_movimientos".
 *
 * @property int $id
 * @property int $id_producto
 * @property string $accion
 * @property int|null $id_lugar_origen
 * @property int|null $id_lugar_destino
 * @property int $cantidad
 * @property int|null $referencia_id
 * @property string $created_at
 *
 * @property Lugares $lugarDestino
 * @property Lugares $lugarOrigen
 * @property Productos $producto
 */
class HistoricoMovimientos extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACCION_ENTRADA = 'ENTRADA';
    const ACCION_SALIDA = 'SALIDA';
    const ACCION_VENTA = 'VENTA';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historico_movimientos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_lugar_origen', 'id_lugar_destino', 'referencia_id'], 'default', 'value' => null],
            [['id_producto', 'accion', 'cantidad'], 'required'],
            [['id_producto', 'id_lugar_origen', 'id_lugar_destino', 'cantidad', 'referencia_id'], 'integer'],
            [['accion'], 'string'],
            [['created_at'], 'safe'],
            ['accion', 'in', 'range' => array_keys(self::optsAccion())],
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
            'accion' => Yii::t('app', 'Accion'),
            'id_lugar_origen' => Yii::t('app', 'Id Lugar Origen'),
            'id_lugar_destino' => Yii::t('app', 'Id Lugar Destino'),
            'cantidad' => Yii::t('app', 'Cantidad'),
            'referencia_id' => Yii::t('app', 'Referencia ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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

    /**
     * Gets query for [[Factura]].
     * Se usa cuando accion es VENTA y referencia_id contiene el id de la factura
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturas::class, ['id' => 'referencia_id']);
    }


    /**
     * column accion ENUM value labels
     * @return string[]
     */
    public static function optsAccion()
    {
        return [
            self::ACCION_ENTRADA => Yii::t('app', 'ENTRADA'),
            self::ACCION_SALIDA => Yii::t('app', 'SALIDA'),
            self::ACCION_VENTA => Yii::t('app', 'VENTA'),
        ];
    }

    /**
     * @return string
     */
    public function displayAccion()
    {
        return self::optsAccion()[$this->accion];
    }

    /**
     * @return bool
     */
    public function isAccionEntrada()
    {
        return $this->accion === self::ACCION_ENTRADA;
    }

    public function setAccionToEntrada()
    {
        $this->accion = self::ACCION_ENTRADA;
    }

    /**
     * @return bool
     */
    public function isAccionSalida()
    {
        return $this->accion === self::ACCION_SALIDA;
    }

    public function setAccionToSalida()
    {
        $this->accion = self::ACCION_SALIDA;
    }

    /**
     * @return bool
     */
    public function isAccionVenta()
    {
        return $this->accion === self::ACCION_VENTA;
    }

    public function setAccionToVenta()
    {
        $this->accion = self::ACCION_VENTA;
    }
}
