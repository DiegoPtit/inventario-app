<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\HistoricoCobros $model */

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
    
    // Calcular total cobrado de esta factura
    $historicoCobros = $factura->getHistoricoCobros()->all();
    foreach ($historicoCobros as $cobro) {
        $totalCobrado += $cobro->monto;
    }
    
    $saldoPendiente = $factura->monto_final - $totalCobrado;
}
?>

<style>
.cobro-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header - Resumen del Pago */
.cobro-header {
    background: linear-gradient(135deg, #546e7a 0%, #37474f 100%);
    border-radius: 12px;
    padding: 30px;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 30px;
}

.cobro-header-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.cobro-header-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge-pago {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Secciones */
.section-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.section-title i {
    color: #546e7a;
}

/* Grid de información */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #546e7a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 1.5rem;
    color: #ffffff;
}

.info-content {
    flex: 1;
    min-width: 0;
}

.info-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.info-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    word-wrap: break-word;
}

/* Tabla de Items de Factura */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.items-table thead {
    background: #f8f9fa;
}

.items-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.items-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
}

.items-table tbody tr:hover {
    background: #f8f9fa;
}

.items-table tbody tr:last-child td {
    border-bottom: none;
}

.producto-nombre {
    font-weight: 600;
    color: #2c3e50;
}

.no-items {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-items i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

/* Resumen de Pago */
.resumen-pago {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    margin-top: 30px;
}

.resumen-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
}

.resumen-item:last-child {
    border-bottom: none;
    margin-top: 10px;
    padding-top: 15px;
    border-top: 2px solid #546e7a;
}

.resumen-label {
    font-size: 1rem;
    color: #495057;
    font-weight: 500;
}

.resumen-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
}

.resumen-item:last-child .resumen-label,
.resumen-item:last-child .resumen-value {
    font-size: 1.3rem;
    font-weight: 700;
}

.saldo-positivo {
    color: #dc3545;
}

.saldo-cero {
    color: #28a745;
}

