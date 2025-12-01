<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productos".
 *
 * @property int $id
 * @property string|null $marca
 * @property string|null $modelo
 * @property string|null $color
 * @property string|null $descripcion
 * @property float|null $contenido_neto
 * @property string|null $unidad_medida
 * @property float $costo
 * @property float $precio_venta
 * @property string|null $codigo_barra
 * @property int|null $id_lugar
 * @property int|null $id_categoria
 * @property string|null $fotos
 * @property string|null $sku
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Categorias $categoria
 * @property Entradas[] $entradas
 * @property HistoricoMovimientos[] $historicoMovimientos
 * @property ItemsFactura[] $itemsFacturas
 * @property Lugares $lugar
 * @property Lugares[] $lugars
 * @property Salidas[] $salidas
 * @property Stock[] $stocks
 */
class Productos extends \yii\db\ActiveRecord
{
    // Propiedades adicionales para el formulario
    public $imageFiles;
    public $cantidad; // Para crear la entrada inicial

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['marca', 'modelo', 'color', 'descripcion', 'contenido_neto', 'unidad_medida', 'codigo_barra', 'id_lugar', 'id_categoria', 'fotos', 'sku'], 'default', 'value' => null],
            [['precio_venta', 'costo'], 'default', 'value' => 0.00],
            [['descripcion'], 'string'],
            [['contenido_neto', 'costo', 'precio_venta'], 'number'],
            [['id_lugar', 'id_categoria', 'cantidad'], 'integer'],
            [['fotos', 'created_at', 'updated_at'], 'safe'],
            [['marca', 'modelo'], 'string', 'max' => 150],
            [['color'], 'string', 'max' => 80],
            [['unidad_medida'], 'string', 'max' => 50],
            [['codigo_barra'], 'string', 'max' => 128],
            [['sku'], 'string', 'max' => 120],
            [['codigo_barra'], 'unique'],
            [['id_categoria'], 'exist', 'skipOnError' => true, 'targetClass' => Categorias::class, 'targetAttribute' => ['id_categoria' => 'id']],
            [['id_lugar'], 'exist', 'skipOnError' => true, 'targetClass' => Lugares::class, 'targetAttribute' => ['id_lugar' => 'id']],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'skipOnError' => true, 'extensions' => 'png, jpg, jpeg, gif, webp', 'maxFiles' => 10, 'checkExtensionByMimeType' => false],
            [['cantidad'], 'integer', 'min' => 0],
            [['cantidad'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'marca' => Yii::t('app', 'Marca'),
            'modelo' => Yii::t('app', 'Modelo'),
            'color' => Yii::t('app', 'Color'),
            'descripcion' => Yii::t('app', 'Descripción'),
            'contenido_neto' => Yii::t('app', 'Contenido Neto'),
            'unidad_medida' => Yii::t('app', 'Unidad de Medida'),
            'costo' => Yii::t('app', 'Costo'),
            'precio_venta' => Yii::t('app', 'Precio de Venta'),
            'codigo_barra' => Yii::t('app', 'Código de Barra'),
            'id_lugar' => Yii::t('app', 'Lugar de Almacén'),
            'id_categoria' => Yii::t('app', 'Categoría'),
            'fotos' => Yii::t('app', 'Fotos'),
            'imageFiles' => Yii::t('app', 'Fotos del Producto'),
            'sku' => Yii::t('app', 'SKU'),
            'cantidad' => Yii::t('app', 'Cantidad Inicial'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * Sube las imágenes y devuelve un array con las rutas
     * @return array|bool
     */
    public function uploadImages()
    {
        // Verificar que imageFiles sea un array y no esté vacío
        if ($this->imageFiles && is_array($this->imageFiles) && count($this->imageFiles) > 0) {
            $uploadPath = Yii::getAlias('@webroot/uploads/');
            
            // Crear el directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $fotosArray = [];
            
            // Si ya hay fotos previas, las mantenemos
            if (!empty($this->fotos)) {
                $fotosExistentes = json_decode($this->fotos, true);
                if (is_array($fotosExistentes)) {
                    $fotosArray = $fotosExistentes;
                }
            }
            
            foreach ($this->imageFiles as $file) {
                // Verificar que el elemento sea un objeto UploadedFile
                if ($file instanceof \yii\web\UploadedFile) {
                    $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
                    $filePath = $uploadPath . $fileName;
                    
                    if ($file->saveAs($filePath)) {
                        $fotosArray[] = 'uploads/' . $fileName;
                    }
                }
            }
            
            return $fotosArray;
        }
        
        return false;
    }

    /**
     * Gets query for [[Categoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categorias::class, ['id' => 'id_categoria']);
    }

    /**
     * Gets query for [[Entradas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntradas()
    {
        return $this->hasMany(Entradas::class, ['id_producto' => 'id']);
    }

    /**
     * Gets query for [[HistoricoMovimientos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoMovimientos()
    {
        return $this->hasMany(HistoricoMovimientos::class, ['id_producto' => 'id']);
    }

    /**
     * Gets query for [[ItemsFacturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemsFacturas()
    {
        return $this->hasMany(ItemsFactura::class, ['id_producto' => 'id']);
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
     * Gets query for [[Lugars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLugars()
    {
        return $this->hasMany(Lugares::class, ['id' => 'id_lugar'])->viaTable('stock', ['id_producto' => 'id']);
    }

    /**
     * Gets query for [[Salidas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalidas()
    {
        return $this->hasMany(Salidas::class, ['id_producto' => 'id']);
    }

    /**
     * Gets query for [[Stocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::class, ['id_producto' => 'id']);
    }

}
