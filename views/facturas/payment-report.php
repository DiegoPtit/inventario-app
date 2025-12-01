<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Facturas $model */
/** @var app\models\ItemsFactura[] $items */
/** @var float $totalPagado */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = 'Factura: ' . $model->codigo;

// Obtener tasas de cambio si no fueron pasadas
if (!isset($precioParalelo)) {
    $precioParalelo = \app\models\HistoricoPreciosDolar::find()
        ->where(['tipo' => 'PARALELO'])
        ->orderBy(['created_at' => SORT_DESC])
        ->one();
}

if (!isset($precioOficial)) {
    $precioOficial = \app\models\HistoricoPreciosDolar::find()
        ->where(['tipo' => 'OFICIAL'])
        ->orderBy(['created_at' => SORT_DESC])
        ->one();
}

// Obtener moneda seleccionada (default: la moneda de la factura)
$currency = Yii::$app->request->get('currency', strtolower($model->currency));

// Función para convertir montos entre diferentes monedas
function convertAmount($amount, $fromCurrency, $toCurrency, $precioParalelo, $precioOficial) {
    if (!$precioParalelo || !$precioOficial) {
        return [
            'value' => $amount, 
            'symbol' => '$', 
            'formatted' => '$' . number_format($amount, 2),
            'label' => strtoupper($fromCurrency)
        ];
    }
    
    $value = floatval($amount);
    
    // Normalizar los nombres de moneda a mayúsculas
    $fromCurrency = strtoupper($fromCurrency);
    $toCurrency = strtoupper($toCurrency);
    
    // Si son la misma moneda, devolver sin conversión
    if ($fromCurrency === $toCurrency) {
        $symbol = ($toCurrency === 'VES') ? 'Bs.' : '$';
        $formatted = ($toCurrency === 'VES') 
            ? 'Bs. ' . number_format($value, 2, ',', '.')
            : '$' . number_format($value, 2);
        
        return [
            'value' => $value,
            'symbol' => $symbol,
            'formatted' => $formatted,
            'label' => $toCurrency
        ];
    }
    
    // Convertir de la moneda origen a VES (moneda base)
    $amountInVES = 0;
    if ($fromCurrency === 'USDT') {
        $amountInVES = $value * $precioParalelo->precio_ves;
    } elseif ($fromCurrency === 'BCV') {
        $amountInVES = $value * $precioOficial->precio_ves;
    } elseif ($fromCurrency === 'VES') {
        $amountInVES = $value;
    }
    
    // Convertir de VES a la moneda destino
    $convertedValue = 0;
    $symbol = '$';
    $label = 'USDT';
    
    if ($toCurrency === 'USDT') {
        $convertedValue = $amountInVES / $precioParalelo->precio_ves;
        $symbol = '$';
        $label = 'USDT';
    } elseif ($toCurrency === 'BCV') {
        $convertedValue = $amountInVES / $precioOficial->precio_ves;
        $symbol = '$';
        $label = 'USD BCV';
    } elseif ($toCurrency === 'VES') {
        $convertedValue = $amountInVES;
        $symbol = 'Bs.';
        $label = 'Bolívares';
    }
    
    $formatted = ($toCurrency === 'VES') 
        ? 'Bs. ' . number_format($convertedValue, 2, ',', '.')
        : '$' . number_format($convertedValue, 2);
    
    return [
        'value' => $convertedValue,
        'symbol' => $symbol,
        'formatted' => $formatted,
        'label' => $label
    ];
}

