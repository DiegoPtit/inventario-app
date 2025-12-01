<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proveedores".
 *
 * @property int $id
 * @property string $razon_social
 * @property string|null $documento_identificacion
 * @property string|null $ciudad
 * @property string|null $pais
 * @property string|null $telefono
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Entradas[] $entradas
 */
class Proveedores extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proveedores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documento_identificacion', 'ciudad', 'pais', 'telefono'], 'default', 'value' => null],
            [['razon_social'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['razon_social'], 'string', 'max' => 255],
            [['documento_identificacion'], 'string', 'max' => 100],
            [['ciudad'], 'string', 'max' => 120],
            [['pais'], 'string', 'max' => 80],
            [['telefono'], 'string', 'max' => 50],
            [['documento_identificacion'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'razon_social' => Yii::t('app', 'Razon Social'),
            'documento_identificacion' => Yii::t('app', 'Documento Identificacion'),
            'ciudad' => Yii::t('app', 'Ciudad'),
            'pais' => Yii::t('app', 'Pais'),
            'telefono' => Yii::t('app', 'Telefono'),
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
        return $this->hasMany(Entradas::class, ['id_proveedor' => 'id']);
    }

}
