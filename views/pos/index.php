<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var bool $editMode */
/** @var int $facturaId */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$editMode = $editMode ?? false;
$facturaId = $facturaId ?? null;
$precioOficial = $precioOficial ?? null;
$precioParalelo = $precioParalelo ?? null;

$this->title = $editMode ? 'Editar Factura' : 'Sistema de Facturación (POS)';

// Register required assets
$this->registerCss("
    .pos-container {
        padding: 0;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        margin-top: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #546e7a;
    }
    
    .section-title:first-of-type {
        margin-top: 0;
    }
    
    .client-info {
        background: #f8f9fa;
        border-left: 4px solid #546e7a;
        border-radius: 4px;
        padding: 20px;
        margin-top: 15px;
        display: none;
    }
    
    .client-info.active {
        display: block;
    }
    
    .client-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .client-info-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .client-info-label {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .client-info-value {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    .product-item {
        background: #f8f9fa;
        border-left: 4px solid #546e7a;
        border-radius: 4px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.2s;
    }
    
    .product-item:hover {
        background: #e9ecef;
    }
    
    .product-item .delete-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 10px;
        font-size: 0.85rem;
    }
    
    .product-grid {
        margin-bottom: 20px;
    }
    
    .add-product-btn {
        border: 2px dashed #546e7a;
        background: transparent;
        color: #546e7a;
        font-weight: 600;
        padding: 15px;
        transition: all 0.3s;
        border-radius: 4px;
    }
    
    .add-product-btn:hover {
        background: #546e7a;
        color: white;
        border-style: solid;
    }
    
    .totales-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e9ecef;
    }
    
    .totales-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .total-item {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
    }
    
    .total-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    
    .total-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .conversion-subtexts {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .conversion-item {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 400;
    }
    
    .conversion-item strong {
        color: #495057;
        font-weight: 600;
    }
    
    /* Currency Carousel Styles */
    .price-carousel {
        position: relative;
        overflow: hidden;
        min-height: 220px;
        touch-action: pan-y;
        padding: 10px 0;
    }
    
    .carousel-page {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .carousel-page.active {
        display: block;
    }
    
    .input-with-badge {
        position: relative;
        width: 100%;
        margin: 15px 0;
    }
    
    .currency-badge {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 6px;
        color: white;
        z-index: 10;
        pointer-events: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .usdt-badge {
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
        box-shadow: 0 2px 6px rgba(76, 175, 80, 0.4);
    }
    
    .bcv-badge {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
        box-shadow: 0 2px 6px rgba(33, 150, 243, 0.4);
    }
    
    .ves-badge {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        box-shadow: 0 2px 6px rgba(255, 152, 0, 0.4);
    }
    
    .currency-label {
        font-size: 0.85rem;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 12px;
        text-align: center;
        letter-spacing: 0.5px;
    }
    
    .currency-input {
        width: 100%;
        padding-right: 15px !important;
    }
    
    .carousel-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }
    
    .carousel-nav {
        background: #f8f9fa;
        border: 2px solid #546e7a;
        color: #546e7a;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }
    
    .carousel-nav:hover:not(:disabled) {
        background: #546e7a;
        color: white;
        transform: scale(1.1);
    }
    
    .carousel-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #dee2e6;
        color: #adb5bd;
    }
    

    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .currency-badge {
            font-size: 0.7rem;
            padding: 5px 10px;
        }
        
        .carousel-controls {
            gap: 15px;
        }
        
        .carousel-nav {
            width: 36px;
            height: 36px;
        }
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
        font-size: 0.9rem;
    }
    
    .btn-process {
        background: #28a745;
        border-color: #28a745;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 30px;
        border-radius: 4px;
        transition: all 0.3s;
    }
    
    .btn-process:hover {
        background: #218838;
        border-color: #1e7e34;
    }
    
    .btn-cancel {
        background: transparent;
        border: 2px solid #dc3545;
        color: #dc3545;
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 30px;
        border-radius: 4px;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background: #dc3545;
        color: white;
    }
    
    .stock-warning {
        color: #dc3545;
        font-weight: 600;
    }
    
    .stock-ok {
        color: #28a745;
        font-weight: 600;
    }
    
    .readonly-field {
        background-color: #e9ecef;
    }
    
    .actions-container {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.1rem;
        }
        
        .totales-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .total-value {
            font-size: 1.5rem;
        }
        
        .actions-container {
            flex-direction: column;
        }
        
        .actions-container .btn {
            width: 100%;
        }
        
        .product-item {
            padding: 15px;
            padding-top: 40px;
        }
        
        .product-item .delete-btn {
            top: 10px;
            right: 10px;
        }
        
        .client-info {
            padding: 15px;
        }
        
        .client-info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }
    
    /* Estilos para Modo Rápido */
    .btn-group .btn.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white !important;
        border-color: #007bff;
        font-weight: 600;
    }
    
    .btn-group .btn {
        transition: all 0.3s ease;
    }
    
    .btn-group .btn:hover:not(.active) {
        background: #e7f3ff;
        border-color: #007bff;
        color: #007bff;
    }
    
    #barcode-reader-pos video, #barcode-reader-pos canvas {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
