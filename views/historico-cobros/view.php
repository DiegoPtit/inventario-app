<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\HistoricoCobros $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

// Cargar relaciones necesarias
$cliente = $model->cliente;
$factura = $model->factura;

// Crear título
$titulo = 'Cobro #' . $model->id;
if ($cliente) {
    $titulo = 'Cobro - ' . Html::encode($cliente->nombre);
}

$this->title = $titulo;
\yii\web\YiiAsset::register($this);

// Obtener items de la factura si existe
$itemsFactura = [];
$totalCobrado = 0;
$saldoPendiente = 0;

if ($factura) {
    $itemsFactura = $factura->getItemsFacturas()
        ->with(['producto'])
        ->all();

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
    
    // Función helper para convertir entre monedas
    $convertCurrency = function($amount, $fromCurrency, $toCurrency) use ($precioParalelo, $precioOficial) {
        if (!$precioParalelo || !$precioOficial) {
            return $amount;
        }
        
        $value = floatval($amount);
        
        // Si son la misma moneda, retornar sin conversión
        if ($fromCurrency === $toCurrency) {
            return $value;
        }
        
        // Convertir de fromCurrency a VES (moneda base)
        $amountInVES = 0;
        if ($fromCurrency === 'USDT') {
            $amountInVES = $value * $precioParalelo->precio_ves;
        } elseif ($fromCurrency === 'BCV') {
            $amountInVES = $value * $precioOficial->precio_ves;
        } elseif ($fromCurrency === 'VES') {
            $amountInVES = $value;
        }
        
        // Convertir de VES a toCurrency
        if ($toCurrency === 'USDT') {
            return $amountInVES / $precioParalelo->precio_ves;
        } elseif ($toCurrency === 'BCV') {
            return $amountInVES / $precioOficial->precio_ves;
        } elseif ($toCurrency === 'VES') {
            return $amountInVES;
        }
        
        return $value;
    };
    
    // Calcular total cobrado de esta factura EN LA MONEDA DE LA FACTURA
    $historicoCobros = $factura->getHistoricoCobros()->all();
    foreach ($historicoCobros as $cobro) {
        // Convertir cada cobro a la moneda de la factura
        $totalCobrado += $convertCurrency($cobro->monto, $cobro->currency, $factura->currency);
    }
    
    $totalCobrado = round($totalCobrado, 2);
    $saldoPendiente = round($factura->monto_final - $totalCobrado, 2);
}

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

// Exchange rates
$precioParaleloJs = $precioParalelo ? $precioParalelo->precio_ves : 'null';
$precioOficialJs = $precioOficial ? $precioOficial->precio_ves : 'null';
?>

<style>
.cobro-view {
    max-width: 900px;
    margin: 0 auto;
    padding: 15px;
}

/* Currency Selector */
.currency-selector {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.currency-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.currency-select {
    padding: 8px 15px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #495057;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.currency-select:focus {
    outline: none;
    border-color: #80bdff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
}

/* Header */
.header-cobro {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #6c757d;
}

.header-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;
}

.header-meta {
    font-size: 0.85rem;
    color: #868e96;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.meta-badge {
    background: #e9ecef;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #495057;
}

/* Sections */
.section {
    background: white;
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
}

.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #6c757d;
    font-size: 1.1rem;
}

/* Info Items */
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.85rem;
    color: #868e96;
    font-weight: 500;
}

.info-value {
    font-size: 0.9rem;
    color: #212529;
    font-weight: 600;
    text-align: right;
}

/* Monto destacado */
.monto-destacado {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    margin: 15px 0;
}

.monto-label {
    font-size: 0.85rem;
    color: #868e96;
    margin-bottom: 5px;
}

.monto-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #212529;
}

/* Products Table */
.products-compact {
    margin-top: 15px;
}

.product-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
}

.product-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 5px;
}

.product-meta {
    font-size: 0.8rem;
    color: #868e96;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

/* Summary */
.summary-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 0.9rem;
}

.summary-row.total {
    border-top: 2px solid #dee2e6;
    margin-top: 8px;
    padding-top: 12px;
    font-size: 1.1rem;
    font-weight: 700;
}

.summary-label {
    color: #495057;
}

.summary-value {
    color: #212529;
    font-weight: 600;
}

.saldo-pendiente {
    color: #dc3545;
}

.saldo-cero {
    color: #28a745;
}

/* Nota */
.nota-box {
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
}

