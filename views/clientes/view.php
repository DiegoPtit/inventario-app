<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = $model->nombre;
\yii\web\YiiAsset::register($this);

// Obtener las facturas del cliente
$facturas = $model->getFacturas()
    ->with(['itemsFacturas', 'historicoCobros'])
    ->orderBy(['fecha' => SORT_DESC])
    ->all();

// Obtener el histórico de cobros
$historicoCobros = $model->getHistoricoCobros()
    ->with(['factura'])
    ->orderBy(['fecha' => SORT_DESC])
    ->limit(15)
    ->all();

// Obtener las salidas del cliente
$salidas = $model->getSalidas()
    ->with(['producto'])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(10)
    ->all();

// Calcular estadísticas
$totalFacturas = count($facturas);
$montoTotalFacturas = 0;
$facturasPendientes = 0;
$facturasCerradas = 0;

foreach ($facturas as $factura) {
    $montoTotalFacturas += $factura->monto_final;
    
    // Calcular total cobrado de esta factura
    $totalCobradoFactura = 0;
    foreach ($factura->historicoCobros as $cobro) {
        $totalCobradoFactura += $cobro->monto;
    }
    
    // Determinar si está cerrada
    if ($totalCobradoFactura >= $factura->monto_final) {
        $facturasCerradas++;
    } else {
        $facturasPendientes++;
    }
}

$totalCobros = 0;
foreach ($historicoCobros as $cobro) {
    $totalCobros += $cobro->monto;
}

$saldoPendiente = $montoTotalFacturas - $totalCobros;

// Exchange rates
$precioParaleloJs = $precioParalelo ? $precioParalelo->precio_ves : 'null';
$precioOficialJs = $precioOficial ? $precioOficial->precio_ves : 'null';