");
?>

<div class="pos-container container-fluid px-3">
    
    <div class="text-center mb-4">
        <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
        <div class="text-start mt-3">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Volver al inicio', Url::to(['site/index']), [
                'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
            ]) ?>
        </div>
    </div>
    
    <!-- INFORMACIÓN DEL CLIENTE -->
    <h2 class="section-title"><i class="bi bi-person-circle"></i> Información del Cliente</h2>
    
    <div class="mb-3">
        <label class="form-label">Cliente</label>
        <div class="input-group">
            <select id="cliente-select" class="form-select" <?= $editMode ? 'disabled' : '' ?>>
                <option value="">Seleccione un cliente (opcional)</option>
            </select>
            <?php if (!$editMode): ?>
            <button type="button" class="btn btn-outline-primary" id="btn-add-client" title="Agregar nuevo cliente">
                <i class="bi bi-plus-lg"></i>
            </button>
            <?php endif; ?>
        </div>
        <?php if ($editMode): ?>
        <small class="text-muted">El cliente no puede ser modificado en modo edición</small>
        <?php endif; ?>
    </div>
    
    <div id="client-info" class="client-info">
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-person me-2"></i>Nombre:</span>
            <span class="client-info-value" id="client-nombre">-</span>
        </div>
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-card-text me-2"></i>Documento:</span>
            <span class="client-info-value" id="client-documento">-</span>
        </div>
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-telephone me-2"></i>Teléfono:</span>
            <span class="client-info-value" id="client-telefono">-</span>
        </div>
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-geo-alt me-2"></i>Ubicación:</span>
            <span class="client-info-value" id="client-ubicacion">-</span>
        </div>
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-123 me-2"></i>Edad:</span>
            <span class="client-info-value" id="client-edad">-</span>
        </div>
        <div class="client-info-row">
            <span class="client-info-label"><i class="bi bi-shield-check me-2"></i>Status:</span>
            <span class="client-info-value" id="client-status">-</span>
        </div>
    </div>
    
    <!-- DATOS DE LA FACTURA -->
    <h2 class="section-title"><i class="bi bi-receipt"></i> Datos de la Factura</h2>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Código de Factura</label>
            <input type="text" id="codigo-factura" class="form-control readonly-field" readonly>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" id="fecha-factura" class="form-control readonly-field" value="<?= date('Y-m-d') ?>" <?= $editMode ? 'readonly' : '' ?>>
            <?php if ($editMode): ?>
            <small class="text-muted">La fecha no puede ser modificada</small>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Concepto</label>
            <textarea id="concepto-factura" class="form-control readonly-field" rows="2" placeholder="Ingrese el concepto..." <?= $editMode ? 'readonly' : '' ?>></textarea>
            <?php if ($editMode): ?>
            <small class="text-muted">El concepto no puede ser modificado</small>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- PRODUCTOS -->
    <h2 class="section-title"><i class="bi bi-box-seam"></i> Productos</h2>
    
    <!-- Toggle Modo Clásico / Modo Rápido -->
    <div class="mb-4">
        <div class="btn-group w-100" role="group">
            <button type="button" id="btn-modo-clasico" class="btn btn-outline-primary active">
                <i class="bi bi-hand-index"></i> Modo Clásico
            </button>
            <button type="button" id="btn-modo-rapido" class="btn btn-outline-primary">
                <i class="bi bi-upc-scan"></i> Modo Rápido
            </button>
        </div>
    </div>
    
    <!-- Scanner Container (solo visible en modo rápido) -->
    <div id="scanner-container" style="display: none;" class="mb-4">
        <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h5 class="mb-3"><i class="bi bi-camera-video"></i> Escáner de Códigos de Barras</h5>
            <div style="position: relative; width: 100%; max-width: 640px; margin: 0 auto; overflow: hidden; border-radius: 10px;">
                <div id="barcode-reader-pos" style="position: relative; width: 100%; height: 400px; background: #000; border-radius: 10px; overflow: hidden;"></div>
                <div id="scanner-overlay-pos" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 70%; height: 50%; border: 3px solid #28a745; border-radius: 10px; pointer-events: none; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);"></div>
            </div>
            <div id="scanner-status-pos" style="text-align: center; margin-top: 15px; font-weight: 600; color: #6c757d;">
                <i class="bi bi-camera-video"></i> Iniciando cámara...
            </div>
        </div>
    </div>
    
    <div id="products-grid" class="product-grid">
        <!-- Product items will be added here dynamically -->
    </div>
    
    <button type="button" id="add-product-btn" class="btn add-product-btn w-100">
        <i class="bi bi-plus-circle"></i> Insertar Nuevo Producto
    </button>
    
    <!-- TOTALES Y ACCIONES -->
    <div class="totales-section">
        <h2 class="section-title"><i class="bi bi-calculator"></i> Totales y Procesamiento</h2>
        
        <div class="totales-grid">
            <div class="total-item">
                <div class="total-label">Subtotal (Costo)</div>
                <div class="total-value">$<span id="subtotal-display">0.00</span> <small class="text-muted">(USDT)</small></div>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <div class="conversion-subtexts">
                        <div class="conversion-item">
                            En Bolívares al cambio paralelo: <strong id="subtotal-ves">Bs. 0,00</strong>
                        </div>
                        <div class="conversion-item">
                            En Dólares a Tasa BCV: <strong id="subtotal-usd-oficial">$0.00</strong>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="total-item">
                <div class="total-label">Precio de Venta</div>
                
                <!-- Currency Carousel -->
                <div class="price-carousel">
                    <!-- Page 1: USDT Input -->
                    <div class="carousel-page active" data-currency="usdt">
                        <div class="currency-label">Precio de Venta (USDT)</div>
                        <div class="input-with-badge">
                            <span class="currency-badge usdt-badge">USDT</span>
                            <input type="number" 
                                   id="precio-usdt" 
                                   class="form-control form-control-lg text-center fw-bold currency-input" 
                                   step="0.01" 
                                   value="0.00"
                                   placeholder="0.00"
                                   style="font-size: 1.5rem; border: 2px solid #4caf50; padding-left: 70px;">
                        </div>
                        
                        <?php if ($precioParalelo && $precioOficial): ?>
                            <div class="conversion-subtexts">
                                <div class="conversion-item">
                                    En Bolívares al cambio paralelo: <strong id="usdt-to-ves">Bs. 0,00</strong>
                                </div>
                                <div class="conversion-item">
                                    En Dólares a Tasa BCV: <strong id="usdt-to-usd-bcv">$0.00</strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Page 2: USD BCV Input -->
                    <div class="carousel-page" data-currency="usd-bcv">
                        <div class="currency-label">Precio de Venta (USD BCV)</div>
                        <div class="input-with-badge">
                            <span class="currency-badge bcv-badge">USD BCV</span>
                            <input type="number" 
                                   id="precio-usd-bcv" 
                                   class="form-control form-control-lg text-center fw-bold currency-input" 
                                   step="0.01" 
                                   value="0.00"
                                   placeholder="0.00"
                                   style="font-size: 1.5rem; border: 2px solid #2196f3; padding-left: 90px;">
                        </div>
                        
                        <?php if ($precioParalelo && $precioOficial): ?>
                            <div class="conversion-subtexts">
                                <div class="conversion-item">
                                    En Dólares USDT: <strong id="bcv-to-usdt">$0.00</strong>
                                </div>
                                <div class="conversion-item">
                                    En Bolívares al cambio paralelo: <strong id="bcv-to-ves">Bs. 0,00</strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Page 3: Bolívares Input -->
                    <div class="carousel-page" data-currency="ves">
                        <div class="currency-label">Precio de Venta (Bolívares)</div>
                        <div class="input-with-badge">
                            <span class="currency-badge ves-badge">Bs.</span>
                            <input type="number" 
                                   id="precio-ves" 
                                   class="form-control form-control-lg text-center fw-bold currency-input" 
                                   step="0.01" 
                                   value="0.00"
                                   placeholder="0,00"
                                   style="font-size: 1.5rem; border: 2px solid #ff9800; padding-left: 70px;">
                        </div>
                        
                        <?php if ($precioParalelo && $precioOficial): ?>
                            <div class="conversion-subtexts">
                                <div class="conversion-item">
                                    En Dólares USDT: <strong id="ves-to-usdt">$0.00</strong>
                                </div>
                                <div class="conversion-item">
                                    En Dólares a Tasa BCV: <strong id="ves-to-usd-bcv">$0.00</strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <div class="carousel-controls">
                    <button type="button" class="carousel-nav" id="carousel-prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="carousel-nav" id="carousel-next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                
                <!-- Hidden fields for server submission -->
                <input type="hidden" id="monto-final" value="0.00">
                <input type="hidden" id="currency-selected" value="USDT">
            </div>
        </div>
        
        <div class="actions-container">
            <button type="button" id="btn-cancel" class="btn btn-cancel">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
            <button type="button" id="btn-process" class="btn btn-process">
                <i class="bi bi-check-circle"></i> <?= $editMode ? 'Actualizar Factura' : 'Procesar Factura' ?>
            </button>
        </div>
    </div>
    
