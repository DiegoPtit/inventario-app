<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "historico_precios_dolar".
 *
 * @property int $id
 * @property float $precio_ves
 * @property string $tipo
 * @property string $created_at
 */
class HistoricoPreciosDolar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false, // No updated_at field
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * ENUM field values
     */
    const TIPO_OFICIAL = 'OFICIAL';
    const TIPO_PARALELO = 'PARALELO';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historico_precios_dolar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['precio_ves', 'tipo'], 'required'],
            [['precio_ves'], 'number'],
            [['tipo'], 'string'],
            [['created_at'], 'safe'],
            ['tipo', 'in', 'range' => array_keys(self::optsTipo())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'precio_ves' => Yii::t('app', 'Precio Ves'),
            'tipo' => Yii::t('app', 'Tipo'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }


    /**
     * column tipo ENUM value labels
     * @return string[]
     */
    public static function optsTipo()
    {
        return [
            self::TIPO_OFICIAL => Yii::t('app', 'OFICIAL'),
            self::TIPO_PARALELO => Yii::t('app', 'PARALELO'),
        ];
    }

    /**
     * @return string
     */
    public function displayTipo()
    {
        return self::optsTipo()[$this->tipo];
    }

    /**
     * @return bool
     */
    public function isTipoOficial()
    {
        return $this->tipo === self::TIPO_OFICIAL;
    }

    public function setTipoToOficial()
    {
        $this->tipo = self::TIPO_OFICIAL;
    }

    /**
     * @return bool
     */
    public function isTipoParalelo()
    {
        return $this->tipo === self::TIPO_PARALELO;
    }

    public function setTipoToParalelo()
    {
        $this->tipo = self::TIPO_PARALELO;
    }
}
