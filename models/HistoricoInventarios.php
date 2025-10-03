<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "historico_inventarios".
 *
 * @property int $id
 * @property string $fecha_inicio
 * @property string $fecha_cierre
 * @property int $cantidad_productos
 * @property float $valor
 * @property string|null $nota
 * @property string $created_at
 */
class HistoricoInventarios extends \yii\db\ActiveRecord
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
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historico_inventarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nota'], 'default', 'value' => null],
            [['fecha_inicio', 'fecha_cierre', 'cantidad_productos', 'valor'], 'required'],
            [['fecha_inicio', 'fecha_cierre', 'created_at'], 'safe'],
            [['cantidad_productos'], 'integer'],
            [['valor'], 'number'],
            [['nota'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fecha_inicio' => Yii::t('app', 'Fecha Inicio'),
            'fecha_cierre' => Yii::t('app', 'Fecha Cierre'),
            'cantidad_productos' => Yii::t('app', 'Cantidad Productos'),
            'valor' => Yii::t('app', 'Valor'),
            'nota' => Yii::t('app', 'Nota'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

}
