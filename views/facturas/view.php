<?php

use yii\helpers\Html;
use yii\helpers\Url;

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

// Exchange rates
$precioParaleloJs = $precioParalelo ? $precioParalelo->precio_ves : 'null';
$precioOficialJs = $precioOficial ? $precioOficial->precio_ves : 'null';

// Calculate saldo pendiente
$saldoPendiente = $model->monto_final - $totalPagado;
?>

<style>
.factura-view {
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
.header-factura {
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
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-meta {
    font-size: 0.85rem;
    color: #868e96;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
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

/* Products */
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

/* Totals */
.totals-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 0.9rem;
}

.total-row.main {
    border-top: 2px solid #dee2e6;
    margin-top: 8px;
    padding-top: 12px;
    font-size: 1.1rem;
    font-weight: 700;
}

.total-row.pagado {
    color: #28a745;
    font-weight: 600;
}

.total-row.pendiente {
    color: #dc3545;
    font-weight: 600;
}

.total-row.pendiente.cero {
    color: #28a745;
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

.btn-primary {
    color: #495057;
    border-color: #495057;
    background: white;
}

.btn-primary:hover {
    background: #495057;
    color: white;
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

.btn-warning {
    color: #ff9800;
    border-color: #ff9800;
    background: white;
}

.btn-warning:hover {
    background: #ff9800;
    color: white;
}

.btn-success {
    color: #28a745;
    border-color: #28a745;
    background: white;
}

.btn-success:hover {
    background: #28a745;
    color: white;
}

.btn-success:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-success:disabled:hover {
    background: white;
    color: #28a745;
}

@media (max-width: 768px) {
    .currency-selector {
        flex-direction: column;
        align-items: stretch;
    }
    
    .currency-select {
        width: 100%;
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

<div class="factura-view">
    
    <!-- Currency Selector -->
    <div class="currency-selector">
        <span class="currency-label">
            <i class="bi bi-currency-exchange"></i> Ver montos en:
        </span>
        <select id="currency-selector" class="currency-select">
            <option value="usdt" <?= $model->currency === 'USDT' ? 'selected' : '' ?>>USDT (Dólar)</option>
            <option value="bcv" <?= $model->currency === 'BCV' ? 'selected' : '' ?>>USD BCV (Tasa BCV)</option>
            <option value="ves" <?= $model->currency === 'VES' ? 'selected' : '' ?>>Bolívares</option>
        </select>
    </div>

    <!-- Header -->
    <div class="header-factura">
        <div class="header-title">
            <i class="bi bi-receipt"></i> <?= Html::encode($model->codigo) ?>
        </div>
        <div class="header-meta">
            <span><i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDate($model->fecha) ?></span>
            <span><i class="bi bi-clock"></i> <?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
        </div>
    </div>

    <!-- Cliente Info -->
    <?php if ($model->cliente): ?>
    <div class="section">
        <div class="section-title">
            <i class="bi bi-person-circle"></i> Cliente
        </div>
        <div class="info-row">
            <span class="info-label">Nombre</span>
            <span class="info-value"><?= Html::encode($model->cliente->nombre) ?></span>
        </div>
        <?php if ($model->cliente->documento_identidad): ?>
        <div class="info-row">
            <span class="info-label">Documento</span>
            <span class="info-value"><?= Html::encode($model->cliente->documento_identidad) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($model->cliente->telefono): ?>
        <div class="info-row">
            <span class="info-label">Teléfono</span>
            <span class="info-value"><?= Html::encode($model->cliente->telefono) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value">
                <span class="status-badge <?= $model->cliente->isStatusSolvente() ? 'status-solvente' : 'status-moroso' ?>">
                    <?= $model->cliente->isStatusSolvente() ? 'Solvente' : 'Moroso' ?>
                </span>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detalles -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-file-text"></i> Detalles de la Factura
        </div>
        <?php if ($model->concepto): ?>
        <div class="info-row">
            <span class="info-label">Concepto</span>
            <span class="info-value"><?= Html::encode($model->concepto) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Total Items</span>
            <span class="info-value"><?= count($items) ?> producto<?= count($items) != 1 ? 's' : '' ?></span>
        </div>
    </div>

    <!-- Productos -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-box-seam"></i> Productos
        </div>
        
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
            <div class="product-item">
                <div class="product-name">
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
                </div>
                <div class="product-meta">
                    <span>Cant: <?= $item->cantidad ?></span>
                    <span class="precio-unitario" 
                          data-amount="<?= $item->precio_unitario ?>" 
                          data-currency="USDT">
                        Precio: $<?= number_format($item->precio_unitario, 2) ?>
                    </span>
                    <span class="subtotal-item" 
                          data-amount="<?= $item->subtotal ?? ($item->cantidad * $item->precio_unitario) ?>" 
                          data-currency="USDT">
                        Subtotal: $<?= number_format($item->subtotal ?? ($item->cantidad * $item->precio_unitario), 2) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #868e96; padding: 20px;">No hay productos en esta factura</p>
        <?php endif; ?>
        
        <!-- Totals -->
        <div class="totals-box">
            <div class="total-row">
                <span>Monto Calculado:</span>
                <span class="monto-calculado" 
                      data-amount="<?= $model->monto_calculado ?>" 
                      data-currency="USDT">
                    $<?= number_format($model->monto_calculado, 2) ?>
                </span>
            </div>
            <div class="total-row main">
                <span>Total Factura:</span>
                <span class="total-factura" 
                      data-amount="<?= $model->monto_final ?>" 
                      data-currency="<?= $model->currency ?>">
                    $<?= number_format($model->monto_final, 2) ?>
                </span>
            </div>
            <div class="total-row pagado">
                <span>Total Pagado:</span>
                <span class="total-pagado" 
                      data-amount="<?= $totalPagado ?>" 
                      data-currency="<?= $model->currency ?>">
                    $<?= number_format($totalPagado, 2) ?>
                </span>
            </div>
            <div class="total-row pendiente <?= $saldoPendiente <= 0 ? 'cero' : '' ?>">
                <span>Saldo Pendiente:</span>
                <span class="saldo-pendiente" 
                      data-amount="<?= $saldoPendiente ?>" 
                      data-currency="<?= $model->currency ?>">
                    $<?= number_format($saldoPendiente, 2) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="<?= Url::to(['facturas/payment-report', 'id' => $model->id, 'currency' => 'bcv']) ?>" 
           id="print-link"
           class="btn-compact btn-primary" 
           target="_blank">
            <i class="bi bi-printer"></i> Imprimir
        </a>
        
        <?= Html::a(
            '<i class="bi bi-arrow-left"></i> Volver',
            Url::to(['site/index']),
            ['class' => 'btn-compact btn-secondary']
        ) ?>
        
        <?= Html::a(
            '<i class="bi bi-pencil"></i> Editar',
            Url::to(['pos/edit', 'id' => $model->id]),
            ['class' => 'btn-compact btn-warning']
        ) ?>
        
        <?php 
        $facturaPagada = $totalPagado >= $model->monto_final;
        ?>
        <?= Html::a(
            '<i class="bi bi-cash-stack"></i> Cobrar',
            $facturaPagada ? '#' : Url::to(['historico-cobros/create', 'id_cliente' => $model->id_cliente, 'id_factura' => $model->id]),
            [
                'class' => 'btn-compact btn-success',
                'disabled' => $facturaPagada,
                'title' => $facturaPagada ? 'Factura pagada' : 'Registrar cobro'
            ]
        ) ?>
    </div>

</div>

<?php
$invoiceId = $model->id;
$js = <<<JS
const precioParalelo = {$precioParaleloJs};
const precioOficial = {$precioOficialJs};
const invoiceId = {$invoiceId};

// Currency conversion functions
// Converts from one currency to another
function convertCurrency(amount, fromCurrency, toCurrency) {
    if (!precioParalelo || !precioOficial) {
        return amount;
    }
    
    const value = parseFloat(amount) || 0;
    
    // Si son la misma moneda, devolver el valor sin cambios
    if (fromCurrency === toCurrency) {
        return value;
    }
    
    // Primero convertir de la moneda origen a VES (moneda base)
    let amountInVES = 0;
    if (fromCurrency === 'USDT') {
        amountInVES = value * precioParalelo;
    } else if (fromCurrency === 'BCV') {
        amountInVES = value * precioOficial;
    } else if (fromCurrency === 'VES') {
        amountInVES = value;
    }
    
    // Luego convertir de VES a la moneda destino
    if (toCurrency === 'usdt') {
        return amountInVES / precioParalelo;
    } else if (toCurrency === 'bcv') {
        return amountInVES / precioOficial;
    } else if (toCurrency === 'ves') {
        return amountInVES;
    }
    
    return value;
}

// Format amount with currency symbol
function formatAmount(amount, currency) {
    if (currency === 'ves') {
        return 'Bs. ' + amount.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } else {
        return '$' + amount.toFixed(2);
    }
}

// Update print link with current currency
function updatePrintLink() {
    const currency = document.getElementById('currency-selector').value;
    const printLink = document.getElementById('print-link');
    if (printLink) {
        const url = new URL(printLink.href);
        url.searchParams.set('currency', currency);
        printLink.href = url.toString();
    }
}

// Update all amounts on page
function updateAllAmounts() {
    const selectedCurrency = document.getElementById('currency-selector').value;
    
    // Update product prices
    document.querySelectorAll('.precio-unitario').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        
        let displayText = 'Precio: ' + formatAmount(convertedAmount, selectedCurrency);
        
        // Añadir indicador si se está convirtiendo desde USDT a otra moneda
        if (originalCurrency === 'USDT' && selectedCurrency !== 'usdt') {
            displayText += ' <small class="text-muted" style="font-size: 0.75em;">(Conversión desde USDT)</small>';
        }
        
        el.innerHTML = displayText;
    });
    
    // Update product subtotals
    document.querySelectorAll('.subtotal-item').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        
        let displayText = 'Subtotal: ' + formatAmount(convertedAmount, selectedCurrency);
        
        // Añadir indicador si se está convirtiendo desde USDT a otra moneda
        if (originalCurrency === 'USDT' && selectedCurrency !== 'usdt') {
            displayText += ' <small class="text-muted" style="font-size: 0.75em;">(Conversión desde USDT)</small>';
        }
        
        el.innerHTML = displayText;
    });
    
    // Update monto calculado
    const montoCalculado = document.querySelector('.monto-calculado');
    if (montoCalculado) {
        const originalAmount = parseFloat(montoCalculado.dataset.amount) || 0;
        const originalCurrency = montoCalculado.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        montoCalculado.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update total factura
    const totalFactura = document.querySelector('.total-factura');
    if (totalFactura) {
        const originalAmount = parseFloat(totalFactura.dataset.amount) || 0;
        const originalCurrency = totalFactura.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        totalFactura.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update total pagado
    const totalPagado = document.querySelector('.total-pagado');
    if (totalPagado) {
        const originalAmount = parseFloat(totalPagado.dataset.amount) || 0;
        const originalCurrency = totalPagado.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        totalPagado.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update saldo pendiente
    const saldoPendiente = document.querySelector('.saldo-pendiente');
    if (saldoPendiente) {
        const originalAmount = parseFloat(saldoPendiente.dataset.amount) || 0;
        const originalCurrency = saldoPendiente.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        saldoPendiente.textContent = formatAmount(convertedAmount, selectedCurrency);
    }
    
    // Update print link
    updatePrintLink();
}

// Listen for currency changes
document.getElementById('currency-selector').addEventListener('change', updateAllAmounts);

// Initialize immediately
updateAllAmounts();
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