.saldo-negativo {
    color: #28a745;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
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

/* Botones de acción */
.acciones-footer {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn-action {
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border: 2px solid;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-volver {
    color: #546e7a;
    border-color: #546e7a;
    background: transparent;
}

.btn-volver:hover {
    background: #546e7a;
    color: white;
}

.btn-editar {
    color: #0d6efd;
    border-color: #0d6efd;
    background: transparent;
}

.btn-editar:hover {
    background: #0d6efd;
    color: white;
}

.btn-borrar {
    color: #dc3545;
    border-color: #dc3545;
    background: transparent;
}

.btn-borrar:hover {
    background: #dc3545;
    color: white;
}

/* Nota Section */
.nota-section {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.nota-titulo {
    font-weight: 600;
    color: #856404;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nota-contenido {
    color: #856404;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .items-table {
        font-size: 0.85rem;
    }
    
    .items-table th,
    .items-table td {
        padding: 10px;
    }
    
    .cobro-header-title {
        font-size: 1.5rem;
    }
    
    .acciones-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .cobro-view-container {
        padding: 15px;
    }
    
    .section-card {
        padding: 20px 15px;
    }
    
    .items-table {
        display: block;
        overflow-x: auto;
    }
}
</style>

<div class="cobro-view-container">

    <!-- HEADER -->
    <div class="cobro-header">
        <div class="cobro-header-title">
            <i class="bi bi-cash-coin"></i>
            <?= Html::encode($titulo) ?>
        </div>
        <div class="cobro-header-subtitle">
            <i class="bi bi-calendar-event"></i>
            Operación realizada el <?= Yii::$app->formatter->asDate($model->fecha, 'long') ?>
            <?php if ($model->metodo_pago): ?>
                <span class="badge-pago">
                    <i class="bi bi-credit-card"></i>
                    <?= Html::encode($model->metodo_pago) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- INFORMACIÓN DEL CLIENTE -->
    <?php if ($cliente): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-person-circle"></i>
            Información del Cliente
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-person"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Nombre</div>
                    <div class="info-value"><?= Html::encode($cliente->nombre) ?></div>
                </div>
            </div>

            <?php if ($cliente->documento_identidad): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-card-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Documento</div>
                    <div class="info-value"><?= Html::encode($cliente->documento_identidad) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($cliente->telefono): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-telephone"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value"><?= Html::encode($cliente->telefono) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($cliente->ubicacion): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Ubicación</div>
                    <div class="info-value"><?= Html::encode($cliente->ubicacion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge <?= $cliente->isStatusSolvente() ? 'status-solvente' : 'status-moroso' ?>">
                            <?= Html::encode($cliente->displayStatus()) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- DETALLES DEL COBRO -->
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-receipt"></i>
            Detalles del Cobro
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-hash"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">ID Cobro</div>
                    <div class="info-value">#<?= $model->id ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Fecha de Pago</div>
                    <div class="info-value"><?= Yii::$app->formatter->asDate($model->fecha) ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Monto Pagado</div>
                    <div class="info-value">$<?= number_format($model->monto, 2) ?></div>
                </div>
            </div>

            <?php if ($model->metodo_pago): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Método de Pago</div>
                    <div class="info-value"><?= Html::encode($model->metodo_pago) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($model->nota): ?>
        <div class="nota-section">
            <div class="nota-titulo">
                <i class="bi bi-sticky"></i>
                Nota
            </div>
            <div class="nota-contenido">
                <?= Html::encode($model->nota) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- FACTURA ASOCIADA -->
    <?php if ($factura): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-file-text"></i>
            Factura Asociada: <?= Html::encode($factura->codigo) ?>
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-file-earmark-code"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Código de Factura</div>
                    <div class="info-value"><?= Html::encode($factura->codigo) ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Fecha de Factura</div>
                    <div class="info-value"><?= Yii::$app->formatter->asDate($factura->fecha) ?></div>
                </div>
            </div>

            <?php if ($factura->concepto): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-chat-left-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Concepto</div>
                    <div class="info-value"><?= Html::encode($factura->concepto) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ITEMS DE LA FACTURA -->
        <?php if (!empty($itemsFactura)): ?>
        <h4 style="margin-top: 30px; margin-bottom: 15px; color: #2c3e50; font-weight: 600;">
            <i class="bi bi-box-seam"></i> Productos en esta Factura
        </h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: right;">Precio Unitario</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemsFactura as $item): ?>
                <tr>
                    <td>
                        <div class="producto-nombre">
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
                                <?php if ($item->producto->descripcion): ?>
                                    <br><small style="color: #6c757d;"><?= Html::encode($item->producto->descripcion) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                Producto no disponible
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="text-align: center;"><?= $item->cantidad ?></td>
                    <td style="text-align: right;">$<?= number_format($item->precio_unitario, 2) ?></td>
                    <td style="text-align: right; font-weight: 600;">$<?= number_format($item->subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-items">
            <i class="bi bi-inbox"></i>
            <p>No hay productos registrados en esta factura.</p>
        </div>
        <?php endif; ?>

        <!-- RESUMEN DE PAGO -->
        <div class="resumen-pago">
            <div class="resumen-item">
                <span class="resumen-label">Monto Calculado:</span>
                <span class="resumen-value">$<?= number_format($factura->monto_calculado, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Monto Total a Pagar:</span>
                <span class="resumen-value">$<?= number_format($factura->monto_final, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Total Cobrado:</span>
                <span class="resumen-value">$<?= number_format($totalCobrado, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Saldo Pendiente:</span>
                <span class="resumen-value <?= $saldoPendiente > 0 ? 'saldo-positivo' : ($saldoPendiente < 0 ? 'saldo-negativo' : 'saldo-cero') ?>">
                    $<?= number_format($saldoPendiente, 2) ?>
                </span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-file-text"></i>
            Factura Asociada
        </h3>
        <div class="no-items">
            <i class="bi bi-inbox"></i>
            <p>Este cobro no está asociado a ninguna factura.</p>
        </div>
    </div>
    <?php endif; ?>

    

</div>

