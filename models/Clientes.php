<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string|null $documento_identidad
 * @property string $nombre
 * @property string|null $ubicacion
 * @property string|null $telefono
 * @property int|null $edad
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Facturas[] $facturas
 * @property HistoricoCobros[] $historicoCobros
 * @property Salidas[] $salidas
 */
class Clientes extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_MOROSO = 'Moroso';
    const STATUS_SOLVENTE = 'Solvente';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documento_identidad', 'ubicacion', 'telefono', 'edad'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'Solvente'],
            [['nombre'], 'required'],
            [['edad'], 'integer'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['documento_identidad'], 'string', 'max' => 100],
            [['nombre', 'ubicacion'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 80],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['documento_identidad'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'documento_identidad' => Yii::t('app', 'Documento Identidad'),
            'nombre' => Yii::t('app', 'Nombre'),
            'ubicacion' => Yii::t('app', 'Ubicacion'),
            'telefono' => Yii::t('app', 'Telefono'),
            'edad' => Yii::t('app', 'Edad'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Facturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::class, ['id_cliente' => 'id']);
    }

    /**
     * Gets query for [[HistoricoCobros]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoCobros()
    {
        return $this->hasMany(HistoricoCobros::class, ['id_cliente' => 'id']);
    }

    /**
     * Gets query for [[Salidas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalidas()
    {
        return $this->hasMany(Salidas::class, ['id_cliente' => 'id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_MOROSO => Yii::t('app', 'Moroso'),
            self::STATUS_SOLVENTE => Yii::t('app', 'Solvente'),
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusMoroso()
    {
        return $this->status === self::STATUS_MOROSO;
    }

    public function setStatusToMoroso()
    {
        $this->status = self::STATUS_MOROSO;
    }

    /**
     * @return bool
     */
    public function isStatusSolvente()
    {
        return $this->status === self::STATUS_SOLVENTE;
    }

    public function setStatusToSolvente()
    {
        $this->status = self::STATUS_SOLVENTE;
    }

    /**
 * Evalúa todas las facturas del cliente y actualiza su status automáticamente.
 * El cliente será 'Solvente' solo si TODAS sus facturas están completamente pagadas.
 * @return bool true si se guardó el cambio correctamente
 */
public function evaluarYActualizarStatus()
{
    // Obtener todas las facturas del cliente
    $facturas = $this->getFacturas()
        ->with(['historicoCobros'])
        ->all();
    
    // Si no tiene facturas, es solvente
    if (empty($facturas)) {
        if ($this->isStatusMoroso()) {
            $this->setStatusToSolvente();
            return $this->save(false);
        }
        return true;
    }
    
    // Obtener precios para conversiones
    $precioOficial = \app\models\HistoricoPreciosDolar::find()
        ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
        ->orderBy(['created_at' => SORT_DESC])
        ->one();
    
    $precioParalelo = \app\models\HistoricoPreciosDolar::find()
        ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
        ->orderBy(['created_at' => SORT_DESC])
        ->one();
    
    // Función helper para convertir entre monedas
    $convertCurrency = function($amount, $fromCurrency, $toCurrency) use ($precioParalelo, $precioOficial) {
        if (!$precioParalelo || !$precioOficial) {
            return $amount;
        }
        
        $value = floatval($amount);
        
        if ($fromCurrency === $toCurrency) {
            return $value;
        }
        
        // Convertir a VES
        $amountInVES = 0;
        if ($fromCurrency === 'USDT') {
            $amountInVES = $value * $precioParalelo->precio_ves;
        } elseif ($fromCurrency === 'BCV') {
            $amountInVES = $value * $precioOficial->precio_ves;
        } elseif ($fromCurrency === 'VES') {
            $amountInVES = $value;
        }
        
        // Convertir de VES a moneda destino
        if ($toCurrency === 'USDT') {
            return $amountInVES / $precioParalelo->precio_ves;
        } elseif ($toCurrency === 'BCV') {
            return $amountInVES / $precioOficial->precio_ves;
        } elseif ($toCurrency === 'VES') {
            return $amountInVES;
        }
        
        return $value;
    };
    
    // Verificar si todas las facturas están completamente pagadas
    $todasPagadas = true;
    
    foreach ($facturas as $factura) {
        // Calcular total cobrado de esta factura - convertir cada cobro a la moneda de la factura
        $totalCobrado = 0;
        foreach ($factura->historicoCobros as $cobro) {
            $totalCobrado += $convertCurrency($cobro->monto, $cobro->currency, $factura->currency);
        }
        
        // Aplicar round para evitar problemas de precisión
        $totalCobrado = round($totalCobrado, 2);
        
        // Si el total cobrado es menor al monto final, la factura está pendiente
        if ($totalCobrado < $factura->monto_final) {
            $todasPagadas = false;
            break;
        }
    }
    
    // Actualizar status según el resultado
    $statusAnterior = $this->status;
    
    if ($todasPagadas) {
        $this->setStatusToSolvente();
    } else {
        $this->setStatusToMoroso();
    }
    
    // Solo guardar si cambió el status
    if ($statusAnterior !== $this->status) {
        return $this->save(false);
    }
    
    return true;
}
}