// Registrar CSS personalizado
$this->registerCss('
.cliente-view-container { 
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
.header-cliente {
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

/* Estadísticas Compactas */
.stats-compact {
    background: white;
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.stat-label {
    font-size: 0.75rem;
    color: #868e96;
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 5px;
}

.stat-meta {
    font-size: 0.7rem;
    color: #adb5bd;
}

.stat-conversions {
    font-size: 0.65rem;
    color: #6c757d;
    margin-top: 5px;
    line-height: 1.3;
}

/* Facturas List */
.factura-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.factura-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.factura-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
    gap: 10px;
}

.factura-info {
    flex: 1;
}

.factura-codigo {
    font-weight: 700;
    color: #212529;
    font-size: 0.95rem;
    margin-bottom: 4px;
}

.factura-detalles {
    font-size: 0.8rem;
    color: #868e96;
}

.factura-status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.factura-status-cerrada {
    background: #d4edda;
    color: #155724;
}

.factura-status-abierta {
    background: #fff3cd;
    color: #856404;
}

.factura-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.factura-monto-section {
    text-align: right;
}

.factura-monto {
    font-size: 1rem;
    font-weight: 700;
    color: #212529;
}

.factura-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 3px;
}

/* Cobros List */
.cobros-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.cobro-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cobro-info {
    flex: 1;
}

.cobro-fecha {
    font-size: 0.85rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 3px;
}

.cobro-detalles {
    font-size: 0.75rem;
    color: #868e96;
}

.cobro-monto-wrapper {
    text-align: right;
}

.cobro-monto {
    font-size: 0.9rem;
    font-weight: 700;
    color: #28a745;
}

.cobro-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 3px;
}

/* Salidas List */
.salidas-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.salida-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.salida-info {
    flex: 1;
}

.salida-fecha {
    font-size: 0.85rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 3px;
}

.salida-detalles {
    font-size: 0.75rem;
    color: #868e96;
}

.salida-monto {
    font-size: 0.85rem;
    font-weight: 600;
    color: #17a2b8;
}

/* Empty States */
.no-facturas, .no-cobros, .no-salidas {
    text-align: center;
    padding: 30px;
    color: #868e96;
}

.no-facturas i, .no-cobros i, .no-salidas i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #adb5bd;
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

.btn-editar {
    color: #495057;
    border-color: #495057;
    background: white;
}

.btn-editar:hover {
    background: #495057;
    color: white;
}

.btn-borrar {
    color: #dc3545;
    border-color: #dc3545;
    background: white;
}

.btn-borrar:hover {
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
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .factura-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .factura-footer {
        justify-content: flex-start;
    }
    
    .factura-monto-section {
        text-align: left;
    }
    
    .cobro-item, .salida-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .cobro-monto-wrapper, .salida-monto {
        text-align: left;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn-compact {
        width: 100%;
    }
}
');
?>

<div class="cliente-view-container">
    
    <!-- Currency Selector -->
    <div class="currency-selector">
        <span class="currency-label">
            <i class="bi bi-currency-exchange"></i> Ver montos en:
        </span>
        <select id="currency-selector" class="currency-select">
            <option value="usdt" selected>USDT (Dólar)</option>
            <option value="bcv">USD BCV (Tasa BCV)</option>
            <option value="ves">Bolívares</option>
        </select>
    </div>

    <!-- Header -->
    <div class="header-cliente">
        <div class="header-title">
            <i class="bi bi-person-circle"></i> <?= Html::encode($model->nombre) ?>
        </div>
        <div class="header-meta">
            <span>
                <span class="status-badge <?= $model->isStatusSolvente() ? 'status-solvente' : 'status-moroso' ?>">
                    <?= $model->isStatusSolvente() ? 'Solvente' : 'Moroso' ?>
                </span>
            </span>
        </div>
    </div>

    <!-- Estadísticas Compactas -->
    <div class="stats-compact">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Facturas</div>
                <div class="stat-value"><?= $totalFacturas ?></div>
                <div class="stat-meta"><?= $facturasCerradas ?> cerradas | <?= $facturasPendientes ?> abiertas</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Total Cobrado</div>
                <div class="stat-value total-cobrado"
                     data-amount="<?= $totalCobros ?>" 
                     data-currency="USDT">$<?= number_format($totalCobros, 2) ?></div>
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $totalCobrosVes = $totalCobros * $precioParalelo->precio_ves;
                    $totalCobrosUsdOficial = $totalCobrosVes / $precioOficial->precio_ves;
                    ?>
                    <div class="stat-conversions">
                        Bs. <?= number_format($totalCobrosVes, 2, ',', '.') ?> | $<?= number_format($totalCobrosUsdOficial, 2) ?> (BCV)
                    </div>
                <?php endif; ?>
            </div>
            <div class="stat-item">
                <div class="stat-label">Saldo Pendiente</div>
                <div class="stat-value saldo-pendiente"
                     data-amount="<?= $saldoPendiente ?>" 
                     data-currency="USDT">$<?= number_format($saldoPendiente, 2) ?></div>
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $saldoPendienteVes = $saldoPendiente * $precioParalelo->precio_ves;
                    $saldoPendienteUsdOficial = $saldoPendienteVes / $precioOficial->precio_ves;
                    ?>
                    <div class="stat-conversions">
                        Bs. <?= number_format($saldoPendienteVes, 2, ',', '.') ?> | $<?= number_format($saldoPendienteUsdOficial, 2) ?> (BCV)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-person-badge"></i> Información del Cliente
        </div>
        <?php if (!empty($model->documento_identidad)): ?>
        <div class="info-row">
            <span class="info-label">Documento</span>
            <span class="info-value"><?= Html::encode($model->documento_identidad) ?></span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($model->telefono)): ?>
        <div class="info-row">
            <span class="info-label">Teléfono</span>
            <span class="info-value"><?= Html::encode($model->telefono) ?></span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($model->ubicacion)): ?>
        <div class="info-row">
            <span class="info-label">Ubicación</span>
            <span class="info-value"><?= Html::encode($model->ubicacion) ?></span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($model->edad)): ?>
        <div class="info-row">
            <span class="info-label">Edad</span>
            <span class="info-value"><?= Html::encode($model->edad) ?> años</span>
        </div>
        <?php endif; ?>
        
        <div class="info-row">
            <span class="info-label">Registrado</span>
            <span class="info-value"><?= Yii::$app->formatter->asDate($model->created_at) ?></span>
        </div>
    </div>

    <!-- Facturas -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-receipt"></i> Facturas del Cliente
        </div>
        <?php if (!empty($facturas)): ?>
            <?php foreach ($facturas as $factura): ?>
                <?php
                // Calcular total cobrado de esta factura
                $totalCobradoFactura = 0;
                foreach ($factura->historicoCobros as $cobro) {
                    $totalCobradoFactura += $cobro->monto;
                }
                $esCerrada = ($totalCobradoFactura >= $factura->monto_final);
                ?>
                <div class="factura-item" onclick="window.location.href='<?= Url::to(['facturas/view', 'id' => $factura->id]) ?>';">
                    <div class="factura-header">
                        <div class="factura-info">
                            <div class="factura-codigo">
                                <i class="bi bi-file-text"></i> <?= Html::encode($factura->codigo) ?>
                            </div>
                            <div class="factura-detalles">
                                <?= Yii::$app->formatter->asDate($factura->fecha) ?> | Items: <?= count($factura->itemsFacturas) ?>
                            </div>
                        </div>
                        <span class="factura-status-badge <?= $esCerrada ? 'factura-status-cerrada' : 'factura-status-abierta' ?>">
                            <?= $esCerrada ? 'Cerrada' : 'Abierta' ?>
                        </span>
                    </div>
                    
                    <div class="factura-footer">
                        <div class="factura-monto-section">
                            <div class="factura-monto factura-monto-item"
                                 data-amount="<?= $factura->monto_final ?>" 
                                 data-currency="USDT">$<?= number_format($factura->monto_final, 2) ?></div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $facturaVes = $factura->monto_final * $precioParalelo->precio_ves;
                                $facturaUsdOficial = $facturaVes / $precioOficial->precio_ves;
                                ?>
                                <div class="factura-conversions">
                                    Bs. <?= number_format($facturaVes, 2, ',', '.') ?> | $<?= number_format($facturaUsdOficial, 2) ?> (BCV)
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="no-facturas">
                <i class="bi bi-inbox"></i>
                <p>Este cliente no tiene facturas registradas aún.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cobros -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-cash-coin"></i> Histórico de Cobros
        </div>
            <?php if (!empty($historicoCobros)): ?>
                <ul class="cobros-lista">
                    <?php foreach ($historicoCobros as $cobro): ?>
                        <li class="cobro-item">
                            <div class="cobro-info">
                                <div class="cobro-fecha">
                                    <i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDate($cobro->fecha) ?>
                                </div>
                                <div class="cobro-detalles">
                                    <?php if ($cobro->factura): ?>
                                        Factura: <?= Html::encode($cobro->factura->codigo) ?>
                                        <?php if ($cobro->metodo_pago): ?> | Método: <?= Html::encode($cobro->metodo_pago) ?><?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="cobro-monto-wrapper">
                                <span class="cobro-monto">$<?= number_format($cobro->monto, 2) ?></span>
                                <?php if ($precioParalelo && $precioOficial): ?>
                                    <?php 
                                    $cobroVes = $cobro->monto * $precioParalelo->precio_ves;
                                    $cobroUsdOficial = $cobroVes / $precioOficial->precio_ves;
                                    ?>
                                    <div class="cobro-conversions">
                                        Bs. <?= number_format($cobroVes, 2, ',', '.') ?> | <strong>$<?= number_format($cobroUsdOficial, 2) ?></strong> (BCV)
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-cobros">
                    <i class="bi bi-inbox"></i>
                    <p>No hay registros de cobros para este cliente.</p>
                </div>
            <?php endif; ?>
        </div>

    <!-- Salidas -->
    <div class="section">
        <div class="section-title">
            <i class="bi bi-box-arrow-right"></i> Salidas Registradas
        </div>
            <?php if (!empty($salidas)): ?>
                <ul class="salidas-lista">
                    <?php foreach ($salidas as $salida): ?>
                        <li class="salida-item">
                            <div class="salida-info">
                                <div class="salida-fecha">
                                    <i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDatetime($salida->created_at) ?>
                                </div>
                                <div class="salida-detalles">
                                    <?php if ($salida->producto): ?>
                                        Producto: <?= Html::encode($salida->producto->marca . ' ' . $salida->producto->modelo) ?>
                                    <?php endif; ?>
                                    <?php if ($salida->is_movimiento): ?>
                                        <span class="badge bg-info text-white ms-2">Movimiento</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="salida-monto"><?= $salida->cantidad ?> unidades</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-salidas">
                    <i class="bi bi-inbox"></i>
                    <p>No hay salidas registradas para este cliente.</p>
                </div>
            <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn-compact btn-editar">
            <i class="bi bi-pencil-square"></i> Editar
        </a>
        <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display: inline; flex: 1;']) ?>
            <button type="submit" class="btn-compact btn-borrar" style="width: 100%;" onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">
                <i class="bi bi-trash"></i> Borrar
            </button>
        <?= Html::endForm() ?>
    </div>
</div>

<?php
$clienteId = $model->id;
$js = <<<JS
const precioParalelo = {$precioParaleloJs};
const precioOficial = {$precioOficialJs};

// Currency conversion functions
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
        return '\$' + amount.toFixed(2);
    }
}

// Update all amounts on page
function updateAllAmounts() {
    const selectedCurrency = document.getElementById('currency-selector').value;
    
    // Update stats values
    document.querySelectorAll('.total-cobrado').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        el.textContent = formatAmount(convertedAmount, selectedCurrency);
    });
    
    document.querySelectorAll('.saldo-pendiente').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        el.textContent = formatAmount(convertedAmount, selectedCurrency);
    });
    
    // Update factura amounts
    document.querySelectorAll('.factura-monto-item').forEach(el => {
        const originalAmount = parseFloat(el.dataset.amount) || 0;
        const originalCurrency = el.dataset.currency || 'USDT';
        const convertedAmount = convertCurrency(originalAmount, originalCurrency, selectedCurrency);
        el.textContent = formatAmount(convertedAmount, selectedCurrency);
    });
}

// Listen for currency changes
document.getElementById('currency-selector').addEventListener('change', updateAllAmounts);

// Initialize immediately
updateAllAmounts();
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
