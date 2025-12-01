<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "historico_cobros".
 *
 * @property int $id
 * @property int $id_cliente
 * @property int|null $id_factura
 * @property string $fecha
 * @property float $monto
 * @property string $currency
 * @property string|null $metodo_pago
 * @property string|null $nota
 *
 * @property Clientes $cliente
 * @property Facturas $factura
 */
class HistoricoCobros extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historico_cobros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_factura', 'metodo_pago', 'nota'], 'default', 'value' => null],
            [['currency'], 'default', 'value' => self::USDT],
            [['monto'], 'default', 'value' => 0.00],
            [['id_cliente'], 'required'],
            [['id_cliente', 'id_factura'], 'integer'],
            [['fecha'], 'safe'],
            [['monto'], 'number'],
            [['currency'], 'in', 'range' => [self::USDT, self::BCV, self::VES]],
            [['nota'], 'string'],
            [['metodo_pago'], 'string', 'max' => 100],
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::class, 'targetAttribute' => ['id_cliente' => 'id']],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::class, 'targetAttribute' => ['id_factura' => 'id']],
        ];
    }

    /**
     * ENUM field values
     */
    const USDT = 'USDT';
    const BCV = 'BCV';
    const VES = 'VES';

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_cliente' => Yii::t('app', 'Id Cliente'),
            'id_factura' => Yii::t('app', 'Id Factura'),
            'fecha' => Yii::t('app', 'Fecha'),
            'monto' => Yii::t('app', 'Monto'),
            'currency' => Yii::t('app', 'Moneda'),
            'metodo_pago' => Yii::t('app', 'Metodo Pago'),
            'nota' => Yii::t('app', 'Nota'),
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
     * column currency ENUM value labels
     * @return string[]
     */
    public static function optsCurrency()
    {
        return [
            self::USDT => Yii::t('app', 'USDT'),
            self::BCV => Yii::t('app', 'BCV'),
            self::VES => Yii::t('app', 'VES'),
        ];
    }

    //ESTOS SON LOS METODOS PARA TRABAJAR CON EL ENUM

        /**
         * @return string
         */
        public function displayCurrency()
        {
            return self::optsCurrency()[$this->currency];
        }

        /**
         * @return bool
         */
        public function isCurrencyUSDT()
        {
            return $this->currency === self::USDT;
        }

        public function setCurrencyToUSDT()
        {
            $this->currency = self::USDT;
        }

        /**
         * @return bool
         */
        public function isCurrencyBCV()
        {
            return $this->currency === self::BCV;
        }

        public function setCurrencyToBCV()
        {
            $this->currency = self::BCV;
        }

        /**
         * @return bool
         */
        public function isCurrencyVES()
        {
            return $this->currency === self::VES;
        }

        public function setCurrencyToVES()
        {
            $this->currency = self::VES;
        }

    //-------------------------------------------------------|

    /**
     * Gets query for [[Factura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturas::class, ['id' => 'id_factura']);
    }

}