// Convertir totales (estos están en la moneda de la factura)
$montoTotal = convertAmount($model->monto_final, $model->currency, $currency, $precioParalelo, $precioOficial);
$montoPagado = convertAmount($totalPagado, $model->currency, $currency, $precioParalelo, $precioOficial);
$montoCalculado = convertAmount($model->monto_calculado, 'USDT', $currency, $precioParalelo, $precioOficial);
$saldoPendiente = $montoTotal['value'] - $montoPagado['value'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ecf0f1;
            padding: 40px 20px;
            line-height: 1.6;
        }
        
        /* A4 Page Container */
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            position: relative;
        }
        
        /* Print Button - Hidden on print */
        .print-button-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.3);
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 4px solid #2c3e50;
        }
        
        .company-info h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-info .tagline {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }
        
        .invoice-code {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin: 5px 0 0 0;
        }
        
        /* Info Section */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .info-block {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
        }
        
        .info-block-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #7f8c8d;
            margin: 0 0 15px 0;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 8px;
        }
        
        .info-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        
        .info-line:last-child {
            margin-bottom: 0;
        }
        
        .info-line-label {
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .info-line-value {
            color: #2c3e50;
            font-weight: 600;
            text-align: right;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.solvente {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.moroso {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background: #2c3e50;
            color: #fff;
        }
        
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .items-table td {
            padding: 12px 10px;
            font-size: 0.9rem;
            color: #2c3e50;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        /* Totals Section */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .totals-box {
            min-width: 400px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            font-size: 1rem;
        }
        
        .total-row.subtotal {
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .total-row.paid {
            background: #d4edda;
            color: #155724;
            font-weight: 600;
        }
        
        .total-row.final {
            background: #2c3e50;
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 18px 20px;
        }
        
        .total-label {
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .total-value {
            font-weight: 700;
        }
        
        /* Conversions */
        .conversions {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .conversion-line {
            margin-bottom: 5px;
        }
        
        .conversion-line:last-child {
            margin-bottom: 0;
        }
        
        .conversion-line strong {
            color: #2c3e50;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.85rem;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            
            .a4-container {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
            }
            
            .print-button-wrapper {
                display: none !important;
            }
            
            .invoice-header {
                page-break-after: avoid;
            }
            
            .items-table {
                page-break-inside: avoid;
            }
            
            .items-table thead {
                display: table-header-group;
            }
            
            .totals-section {
                page-break-inside: avoid;
            }
        }
        
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="print-button-wrapper">
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer"></i> Imprimir Factura
        </button>
    </div>
    
    <!-- A4 Container -->
    <div class="a4-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>Hava Inventario</h1>
                <p class="tagline">Salud & Bienestar</p>
            </div>
            <div class="invoice-info">
                <h2>FACTURA</h2>
                <p class="invoice-code"><?= Html::encode($model->codigo) ?></p>
                <!--<p style="font-size: 0.85rem; margin-top: 5px; color: #7f8c8d;">
                    Moneda: <strong><?= isset($montoTotal['label']) ? $montoTotal['label'] : 'USDT' ?></strong>
                </p>--> <!-- YA NO -->
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="info-section">
            <!-- Cliente -->
            <div class="info-block">
                <h3 class="info-block-title">Cliente</h3>
                <?php if ($model->cliente): ?>
                    <div class="info-line">
                        <span class="info-line-label">Nombre:</span>
                        <span class="info-line-value"><?= Html::encode($model->cliente->nombre) ?></span>
                    </div>
                    <?php if ($model->cliente->documento_identidad): ?>
                    <div class="info-line">
                        <span class="info-line-label">Documento:</span>
                        <span class="info-line-value"><?= Html::encode($model->cliente->documento_identidad) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($model->cliente->telefono): ?>
                    <div class="info-line">
                        <span class="info-line-label">Teléfono:</span>
                        <span class="info-line-value"><?= Html::encode($model->cliente->telefono) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-line">
                        <span class="info-line-label">Status:</span>
                        <span class="info-line-value">
                            <span class="status-badge <?= $model->cliente->isStatusSolvente() ? 'solvente' : 'moroso' ?>">
                                <?= $model->cliente->isStatusSolvente() ? 'Solvente' : 'Moroso' ?>
                            </span>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="info-line">
                        <span class="info-line-value">Sin cliente asociado</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Detalles -->
            <div class="info-block">
                <h3 class="info-block-title">Detalles de Factura</h3>
                <div class="info-line">
                    <span class="info-line-label">Fecha:</span>
                    <span class="info-line-value"><?= Yii::$app->formatter->asDate($model->fecha) ?></span>
                </div>
                <?php if ($model->concepto): ?>
                <div class="info-line">
                    <span class="info-line-label">Concepto:</span>
                    <span class="info-line-value"><?= Html::encode($model->concepto) ?></span>
                </div>
                <?php endif; ?>
                <div class="info-line">
                    <span class="info-line-label">Fecha de Creación:</span>
                    <span class="info-line-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
              <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php $index = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td>
                                <?php if ($item->producto): ?>
                                    <?php 
                                    $nombreProducto = trim(implode(' ', array_filter([
                                        $item->producto->marca,
                                        $item->producto->modelo,
                                        $item->producto->color
                                    ])));
                                    if (empty($nombreProducto)) {
                                        $nombreProducto = 'Producto #' . $item->id_producto;
                                    }
                                    ?>
                                    <?= Html::encode($nombreProducto) ?>
                                <?php else: ?>
                                    Producto #<?= $item->id_producto ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $item->cantidad ?></td>
                            <td class="text-right">
                                <?php
                                // Productos siempre están en USDT
                                $precioConvertido = convertAmount($item->precio_unitario, 'USDT', $currency, $precioParalelo, $precioOficial);
                                echo $precioConvertido['formatted'];
                                
                                // Añadir indicador si se convierte desde USDT (NI SE NOS OCURRA MENCIONAR USDT, ES ILEGAL)
                                /*if (strtoupper($currency) !== 'USDT') {
                                    echo '<br><small style="color: #7f8c8d; font-size: 0.7rem;">(Conversión desde USDT)</small>';
                                }
                                */
                                ?>
                            </td>
                            <td class="text-right">
                                <?php
                                $subtotal = $item->subtotal ?? ($item->cantidad * $item->precio_unitario);
                                // Subtotales también están en USDT
                                $subtotalConvertido = convertAmount($subtotal, 'USDT', $currency, $precioParalelo, $precioOficial);
                                echo $subtotalConvertido['formatted'];
                                
                                // Añadir indicador si se convierte desde USDT (NI SE NOS OCURRA MENCIONAR USDT, ES ILEGAL)
                                /*if (strtoupper($currency) !== 'USDT') {
                                    echo '<br><small style="color: #7f8c8d; font-size: 0.7rem;">(Conversión desde USDT)</small>';
                                }
                                */
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #7f8c8d;">
                            No hay productos en esta factura
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row subtotal">
                    <span class="total-label">Monto Calculado:</span>
                    <span class="total-value"><?= $montoCalculado['formatted'] ?></span>
                </div>
                <div class="total-row paid">
                    <span class="total-label">Pagado:</span>
                    <span  class="total-value"><?= $montoPagado['formatted'] ?></span>
                </div>
                <div class="total-row final">
                    <span class="total-label">TOTAL:</span>
                    <span class="total-value"><?= $montoTotal['formatted'] ?></span>
                </div>
                <?php if ($saldoPendiente > 0): ?>
                <div class="total-row" style="background: #fff3cd; color: #856404; padding: 12px 20px;">
                    <span class="total-label">Saldo Pendiente:</span>
                    <span class="total-value">
                        <?= $montoTotal['symbol'] . ' ' . number_format($saldoPendiente, 2, $currency === 'ves' ? ',' : '.', $currency === 'ves' ? '.' : ',') ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Additional Info (if needed) -->
        <!--<?php if ($precioParalelo && $precioOficial && $currency !== 'usdt'): ?>
            <div class="conversions">
                <div class="conversion-line">
                    <strong>Nota:</strong> Todos los montos están expresados en <strong><?= $montoTotal['label'] ?></strong>
                </div>
                <div class="conversion-line">
                    • Tasa Paralelo: Bs. <?= number_format($precioParalelo->precio_ves, 2, ',', '.') ?> por USDT
                </div>
                <div class="conversion-line">
                    • Tasa BCV: Bs. <?= number_format($precioOficial->precio_ves, 2, ',', '.') ?> por USD
                </div>
            </div>
        <?php endif; ?>--> <!-- ESTO YA NO! -->
        
        <!-- Footer -->
        <div class="invoice-footer">
            <p>Gracias por su preferencia</p>
            <p>Hava Inventario - Salud & Bienestar</p>
        </div>
    </div>
</body>
</html>