.nota-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #856404;
    margin-bottom: 5px;
}

.nota-content {
    font-size: 0.85rem;
    color: #856404;
    line-height: 1.5;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-solvente {
    background: #d4edda;
    color: #155724;
}

.status-moroso {
    background: #f8d7da;
    color: #721c24;
}

/* Actions */
.actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-compact {
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 600;
    border: 2px solid;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    flex: 1;
    justify-content: center;
    min-width: 120px;
}

.btn-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background: white;
}

.btn-secondary:hover {
    background: #6c757d;
    color: white;
}

.btn-primary {
    color: #0d6efd;
    border-color: #0d6efd;
    background: white;
}

.btn-primary:hover {
    background: #0d6efd;
    color: white;
}

.btn-danger {
    color: #dc3545;
    border-color: #dc3545;
    background: white;
}

.btn-danger:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .currency-selector {
        flex-direction: column;
        align-items: stretch;
    }
    
    .currency-select {
        width: 100%;
    }
    
    .header-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .product-meta {
        flex-direction: column;
        gap: 5px;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn-compact {
        width: 100%;
    }
}
</style>

<div class="cobro-view">
    
    <!-- Currency Selector -->
    <div class="currency-selector">
        <span class="currency-label">
            <i class="bi bi-currency-exchange"></i> Ver montos en:
        </span>
        <select id="currency-selector" class="currency-select">
            <option value="usdt" <?= strtolower($model->currency) === 'usdt' ? 'selected' : '' ?>>USDT (Dólar)</option>
            <option value="bcv" <?= strtolower($model->currency) === 'bcv' ? 'selected' : '' ?>>USD BCV (Tasa BCV)</option>
            <option value="ves" <?= strtolower($model->currency) === 'ves' ? 'selected' : '' ?>>Bolívares (Paralelo)</option>
        </select>
    </div>

    <!-- Header -->
    <div class="header-cobro">
        <div class="header-title">
            <i class="bi bi-cash-coin"></i> <?= Html::encode($titulo) ?>
        </div>
        <div class="header-meta">
            <span><i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDate($model->fecha) ?></span>
            <?php if ($model->metodo_pago): ?>
                <span class="meta-badge">
                    <i class="bi bi-credit-card"></i> <?= Html::encode($model->metodo_pago) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cliente Info -->
    <?php if ($cliente): ?>
    <div class="section">
        <div class="section-title">
            <i class="bi bi-person-circle"></i> Información del Cliente
        </div>
        <div class="info-row">
            <span class="info-label">Nombre</span>
            <span class="info-value"><?= Html::encode($cliente-> nombre) ?></span>
        </div>
        <?php if ($cliente->documento_identidad): ?>
        <div class="info-row">
            <span class="info-label">Documento</span>
            <span class="info-value"><?= Html::encode($cliente->documento_identidad) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($cliente->telefono): ?>
        <div class="info-row">
            <span class="info-label">Teléfono</span>
            <span class="info-value"><?= Html::encode($cliente->telefono) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value">
                <span class="status-badge <?= $cliente->isStatusSolvente() ? 'status-solvente' : 'status-moroso' ?>">
                    <?= Html::encode($cliente->displayStatus()) ?>
                </span>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detalles del Cobro -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-receipt"></i> Detalles del Cobro
        </div>
        <div class="info-row">
            <span class="info-label">ID Cobro</span>
            <span class="info-value">#<?= $model->id ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Pago</span>
            <span class="info-value"><?= Yii::$app->formatter->asDate($model->fecha) ?></span>
        </div>
        
        <div class="monto-destacado">
            <div class="monto-label">Monto Pagado</div>
            <div class="monto-value" id="monto-pagado" 
                 data-amount="<?= $model->monto ?>" 
                 data-currency="<?= $model->currency ?>">
                <?php
                $symbol = ($model->currency === 'VES') ? 'Bs. ' : '$';
                $formatted = ($model->currency === 'VES') 
                    ? 'Bs. ' . number_format($model->monto, 2, ',', '.')
                    : '$' . number_format($model->monto, 2);
                echo $formatted;
                ?>
            </div>
            <div style="font-size: 0.75rem; color: #6c757d; margin-top: 5px;">
                <i class="bi bi-currency-exchange"></i> Moneda: <strong><?= $model->displayCurrency() ?></strong>
            </div>
        </div>
        
        <?php if ($model->nota): ?>
        <div class="nota-box">
            <div class="nota-title"><i class="bi bi-sticky"></i> Nota</div>
            <div class="nota-content"><?= Html::encode($model->nota) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Factura Asociada -->
    <?php if ($factura): ?>
    <div class="section">
        <div class="section-title">
            <i class="bi bi-file-text"></i> Factura: <?= Html::encode($factura->codigo) ?>
        </div>
        <div class="info-row">
            <span class="info-label">Código</span>
            <span class="info-value"><?= Html::encode($factura->codigo) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha</span>
            <span class="info-value"><?= Yii::$app->formatter->asDate($factura->fecha) ?></span>
        </div>
        <?php if ($factura->concepto): ?>
        <div class="info-row">
            <span class="info-label">Concepto</span>
            <span class="info-value"><?= Html::encode($factura->concepto) ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Products -->
        <?php if (!empty($itemsFactura)): ?>
        <div class="products-compact">
            <strong style="font-size: 0.9rem; color: #495057; display: block; margin-bottom: 10px;">
                <i class="bi bi-box-seam"></i> Productos
            </strong>
            <?php foreach ($itemsFactura as $item): ?>
            <div class="product-item">
                <div class="product-name">
                    <?php if ($item->producto): ?>
                        <?php
                        $nombreProducto = implode(' - ', array_filter([
                            $item->producto->marca,
                            $item->producto->modelo,
                            $item->producto->color
                        ]));
                        if (empty($nombreProducto)) {
                            $nombreProducto = 'Producto #' . $item->producto->id;
                        }
                        ?>
                        <?= Html::encode($nombreProducto) ?>
                    <?php else: ?>
                        Producto no disponible
                    <?php endif; ?>
                </div>
                <div class="product-meta">
                    <span>Cant: <?= $item->cantidad ?></span>
                    <span class="precio-unitario" 
                          data-amount="<?= $item->precio_unitario ?>" 
                          data-currency="USDT">
                        Precio: $<?= number_format($item->precio_unitario, 2) ?>
                    </span>
                    <span class="subtotal-item" 
                          data-amount="<?= $item->subtotal ?>" 
                          data-currency="USDT">
                        Subtotal: $<?= number_format($item->subtotal, 2) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Monto Calculado:</span>
                <span class="summary-value monto-calculado" 
                      data-amount="<?= $factura->monto_calculado ?>" 
                      data-currency="USDT">
                    $<?= number_format($factura->monto_calculado, 2) ?>
                </span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Monto Total:</span>
                <span class="summary-value monto-total" 
                      data-amount="<?= $factura->monto_final ?>" 
                      data-currency="<?= $factura->currency ?>">
                    <?php
                    $symbol = ($factura->currency === 'VES') ? 'Bs. ' : '$';
                    $formatted = ($factura->currency === 'VES') 
                        ? 'Bs. ' . number_format($factura->monto_final, 2, ',', '.')
                        : '$' . number_format($factura->monto_final, 2);
                    echo $formatted;
                    ?>
                </span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Cobrado:</span>
                <span class="summary-value total-cobrado" 
                      data-amount="<?= $totalCobrado ?>" 
                      data-currency="<?= $factura->currency ?>">
                    <?php
                    $formatted = ($factura->currency === 'VES') 
                        ? 'Bs. ' . number_format($totalCobrado, 2, ',', '.')
                        : '$' . number_format($totalCobrado, 2);
                    echo $formatted;
                    ?>
                </span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">Saldo Pendiente:</span>
                <span class="summary-value saldo-pendiente-value <?= $saldoPendiente > 0 ? 'saldo-pendiente' : 'saldo-cero' ?>" 
                      data-amount="<?= $saldoPendiente ?>" 
                      data-currency="<?= $factura->currency ?>">
                    <?php
                    $formatted = ($factura->currency === 'VES') 
                        ? 'Bs. ' . number_format($saldoPendiente, 2, ',', '.')
                        : '$' . number_format($saldoPendiente, 2);
                    echo $formatted;
                    ?>
                </span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="section" style="text-align: center; padding: 30px; color: #868e96;">
        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
        <p>Este cobro no está asociado a ninguna factura.</p>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="actions">
        <a href="<?= Url::to(['index']) ?>" class="btn-compact btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn-compact btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <?= Html::a('<i class="bi bi-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn-compact btn-danger',
            'data' => [
                'confirm' => '¿Está seguro de eliminar este cobro?',
                'method' => 'post',
            ],
        ]) ?>
    </div>