</div>

<!-- Product Item Template (Hidden) -->
<template id="product-card-template">
    <div class="product-item" data-card-index="">
        <button type="button" class="btn btn-danger btn-sm delete-btn delete-product">
            <i class="bi bi-trash"></i>
        </button>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Producto</label>
                <input type="hidden" class="product-id" value="">
                <input type="hidden" class="product-lugar-id" value="">
                <button type="button" class="btn btn-outline-secondary w-100 text-start btn-select-producto" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="product-name-display">Seleccione un producto</span>
                    <i class="bi bi-search"></i>
                </button>
            </div>
            
            <div class="col-md-2 col-6 mb-3">
                <label class="form-label">Stock Disponible</label>
                <input type="text" class="form-control stock-remaining readonly-field text-center" readonly value="0">
            </div>
            
            <div class="col-md-2 col-6 mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control product-quantity text-center" min="1" value="1">
            </div>
            
            <div class="col-md-2 col-12 mb-3">
                <label class="form-label">Valor Unitario</label>
                <input type="number" class="form-control product-cost readonly-field text-center" readonly step="0.01" value="0.00">
            </div>
        </div>
    </div>
</template>

<!-- renderizamos el js heredoc en un archivo separado -->
<?php require __DIR__ . '/js/_pos-js-snippets.php'; ?>

