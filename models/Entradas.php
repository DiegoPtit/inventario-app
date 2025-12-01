<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entradas".
 *
 * @property int $id
 * @property int $id_producto
 * @property int $cantidad
 * @property int|null $id_proveedor
 * @property string $nro_documento
 * @property string|null $ruta_documento_respaldo
 * @property int $id_lugar
 * @property string $created_at
 *
 * @property Lugares $lugar
 * @property Productos $producto
 * @property Proveedores $proveedor
 */
class Entradas extends \yii\db\ActiveRecord
{
    // Campos virtuales para el formulario
    public $tipo_entrada;
    public $documentFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entradas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_proveedor', 'ruta_documento_respaldo', 'nro_documento'], 'default', 'value' => null],
            [['id_producto', 'cantidad', 'id_lugar', 'tipo_entrada'], 'required'],
            [['id_producto', 'cantidad', 'id_proveedor', 'id_lugar'], 'integer'],
            [['created_at'], 'safe'],
            [['ruta_documento_respaldo'], 'string', 'max' => 512],
            [['nro_documento'], 'string', 'max' => 255],
            [['tipo_entrada'], 'string'],
            [['tipo_entrada'], 'in', 'range' => ['Compra', 'Donación', 'Inventario Inicial']],
            [['id_lugar'], 'exist', 'skipOnError' => true, 'targetClass' => Lugares::class, 'targetAttribute' => ['id_lugar' => 'id']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::class, 'targetAttribute' => ['id_producto' => 'id']],
            [['id_proveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedores::class, 'targetAttribute' => ['id_proveedor' => 'id']],
            [['documentFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, jpg, jpeg, png', 'maxSize' => 1024 * 1024 * 5, 'checkExtensionByMimeType' => false], // 5MB max
            // Validación condicional: si es Compra, requiere proveedor
            [['id_proveedor'], 'required', 'when' => function($model) {
                return $model->tipo_entrada === 'Compra';
            }, 'whenClient' => "function (attribute, value) {
                return $('#entradas-tipo_entrada').val() === 'Compra';
            }"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_producto' => Yii::t('app', 'Producto'),
            'cantidad' => Yii::t('app', 'Cantidad'),
            'id_proveedor' => Yii::t('app', 'Proveedor'),
            'nro_documento' => Yii::t('app', 'Número de Comprobante'),
            'ruta_documento_respaldo' => Yii::t('app', 'Documento de Respaldo'),
            'id_lugar' => Yii::t('app', 'Almacén Destino'),
            'created_at' => Yii::t('app', 'Created At'),
            'tipo_entrada' => Yii::t('app', 'Tipo de Entrada'),
            'documentFile' => Yii::t('app', 'Documento de Respaldo'),
        ];
    }
    
    /**
     * Sube el documento de respaldo y devuelve la ruta
     * @return string|bool
     */
    public function uploadDocument()
    {
        if ($this->documentFile) {
            $uploadPath = Yii::getAlias('@webroot/uploads/');
            
            // Crear el directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $fileName = uniqid() . '_' . $this->documentFile->baseName . '.' . $this->documentFile->extension;
            $filePath = $uploadPath . $fileName;
            
            if ($this->documentFile->saveAs($filePath)) {
                return 'uploads/' . $fileName;
            }
        }
        
        return false;
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

    /**
     * Gets query for [[Proveedor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedores::class, ['id' => 'id_proveedor']);
    }

}