</div>

<?php
$js = <<<JS
const precioParalelo = {$precioParaleloJs};
const precioOficial = {$precioOficialJs};

// Función para convertir entre monedas
function convertCurrency(amount, fromCurrency, toCurrency) {
    if (!precioParalelo || !precioOficial) {
        return amount;
    }
    
    const value = parseFloat(amount) || 0;
    
    // Si son la misma moneda, retornar sin conversión
    if (fromCurrency === toCurrency) {
        return value;
    }
    
    // Convertir de fromCurrency a VES (moneda base)
    let amountInVES = 0;
    if (fromCurrency === 'USDT') {
        amountInVES = value * precioParalelo;
    } else if (fromCurrency === 'BCV') {
        amountInVES = value * precioOficial;
    } else if (fromCurrency === 'VES') {
        amountInVES = value;
    }
    
    // Convertir de VES a toCurrency
    let converted = 0;
    if (toCurrency === 'USDT') {
        converted = amountInVES / precioParalelo;
    } else if (toCurrency === 'BCV') {
        converted = amountInVES / precioOficial;
    } else if (toCurrency === 'VES') {
        converted = amountInVES;
    }
    
    // Usar round para evitar errores de precisión (.99)
    return parseFloat(converted.toFixed(2));
}

// Función para formatear montos
function formatAmount(amount, currency) {
    if (currency === 'VES') {
        return 'Bs. ' + amount.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } else {
        return '$' + amount.toFixed(2);
    }
}

