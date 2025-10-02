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

// Register CSS
$this->registerCss("
    .factura-container { padding: 0; }
    .section-title { font-size: 1.3rem; font-weight: 600; color: #2c3e50; margin-bottom: 20px; margin-top: 30px; display: flex; align-items: center; gap: 10px; }
    .section-title i { color: #546e7a; }
    .section-title:first-of-type { margin-top: 0; }
    .info-box { background: #f8f9fa; border-left: 4px solid #546e7a; border-radius: 4px; padding: 20px; margin-bottom: 20px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e9ecef; }
    .info-row:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
    .info-label { font-weight: 500; color: #6c757d; font-size: 0.9rem; }
    .info-value { font-weight: 600; color: #2c3e50; font-size: 0.95rem; }
    .item-producto { background: #f8f9fa; border-left: 4px solid #546e7a; border-radius: 4px; padding: 20px; margin-bottom: 15px; transition: all 0.2s; }
    .item-producto:hover { background: #e9ecef; }
    .item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .item-nombre { font-weight: 600; color: #2c3e50; font-size: 1rem; }
    .item-cantidad-badge { background: #546e7a; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
    .item-details { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
    .item-detail { display: flex; justify-content: space-between; align-items: center; }
    .item-detail-label { font-size: 0.85rem; color: #6c757d; }
    .item-detail-value { font-weight: 600; color: #2c3e50; font-size: 0.9rem; }
    .totales-section { margin-top: 40px; padding-top: 30px; border-top: 2px solid #e9ecef; }
    .total-principal { background: #f8f9fa; border-radius: 4px; padding: 25px; text-align: center; margin-bottom: 20px; }
    .total-label { font-size: 0.9rem; color: #6c757d; font-weight: 500; text-transform: uppercase; margin-bottom: 10px; }
    .total-value { font-size: 2.5rem; font-weight: 700; color: #28a745; }
    .conversion-subtexts { margin-top: 12px; display: flex; flex-direction: column; gap: 6px; }
    .conversion-item { font-size: 0.75rem; color: #6c757d; font-weight: 400; }
    .conversion-item strong { color: #495057; font-weight: 600; }
    .lleva-pagado { text-align: center; margin-bottom: 25px; font-size: 1rem; color: #6c757d; }
    .lleva-pagado-monto { font-weight: 700; color: #2c3e50; font-size: 1.2rem; }
    .lleva-pagado-conversions { margin-top: 8px; display: flex; flex-direction: column; gap: 4px; }
    .lleva-pagado-conversions .conversion-item { font-size: 0.7rem; }
    .actions-container { display: flex; gap: 15px; justify-content: center; }
    .btn-volver { background: transparent; border: 2px solid #546e7a; color: #546e7a; font-weight: 600; font-size: 1rem; padding: 12px 30px; border-radius: 4px; transition: all 0.3s; }
    .btn-volver:hover { background: #546e7a; color: white; }
    .btn-registrar { background: #28a745; border: 2px solid #28a745; color: white; font-weight: 600; font-size: 1rem; padding: 12px 30px; border-radius: 4px; transition: all 0.3s; }
    .btn-registrar:hover { background: #218838; border-color: #1e7e34; }
    .btn-registrar:disabled { background: #6c757d; border-color: #6c757d; cursor: not-allowed; opacity: 0.6; }
    .btn-registrar:disabled:hover { background: #6c757d; border-color: #6c757d; }
    .no-items { text-align: center; padding: 40px; color: #6c757d; }
    .no-items i { font-size: 3rem; margin-bottom: 15px; color: #adb5bd; }
    @media (max-width: 768px) {
        .section-title { font-size: 1.1rem; }
        .total-value { font-size: 2rem; }
        .actions-container { flex-direction: column; }
        .actions-container .btn { width: 100%; }
        .info-row { flex-direction: column; align-items: flex-start; gap: 5px; }
        .item-details { grid-template-columns: 1fr; }
        .item-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    }
");
?>

<div class="factura-container container-fluid px-3">
    
    <div class="text-center mb-4">
        <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
        <div class="text-start mt-3">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Volver al desglose principal', Url::to(['site/index']), [
                'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
            ]) ?>
        </div>
    </div>
    
    <!-- INFORMACIÓN DEL CLIENTE -->
    <h2 class="section-title"><i class="bi bi-person-circle"></i> Información del Cliente</h2>
    
    <?php if ($model->cliente): ?>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label"><i class="bi bi-person me-2"></i>Nombre:</span>
                <span class="info-value"><?= Html::encode($model->cliente->nombre) ?></span>
            </div>
            <?php if ($model->cliente->documento_identidad): ?>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-card-text me-2"></i>Documento:</span>
                <span class="info-value"><?= Html::encode($model->cliente->documento_identidad) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($model->cliente->telefono): ?>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-telephone me-2"></i>Teléfono:</span>
                <span class="info-value"><?= Html::encode($model->cliente->telefono) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($model->cliente->ubicacion): ?>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Ubicación:</span>
                <span class="info-value"><?= Html::encode($model->cliente->ubicacion) ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-shield-check me-2"></i>Status:</span>
                <span class="info-value">
                    <?php if ($model->cliente->isStatusSolvente()): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Solvente</span>
                    <?php else: ?>
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Moroso</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    <?php else: ?>
        <div class="info-box">
            <p class="mb-0 text-muted"><i class="bi bi-info-circle me-2"></i>Sin cliente asociado</p>
        </div>
    <?php endif; ?>
    
    <!-- DATOS DE LA FACTURA -->
    <h2 class="section-title"><i class="bi bi-receipt"></i> Datos de la Factura</h2>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label"><i class="bi bi-hash me-2"></i>Código:</span>
            <span class="info-value"><?= Html::encode($model->codigo) ?></span>
        </div>
        <?php if ($model->concepto): ?>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-file-text me-2"></i>Concepto:</span>
            <span class="info-value"><?= Html::encode($model->concepto) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-calendar me-2"></i>Fecha:</span>
            <span class="info-value"><?= Yii::$app->formatter->asDate($model->fecha) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="bi bi-clock me-2"></i>Creada:</span>
            <span class="info-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
        </div>
    </div>
    
    <!-- ITEMS DE LA FACTURA -->
    <h2 class="section-title"><i class="bi bi-box-seam"></i> Productos en esta Factura</h2>
    
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <div class="item-producto">
                <div class="item-header">
                    <div class="item-nombre">
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
                            <i class="bi bi-box me-2"></i><?= Html::encode($nombreProducto) ?>
                        <?php else: ?>
                            <i class="bi bi-box me-2"></i>Producto #<?= $item->id_producto ?>
                        <?php endif; ?>
                    </div>
                    <div class="item-cantidad-badge">
                        <?= $item->cantidad ?> unidad<?= $item->cantidad != 1 ? 'es' : '' ?>
                    </div>
                </div>
                
                <div class="item-details">
                    <div class="item-detail">
                        <span class="item-detail-label">Precio Unitario:</span>
                        <span class="item-detail-value">$<?= number_format($item->precio_unitario, 2) ?></span>
                    </div>
                    <div class="item-detail">
                        <span class="item-detail-label">Subtotal:</span>
                        <span class="item-detail-value">$<?= number_format($item->subtotal ?? ($item->cantidad * $item->precio_unitario), 2) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-items">
            <i class="bi bi-inbox"></i>
            <p>No hay productos en esta factura</p>
        </div>
    <?php endif; ?>
    
    <!-- TOTAL Y ACCIONES -->
    <div class="totales-section">
        <h2 class="section-title"><i class="bi bi-cash-coin"></i> Total a Pagar</h2>
        
        <div class="total-principal">
            <div class="total-label">Monto Total</div>
            <div class="total-value">$<?= number_format($model->monto_final, 2) ?></div>
            
            <?php if ($precioParalelo && $precioOficial): ?>
                <div class="conversion-subtexts">
                    <?php 
                    $montoVes = $model->monto_final * $precioParalelo->precio_ves;
                    $montoUsdOficial = $montoVes / $precioOficial->precio_ves;
                    ?>
                    <div class="conversion-item">
                        En Bolívares al cambio paralelo: <strong>Bs. <?= number_format($montoVes, 2, ',', '.') ?></strong>
                    </div>
                    <div class="conversion-item">
                        En Dólares a Tasa BCV: <strong>$<?= number_format($montoUsdOficial, 2) ?></strong>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="lleva-pagado">
            Lleva pagado: <span class="lleva-pagado-monto">$<?= number_format($totalPagado, 2) ?></span>
            
            <?php if ($precioParalelo && $precioOficial && $totalPagado > 0): ?>
                <div class="lleva-pagado-conversions">
                    <?php 
                    $pagadoVes = $totalPagado * $precioParalelo->precio_ves;
                    $pagadoUsdOficial = $pagadoVes / $precioOficial->precio_ves;
                    ?>
                    <div class="conversion-item">
                        En Bolívares al cambio paralelo: <strong>Bs. <?= number_format($pagadoVes, 2, ',', '.') ?></strong>
                    </div>
                    <div class="conversion-item">
                        En Dólares a Tasa BCV: <strong>$<?= number_format($pagadoUsdOficial, 2) ?></strong>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="actions-container">
            <?= Html::a('<i class="bi bi-house-door me-2"></i>Volver al Desglose Principal', 
                Url::to(['site/index']), 
                ['class' => 'btn btn-volver']
            ) ?>
            
            <?= Html::a('<i class="bi bi-pencil-square me-2"></i>Editar Factura', 
                Url::to(['pos/edit', 'id' => $model->id]), 
                [
                    'class' => 'btn btn-volver',
                    'style' => 'background: transparent; border: 2px solid #ffc107; color: #ffc107;',
                    'title' => 'Editar los productos de esta factura'
                ]
            ) ?>
            
            <?php 
            $facturaPagada = $totalPagado >= $model->monto_final;
            ?>
            <?= Html::a('<i class="bi bi-cash-stack me-2"></i>Registrar Cobro', 
                $facturaPagada ? '#' : Url::to(['historico-cobros/create', 'id_cliente' => $model->id_cliente, 'id_factura' => $model->id]), 
                [
                    'class' => 'btn btn-registrar',
                    'disabled' => $facturaPagada,
                    'title' => $facturaPagada ? 'La factura ya está completamente pagada' : 'Registrar un nuevo cobro'
                ]
            ) ?>
        </div>
    </div>

</div>