// Update all amounts on page
function updateAllAmounts() {
    const selectedCurrency = document.getElementById('currency-selector').value.toUpperCase();
    
    // Update monto pagado
    const montoPagado = document.getElementById('monto-pagado');
    if (montoPagado) {
        const originalAmount = parseFloat(montoPagado.dataset.amount) || 0;
        const originalCurrency = montoPagado.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        montoPagado.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update product prices (siempre USDT)
    document.querySelectorAll('.precio-unitario').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        
        let displayText = 'Precio: ' + formatAmount(convertedAmount, selectedCurrency);
        
        // Añadir indicador si se está convirtiendo desde USDT a otra moneda
        if (originalCurrency === 'USDT' && selectedCurrency !== 'USDT') {
            displayText += ' <small class="text-muted" style="font-size: 0.75em;">(Conversión desde USDT)</small>';
        }
        
        el.innerHTML = displayText;
    });
    
    // Update product subtotals (siempre USDT)
    document.querySelectorAll('.subtotal-item').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        
        let displayText = 'Subtotal: ' + formatAmount(convertedAmount, selectedCurrency);
        
        // Añadir indicador si se está convirtiendo desde USDT a otra moneda
        if (originalCurrency === 'USDT' && selectedCurrency !== 'USDT') {
            displayText += ' <small class="text-muted" style="font-size: 0.75em;">(Conversión desde USDT)</small>';
        }
        
        el.innerHTML = displayText;
    });
    
    // Update monto calculado (siempre USDT)
    const montoCalculado = document.querySelector('.monto-calculado');
    if (montoCalculado) {
        const originalAmount = parseFloat(montoCalculado.dataset.amount) || 0;
        const originalCurrency = montoCalculado.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        montoCalculado.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update monto total (en la moneda de la factura)
    const montoTotal = document.querySelector('.monto-total');
    if (montoTotal) {
        const originalAmount = parseFloat(montoTotal.dataset.amount) || 0;
        const originalCurrency = montoTotal.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        montoTotal.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update total cobrado (en la moneda de la factura)
    const totalCobrado = document.querySelector('.total-cobrado');
    if (totalCobrado) {
        const originalAmount = parseFloat(totalCobrado.dataset.amount) || 0;
        const originalCurrency = totalCobrado.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        totalCobrado.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update saldo pendiente (en la moneda de la factura)
    const saldoPendiente = document.querySelector('.saldo-pendiente-value');
    if (saldoPendiente) {
        const originalAmount = parseFloat(saldoPendiente.dataset.amount) || 0;
        const originalCurrency = saldoPendiente.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        saldoPendiente.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
}

// Listen for currency changes
document.getElementById('currency-selector').addEventListener('change', updateAllAmounts);

// Initialize immediately
updateAllAmounts();
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
