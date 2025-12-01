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

<?php
$this->registerJs("
    let clientesData = [];
    let productosData = [];
    let productCardCounter = 0;
    const editMode = " . ($editMode ? 'true' : 'false') . ";
    const facturaId = " . ($facturaId ? $facturaId : 'null') . ";
    let originalItems = {}; // Para almacenar los items originales en modo edición
    
    // Dollar exchange rates
    const precioParalelo = " . ($precioParalelo ? $precioParalelo->precio_ves : 'null') . ";
    const precioOficial = " . ($precioOficial ? $precioOficial->precio_ves : 'null') . ";
    
    // URLs for AJAX calls
    const urlGetClients = '" . Url::to(['pos/get-clients']) . "';
    const urlGetClientDetails = '" . Url::to(['pos/get-client-details']) . "';
    const urlGenerateCode = '" . Url::to(['pos/generate-invoice-code']) . "';
    const urlGetProducts = '" . Url::to(['pos/get-products']) . "';
    const urlProcessInvoice = '" . Url::to(['pos/process-invoice']) . "';
    const urlUpdateInvoice = '" . Url::to(['pos/update-invoice']) . "';
    const urlGetInvoiceData = '" . Url::to(['pos/get-invoice-data']) . "';
    
    // Currency carousel state (global scope)
    let currentCurrencyPage = 0;
    const currencyPages = ['usdt', 'usd-bcv', 'ves'];
    let touchStartX = 0;
    let touchEndX = 0;
    
    // Initialize on page load
    $(document).ready(function() {
        loadClients();
        loadProducts();
        
        if (editMode && facturaId) {
            // En modo edición, cargar datos de la factura
            loadInvoiceData(facturaId);
        } else {
            // En modo creación, generar código nuevo
            generateInvoiceCode();
        }
        
        // Initialize currency carousel
        initCurrencyCarousel();
        
        
        // Asegurar que el botón de agregar cliente funcione
        $('#btn-add-client').on('click', function(e) {
            e.preventDefault();
            
            console.log('Botón + clickeado');
            
            const modalElement = document.getElementById('modalNuevoCliente');
            console.log('Modal element:', modalElement);
            console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
            console.log('Bootstrap object:', typeof bootstrap !== 'undefined' ? bootstrap : 'No disponible');
            
            if (!modalElement) {
                alert('Error: El modal no existe en el DOM. Por favor, recargue la página.');
                return;
            }
            
            if (typeof bootstrap === 'undefined') {
                alert('Error: Bootstrap 5 no está cargado. Por favor, recargue la página.');
                return;
            }
            
            // Resetear el formulario antes de abrir
            const form = document.getElementById('form-nuevo-cliente-modal');
            if (form) form.reset();
            
            // Resetear el estado del modal a paso 1
            if (typeof window.resetModalClienteStep === 'function') {
                window.resetModalClienteStep();
            }
            
            try {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Modal abierto exitosamente');
            } catch (error) {
                console.error('Error al abrir modal:', error);
                alert('Error al abrir el formulario: ' + error.message);
            }
        });
    });
    
    // Load all clients for dropdown
    function loadClients() {
        $.ajax({
            url: urlGetClients,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    clientesData = response.data;
                    
                    let options = '<option value=\"\">Seleccione un cliente</option>';
                    response.data.forEach(function(cliente) {
                        options += '<option value=\"' + cliente.id + '\">' + cliente.nombre + 
                                   (cliente.documento_identidad ? ' - ' + cliente.documento_identidad : '') + 
                                   '</option>';
                    });
                    
                    $('#cliente-select').html(options);
                }
            },
            error: function() {
                alert('Error al cargar los clientes');
            }
        });
    }
    
    // Handle client selection
    $('#cliente-select').on('change', function() {
        const clientId = $(this).val();
        
        if (clientId) {
            $.ajax({
                url: urlGetClientDetails,
                method: 'GET',
                data: { id: clientId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        $('#client-nombre').text(data.nombre || '-');
                        $('#client-documento').text(data.documento_identidad || '-');
                        $('#client-telefono').text(data.telefono || '-');
                        $('#client-ubicacion').text(data.ubicacion || '-');
                        $('#client-edad').text(data.edad || '-');
                        $('#client-status').text(data.status || '-');
                        
                        $('#client-info').addClass('active');
                    }
                },
                error: function() {
                    alert('Error al cargar los detalles del cliente');
                }
            });
        } else {
            $('#client-info').removeClass('active');
        }
    });
    
    // Generate invoice code
    function generateInvoiceCode() {
        $.ajax({
            url: urlGenerateCode,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#codigo-factura').val(response.codigo);
                }
            },
            error: function() {
                alert('Error al generar el código de factura');
            }
        });
    }
    
    // Load all products with stock
    function loadProducts() {
        $.ajax({
            url: urlGetProducts,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    productosData = response.data;
                }
            },
            error: function() {
                alert('Error al cargar los productos');
            }
        });
    }
    
    // Add new product card
    $('#add-product-btn').on('click', function() {
        addProductCard();
    });
    
    function addProductCard() {
        const template = document.getElementById('product-card-template');
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.product-item');
        
        card.setAttribute('data-card-index', productCardCounter);
        productCardCounter++;
        
        $('#products-grid').append(clone);
        attachProductCardEvents();
    }
    
    // Attach events to product items
    function attachProductCardEvents() {
        // Handle product selection button click
        $('.btn-select-producto').off('click').on('click', function() {
            const card = $(this).closest('.product-item');
            const cardIndex = card.data('card-index');
            
            // Función para calcular stock reservado
            function getStockReservado(idProducto, idLugar) {
                let totalReservado = 0;
                
                // Recorrer todas las tarjetas de productos existentes
                $('.product-item').each(function() {
                    const currentCard = $(this);
                    const currentCardIndex = currentCard.data('card-index');
                    
                    // No contar la tarjeta actual (la que estamos editando)
                    if (currentCardIndex === cardIndex) {
                        return; // continue
                    }
                    
                    const currentProductId = currentCard.find('.product-id').val();
                    const currentLugarId = currentCard.find('.product-lugar-id').val();
                    const currentQuantity = parseInt(currentCard.find('.product-quantity').val()) || 0;
                    
                    // Si es el mismo producto y lugar, sumar la cantidad
                    if (currentProductId == idProducto && currentLugarId == idLugar) {
                        totalReservado += currentQuantity;
                    }
                });
                
                return totalReservado;
            }
            
            // Open the modal in 'salidas' mode (since POS is selling products)
            if (typeof window.abrirModalSeleccionProducto === 'function') {
                window.abrirModalSeleccionProducto('salidas', function(producto) {
                    // Set product data in hidden fields
                    card.find('.product-id').val(producto.id);
                    card.find('.product-lugar-id').val(producto.stock_seleccionado.id_lugar);
                    
                    // Update display name
                    const displayName = (producto.marca || '') + ' ' + (producto.modelo || '') + ' (' + (producto.stock_seleccionado.lugar_nombre || '') + ')';
                    card.find('.product-name-display').text(displayName);
                    
                    // Set stock and cost
                    const stock = parseInt(producto.stock_seleccionado.cantidad) || 0;
                    const costo = parseFloat(producto.costo || 0);
                    
                    // Store data attributes for later use
                    card.find('.btn-select-producto').attr('data-stock', stock);
                    card.find('.btn-select-producto').attr('data-costo', costo);
                    card.find('.btn-select-producto').attr('data-precio', parseFloat(producto.precio_venta || 0));
                    
                    card.find('.stock-remaining').val(stock);
                    card.find('.product-cost').val(costo.toFixed(2));
                    card.find('.product-quantity').attr('max', stock).val(1);
                    
                    updateTotals();
                }, {
                    getStockReservado: getStockReservado  // Pasar la función al modal
                });
            } else {
                alert('Error: La función de selección de producto no está disponible');
            }
        });
        
        // Handle quantity change
        $('.product-quantity').off('input').on('input', function() {
            const card = $(this).closest('.product-item');
            const btn = card.find('.btn-select-producto');
            const stock = parseInt(btn.data('stock')) || 0;
            const quantity = parseInt($(this).val()) || 0;
            
            // Update remaining stock display
            const remaining = stock - quantity;
            const stockField = card.find('.stock-remaining');
            
            stockField.val(remaining);
            
            if (remaining < 0) {
                stockField.removeClass('stock-ok').addClass('stock-warning');
            } else {
                stockField.removeClass('stock-warning').addClass('stock-ok');
            }
            
            updateTotals();
        });
        
        // Handle delete button
        $('.delete-product').off('click').on('click', function() {
            $(this).closest('.product-item').remove();
            updateTotals();
        });
    }
    
    // Update totals (subtotal and precio venta)
    function updateTotals() {
        let subtotal = 0;
        let precioVentaTotal = 0;
        
        $('.product-item').each(function() {
            const productId = $(this).find('.product-id').val();
            const btn = $(this).find('.btn-select-producto');
            const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
            
            if (productId) {
                const costo = parseFloat(btn.data('costo')) || 0;
                const precioVenta = parseFloat(btn.data('precio')) || 0;
                
                subtotal += costo * quantity;
                precioVentaTotal += precioVenta * quantity;
            }
        });
        
        $('#subtotal-display').text(subtotal.toFixed(2));
        
        // Update the USDT input field (which will trigger carousel sync)
        $('#precio-usdt').val(precioVentaTotal.toFixed(2)).trigger('input');
        
        // Update currency conversions for subtotal
        updateConversions(subtotal, precioVentaTotal);
    }
    
    // Update currency conversions (legacy function for subtotal)
    function updateConversions(subtotal, precioVenta) {
        if (precioParalelo && precioOficial) {
            // Subtotal conversions
            const subtotalVes = subtotal * precioParalelo;
            const subtotalUsdOficial = subtotalVes / precioOficial;
            
            $('#subtotal-ves').text('Bs. ' + subtotalVes.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#subtotal-usd-oficial').text('$' + subtotalUsdOficial.toFixed(2));
        }
    }
    
    // ===== CURRENCY CAROUSEL FUNCTIONS =====
    
    // Initialize currency carousel
    function initCurrencyCarousel() {
        // Navigation buttons
        $('#carousel-prev').on('click', () => navigateCarousel(-1));
        $('#carousel-next').on('click', () => navigateCarousel(1));
        
        // Input listeners for each currency
        $('#precio-usdt').on('input', handleUsdtInput);
        $('#precio-usd-bcv').on('input', handleUsdBcvInput);
        $('#precio-ves').on('input', handleVesInput);
        
        // Touch events for mobile swipe
        const carousel = document.querySelector('.price-carousel');
        if (carousel) {
            carousel.addEventListener('touchstart', handleTouchStart, { passive: true });
            carousel.addEventListener('touchend', handleTouchEnd, { passive: true });
        }
    }
    
    // Handle touch start
    function handleTouchStart(e) {
        touchStartX = e.changedTouches[0].screenX;
    }
    
    // Handle touch end
    function handleTouchEnd(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }
    
    // Handle swipe gesture
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next page
                navigateCarousel(1);
            } else {
                // Swipe right - previous page
                navigateCarousel(-1);
            }
        }
    }
    
    // Navigate carousel
    function navigateCarousel(direction) {
        const newIndex = currentCurrencyPage + direction;
        if (newIndex >= 0 && newIndex < currencyPages.length) {
            goToCarouselPage(newIndex);
        }
    }
    
    // Go to specific page
    function goToCarouselPage(index) {
        // Update state
        currentCurrencyPage = index;
        
        // Update UI
        $('.carousel-page').removeClass('active');
        $('.carousel-page[data-currency=\\\"' + currencyPages[index] + '\\\"]').addClass('active');
        
        // Update hidden currency field
        let currencyValue = 'USDT'; // default
        if (currencyPages[index] === 'usdt') {
            currencyValue = 'USDT';
        } else if (currencyPages[index] === 'usd-bcv') {
            currencyValue = 'BCV';
        } else if (currencyPages[index] === 'ves') {
            currencyValue = 'VES';
        }
        $('#currency-selected').val(currencyValue);
    }
    
    // Handle USDT input (Page 1)
    function handleUsdtInput() {
        const usdt = parseFloat($(this).val()) || 0;
        
        if (precioParalelo && precioOficial) {
            // USDT → VES (Paralelo)
            const ves = usdt * precioParalelo;
            // USDT → USD BCV
            const usdBcv = ves / precioOficial;
            
            // Update display
            $('#usdt-to-ves').text('Bs. ' + ves.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#usdt-to-usd-bcv').text('$' + usdBcv.toFixed(2));
            
            // Sync other inputs (without triggering events)
            $('#precio-usd-bcv').off('input').val(usdBcv.toFixed(2)).on('input', handleUsdBcvInput);
            $('#precio-ves').off('input').val(ves.toFixed(2)).on('input', handleVesInput);
        }
        
        // Update hidden field - save the value in the selected currency (USDT)
        $('#monto-final').val(usdt.toFixed(2));
    }
    
    // Handle USD BCV input (Page 2)
    function handleUsdBcvInput() {
        const usdBcv = parseFloat($(this).val()) || 0;
        
        if (precioParalelo && precioOficial) {
            // USD BCV → VES (Oficial)
            const ves = usdBcv * precioOficial;
            // VES → USDT
            const usdt = ves / precioParalelo;
            
            // Update display
            $('#bcv-to-usdt').text('$' + usdt.toFixed(2));
            $('#bcv-to-ves').text('Bs. ' + ves.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            
            // Sync other inputs (without triggering events)
            $('#precio-usdt').off('input').val(usdt.toFixed(2)).on('input', handleUsdtInput);
            $('#precio-ves').off('input').val(ves.toFixed(2)).on('input', handleVesInput);
        }
        
        // Update hidden field - save the value in the selected currency (BCV)
        $('#monto-final').val(usdBcv.toFixed(2));
    }
    
    // Handle VES input (Page 3)
    function handleVesInput() {
        const ves = parseFloat($(this).val()) || 0;
        
        if (precioParalelo && precioOficial) {
            // VES → USDT
            const usdt = ves / precioParalelo;
            // VES → USD BCV
            const usdBcv = ves / precioOficial;
            
            // Update display
            $('#ves-to-usdt').text('$' + usdt.toFixed(2));
            $('#ves-to-usd-bcv').text('$' + usdBcv.toFixed(2));
            
            // Sync other inputs (without triggering events)
            $('#precio-usdt').off('input').val(usdt.toFixed(2)).on('input', handleUsdtInput);
            $('#precio-usd-bcv').off('input').val(usdBcv.toFixed(2)).on('input', handleUsdBcvInput);
        }
        
        // Update hidden field - save the value in the selected currency (VES)
        $('#monto-final').val(ves.toFixed(2));
    }
    
    
    // Load invoice data for editing
    function loadInvoiceData(id) {
        $.ajax({
            url: urlGetInvoiceData,
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Set invoice data
                    $('#codigo-factura').val(data.codigo);
                    $('#fecha-factura').val(data.fecha);
                    $('#concepto-factura').val(data.concepto || '');
                    $('#monto-final').val(parseFloat(data.monto_final).toFixed(2));
                    
                    // Set client if exists
                    if (data.id_cliente) {
                        $('#cliente-select').val(data.id_cliente);
                        $('#cliente-select').trigger('change');
                    }
                    
                    // Load items
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(function(item) {
                            addProductCardWithData(item);
                            originalItems[item.item_id] = item;
                        });
                    }
                    
                    // Calculate totals
                    updateTotals();
                }
            },
            error: function() {
                alert('Error al cargar los datos de la factura');
            }
        });
    }
    
    // Add product card with existing data (for edit mode)
    function addProductCardWithData(itemData) {
        const template = document.getElementById('product-card-template');
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.product-item');
        
        const cardIndex = productCardCounter;
        card.setAttribute('data-card-index', cardIndex);
        card.setAttribute('data-item-id', itemData.item_id);
        productCardCounter++;
        
        // Find the product in productosData
        const producto = productosData.find(p => 
            p.id_producto == itemData.id_producto && p.id_lugar == itemData.id_lugar
        );
        
        // Set hidden fields - always set these from itemData
        clone.querySelector('.product-id').value = itemData.id_producto;
        clone.querySelector('.product-lugar-id').value = itemData.id_lugar;
        
        // Set display name - use itemData if producto not found (e.g., stock = 0)
        let displayName;
        if (producto) {
            displayName = producto.display_name || 
                         ((producto.marca || '') + ' ' + (producto.modelo || ''));
        } else {
            // Producto no encontrado en productosData (probablemente stock = 0)
            // Usar los datos que vienen de la factura
            displayName = (itemData.producto_nombre || 'Producto sin nombre') + 
                         (itemData.lugar_nombre ? ' (' + itemData.lugar_nombre + ')' : '');
        }
        clone.querySelector('.product-name-display').textContent = displayName;
        
        // Store data in button for later use
        const btn = clone.querySelector('.btn-select-producto');
        if (producto) {
            // Usar datos del producto actual si está disponible
            btn.setAttribute('data-stock', producto.stock_disponible || 0);
            btn.setAttribute('data-costo', producto.costo || itemData.precio_unitario);
            btn.setAttribute('data-precio', producto.precio_venta || 0);
        } else {
            // Producto no encontrado (stock = 0), usar datos de la factura
            btn.setAttribute('data-stock', itemData.stock_disponible || 0);
            btn.setAttribute('data-costo', itemData.precio_unitario);
            btn.setAttribute('data-precio', itemData.precio_unitario); // Usar el mismo precio que tenía
        }
        
        // Set stock and cost fields
        const stockRemaining = clone.querySelector('.stock-remaining');
        const productCost = clone.querySelector('.product-cost');
        const productQuantity = clone.querySelector('.product-quantity');
        
        stockRemaining.value = itemData.stock_disponible + itemData.cantidad;
        productCost.value = parseFloat(itemData.precio_unitario).toFixed(2);
        productQuantity.value = itemData.cantidad;
        
        $('#products-grid').append(clone);
        attachProductCardEvents();
    }
    
    // Cancel button
    $('#btn-cancel').on('click', function() {
        if (confirm('¿Está seguro que desea cancelar? Se perderán todos los cambios.')) {
            if (editMode && facturaId) {
                window.location.href = '" . Url::to(['facturas/view', 'id' => '']) . "' + facturaId;
            } else {
                location.reload();
            }
        }
    });
    
    // Process invoice (create or update)
    $('#btn-process').on('click', function() {
        const codigo = $('#codigo-factura').val();
        const clienteId = $('#cliente-select').val();
        const concepto = $('#concepto-factura').val();
        const fecha = $('#fecha-factura').val();
        const subtotal = parseFloat($('#subtotal-display').text());
        const montoFinal = parseFloat($('#monto-final').val());
        
        // Validate
        if (!codigo) {
            alert('El código de factura es requerido');
            return;
        }
        
        // Validate client selection
        if (!clienteId) {
            alert('Debe seleccionar un cliente antes de procesar la factura');
            return;
        }
        
        // Get all products
        const items = [];
        let hasError = false;
        
        $('.product-item').each(function() {
            const idProducto = $(this).find('.product-id').val();
            const idLugar = $(this).find('.product-lugar-id').val();
            const btn = $(this).find('.btn-select-producto');
            const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
            const stock = parseInt(btn.data('stock')) || 0;
            const costo = parseFloat(btn.data('costo')) || 0;
            const itemId = $(this).attr('data-item-id') || null;
            
            if (!idProducto) {
                alert('Por favor, seleccione todos los productos');
                hasError = true;
                return false;
            }
            
            if (quantity <= 0) {
                alert('La cantidad debe ser mayor a 0');
                hasError = true;
                return false;
            }
            
            // En modo edición, considerar el stock original
            let availableStock = stock;
            if (editMode && itemId && originalItems[itemId]) {
                availableStock = stock + originalItems[itemId].cantidad;
            }
            
            if (quantity > availableStock && !editMode) {
                alert('La cantidad solicitada supera el stock disponible para uno o más productos');
                hasError = true;
                return false;
            }
            
            const item = {
                id_producto: idProducto,
                id_lugar: idLugar,
                cantidad: quantity,
                precio_unitario: costo
            };
            
            if (editMode && itemId) {
                item.item_id = itemId;
            }
            
            items.push(item);
        });
        
        if (hasError) return;
        
        if (items.length === 0) {
            alert('Debe agregar al menos un producto');
            return;
        }
        
        const confirmMsg = editMode ? '¿Está seguro que desea actualizar esta factura?' : '¿Está seguro que desea procesar esta factura?';
        if (!confirm(confirmMsg)) {
            return;
        }
        
        // Disable button
        const processingText = editMode ? 'Actualizando...' : 'Procesando...';
        $('#btn-process').prop('disabled', true).html('<span class=\"spinner-border spinner-border-sm\"></span> ' + processingText);
        
        // Get selected currency
        const currency = $('#currency-selected').val();
        
        // Prepare data
        const postData = {
            items: items,
            subtotal: subtotal,
            monto_final: montoFinal,
            currency: currency,
            '" . Yii::$app->request->csrfParam . "': '" . Yii::$app->request->csrfToken . "'
        };
        
        if (editMode) {
            postData.factura_id = facturaId;
        } else {
            postData.codigo = codigo;
            postData.id_cliente = clienteId || null;
            postData.concepto = concepto;
            postData.fecha = fecha;
        }
        
        // Send data to server
        $.ajax({
            url: editMode ? urlUpdateInvoice : urlProcessInvoice,
            method: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const successMsg = editMode ? 'Factura actualizada exitosamente.' : 'Factura procesada exitosamente. ID: ' + response.factura_id;
                    alert(successMsg);
                    // Redirigir a la vista de la factura
                    window.location.href = '" . Url::to(['facturas/view', 'id' => '']) . "' + response.factura_id;
                } else {
                    alert('Error: ' + response.message);
                    const btnText = editMode ? '<i class=\"bi bi-check-circle\"></i> Actualizar Factura' : '<i class=\"bi bi-check-circle\"></i> Procesar Factura';
                    $('#btn-process').prop('disabled', false).html(btnText);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al procesar la solicitud: ' + error);
                const btnText = editMode ? '<i class=\"bi bi-check-circle\"></i> Actualizar Factura' : '<i class=\"bi bi-check-circle\"></i> Procesar Factura';
                $('#btn-process').prop('disabled', false).html(btnText);
            }
        });
    });
    
    // Manejo del submit del formulario del modal de nuevo cliente
    $('#btn-modal-submit').on('click', function() {
        const form = document.getElementById('form-nuevo-cliente-modal');
        const nombre = $('#modal-cliente-nombre').val().trim();
        
        // Validar que al menos el nombre esté lleno
        if (!nombre) {
            alert('El nombre del cliente es obligatorio');
            return;
        }
        
        // Preparar datos
        const formData = {
            nombre: nombre,
            documento_identidad: $('#modal-cliente-documento').val().trim(),
            edad: $('#modal-cliente-edad').val(),
            telefono: $('#modal-cliente-telefono').val().trim(),
            ubicacion: $('#modal-cliente-ubicacion').val().trim(),
            status: $('#modal-cliente-status').val(),
            '" . Yii::$app->request->csrfParam . "': '" . Yii::$app->request->csrfToken . "'
        };
        
        // Deshabilitar botón
        $('#btn-modal-submit').prop('disabled', true).html('<span class=\"spinner-border spinner-border-sm me-2\"></span>Guardando...');
        
        // Enviar datos
        $.ajax({
            url: '" . Url::to(['clientes/create']) . "',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Cliente registrado exitosamente');
                    
                    // Cerrar modal
                    const modalElement = document.getElementById('modalNuevoCliente');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                    
                    // Recargar clientes y seleccionar el nuevo
                    loadClientsAndSelect(response.cliente_id);
                } else {
                    alert('Error al registrar el cliente: ' + (response.message || 'Error desconocido'));
                    $('#btn-modal-submit').prop('disabled', false).html('<i class=\"bi bi-check-circle\"></i> Registrar Cliente');
                }
            },
            error: function(xhr, status, error) {
                alert('Error al registrar el cliente. Por favor, intente nuevamente.');
                $('#btn-modal-submit').prop('disabled', false).html('<i class=\"bi bi-check-circle\"></i> Registrar Cliente');
            }
        });
    });
    
    // Función para recargar clientes y seleccionar uno específico
    function loadClientsAndSelect(clienteId) {
        $.ajax({
            url: urlGetClients,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    clientesData = response.data;
                    
                    let options = '<option value=\"\">Seleccione un cliente</option>';
                    response.data.forEach(function(cliente) {
                        const selected = (cliente.id == clienteId) ? ' selected' : '';
                        options += '<option value=\"' + cliente.id + '\"' + selected + '>' + cliente.nombre + 
                                   (cliente.documento_identidad ? ' - ' + cliente.documento_identidad : '') + 
                                   '</option>';
                    });
                    
                    $('#cliente-select').html(options);
                    
                    // Si se pasó un clienteId, cargar sus detalles
                    if (clienteId) {
                        $('#cliente-select').trigger('change');
                    }
                }
            },
            error: function() {
                alert('Error al cargar los clientes');
            }
        });
    }
    
    // ==============================================
    // MODO RÁPIDO - BARCODE SCANNER
    // ==============================================
    
    let modoActual = 'clasico';
    let scannerActivoPOS = false;
    let audioContextPOS = null;
    let productoRapidoActual = null;
    let procesandoCodigo = false;
    
    // Event listeners para toggle de modos
    $('#btn-modo-clasico, #btn-modo-rapido').on('click', function() {
        const nuevoModo = $(this).attr('id') === 'btn-modo-clasico' ? 'clasico' : 'rapido';
        cambiarModo(nuevoModo);
    });
    
    function cambiarModo(modo) {
        modoActual = modo;
        
        $('#btn-modo-clasico, #btn-modo-rapido').removeClass('active');
        
        if (modo === 'clasico') {
            $('#btn-modo-clasico').addClass('active');
            $('#scanner-container').hide();
            $('#add-product-btn').show();
            detenerScannerPOS();
        } else {
            $('#btn-modo-rapido').addClass('active');
            $('#scanner-container').show();
            $('#add-product-btn').hide();
            iniciarScannerPOS();
        }
    }
    
    // Reactivar scanner al hacer click en el contenedor
    $('#scanner-container').on('click', function() {
        reactivarScannerManual();
    });
    
    function iniciarScannerPOS() {
        if (scannerActivoPOS) return;
        
        if (typeof Quagga === 'undefined') {
            $('#scanner-status-pos').html('<i class=\"bi bi-exclamation-triangle text-danger\"></i> Error: QuaggaJS no está cargado');
            return;
        }
        
        Quagga.init({
            inputStream: {
                name: 'Live',
                type: 'LiveStream',
                target: document.querySelector('#barcode-reader-pos'),
                constraints: {
                    width: 640,
                    height: 400,
                    facingMode: 'environment'
                }
            },
            decoder: {
                readers: ['ean_reader', 'ean_8_reader', 'code_128_reader', 'code_39_reader', 'upc_reader', 'upc_e_reader']
            },
            locate: true
        }, function(err) {
            if (err) {
                $('#scanner-status-pos').html('<i class=\"bi bi-exclamation-triangle text-danger\"></i> Error: ' + err.message);
                return;
            }
            
            scannerActivoPOS = true;
            Quagga.start();
            $('#scanner-status-pos').html('<i class=\"bi bi-camera-video text-success\"></i> Cámara activa - Escanee un código');
        });
        
        // Limpiar listeners previos para evitar duplicados
        Quagga.offDetected(procesarCodigoEscaneadoPOS);
        Quagga.onDetected(procesarCodigoEscaneadoPOS);
    }
    
    function detenerScannerPOS() {
        if (scannerActivoPOS && typeof Quagga !== 'undefined') {
            Quagga.stop();
            scannerActivoPOS = false;
        }
    }
    
    function reanudarScannerPOS() {
        procesandoCodigo = false;
        scannerActivoPOS = false;
        
        if (modoActual === 'rapido') {
            $('#scanner-status-pos').html('<i class=\"bi bi-hand-index-thumb text-primary\"></i> Toca la cámara para escanear de nuevo');
            $('#scanner-overlay-pos').css('border-color', '#6c757d'); // Cambiar borde a gris para indicar pausa
        }
    }
    
    // Función para reactivar manualmente
    function reactivarScannerManual() {
        if (modoActual === 'rapido' && !scannerActivoPOS && !procesandoCodigo && typeof Quagga !== 'undefined') {
            iniciarScannerPOS();
            $('#scanner-overlay-pos').css('border-color', '#28a745'); // Borde verde activo
        }
    }
    
    function procesarCodigoEscaneadoPOS(result) {
        if (procesandoCodigo) return;
        
        const codigo = result.codeResult.code;
        console.log('Código detectado:', codigo);
        
        procesandoCodigo = true;
        
        // Pausar scanner
        if (typeof Quagga !== 'undefined') {
            Quagga.stop();
            scannerActivoPOS = false;
        }
        
        // Mostrar indicador de carga (opcional, por ahora un log)
        console.log('Buscando producto...');
        
        // Buscar producto por código de barras
        $.ajax({
            url: '" . Url::to(['pos/get-producto-by-barcode']) . "',
            method: 'GET',
            data: { codigo: codigo },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    const producto = response.data;
                    
                    try {
                        // Verificar si el producto ya está en el grid
                        const productoExistente = encontrarProductoEnGridPorCodigo(codigo);
                        
                        if (productoExistente) {
                            console.log('Producto existente, incrementando cantidad');
                            incrementarCantidadProductoRapido(productoExistente, producto);
                        } else {
                            console.log('Producto nuevo, abriendo modal');
                            mostrarModalConfirmacionRapida(producto);
                        }
                    } catch (e) {
                        console.error('Error procesando producto:', e);
                        alert('Error interno al procesar el producto: ' + e.message);
                        reanudarScannerPOS();
                    }
                } else {
                    alert('Producto no encontrado: ' + codigo);
                    reanudarScannerPOS();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                console.log('Respuesta:', xhr.responseText);
                alert('Error de conexión al buscar el producto. Ver consola para detalles.');
                reanudarScannerPOS();
            }
        });
    }
    
    function encontrarProductoEnGridPorCodigo(codigoBarra) {
        let cardEncontrada = null;
        $('.product-item').each(function() {
            const idProducto = $(this).find('.product-id').val();
            // Buscar en productosData si tiene el mismo código
            const prodData = productosData.find(p => p.id == idProducto && p.codigo_barra === codigoBarra);
            if (prodData) {
                cardEncontrada = $(this);
                return false; // break
            }
        });
        return cardEncontrada;
    }
    
    function incrementarCantidadProductoRapido(card, producto) {
        const cantidadActual = parseInt(card.find('.product-quantity').val()) || 0;
        const nuevaCantidad = cantidadActual + 1;
        
        // Validar stock total
        if (nuevaCantidad > producto.stock_total) {
            alert('Error: La cantidad excede el stock disponible (' + producto.stock_total + ' unidades)');
            reanudarScannerPOS();
            return;
        }
        
        // Incrementar cantidad
        card.find('.product-quantity').val(nuevaCantidad).trigger('input');
        
        // Sonido de confirmación
        reproducirSonidoA4POS();
        
        // Reanudar scanner
        reanudarScannerPOS();
    }
    
    function calcularStockReservadoGlobal(idProducto, idLugar) {
        let totalReservado = 0;
        
        $('.product-item').each(function() {
            const currentCard = $(this);
            const currentProductId = currentCard.find('.product-id').val();
            const currentLugarId = currentCard.find('.product-lugar-id').val();
            const currentQuantity = parseInt(currentCard.find('.product-quantity').val()) || 0;
            
            // Si es el mismo producto y lugar, sumar la cantidad
            if (currentProductId == idProducto && currentLugarId == idLugar) {
                totalReservado += currentQuantity;
            }
        });
        
        return totalReservado;
    }
    
    function mostrarModalConfirmacionRapida(producto) {
        console.log('Preparando modal para:', producto);
        productoRapidoActual = producto;
        
        try {
            // Llenar datos del modal
            $('#modal-rapido-producto-foto').attr('src', producto.foto_url || '" . Yii::getAlias('@web') . "/images/no-image.png');
            $('#modal-rapido-producto-nombre').text((producto.marca || '') + ' ' + (producto.modelo || ''));
            $('#modal-rapido-producto-descripcion').text(producto.descripcion || '');
            $('#modal-rapido-producto-precio').text('$' + parseFloat(producto.precio_venta || 0).toFixed(2));
            $('#modal-rapido-producto-stock-total').text(producto.stock_total + ' uds');
            
            // Llenar selector de ubicaciones
            const selectUbicacion = $('#modal-rapido-ubicacion');
            selectUbicacion.empty();
            
            if (producto.stocks_disponibles && producto.stocks_disponibles.length > 0) {
                let totalStockReal = 0;
                
                producto.stocks_disponibles.forEach(function(stock) {
                    // Calcular stock real disponible (Base de datos - Grid POS)
                    const reservado = calcularStockReservadoGlobal(producto.id, stock.id_lugar);
                    const disponibleReal = Math.max(0, stock.cantidad - reservado);
                    totalStockReal += disponibleReal;
                    
                    const optionText = stock.lugar_nombre + ' (' + disponibleReal + ' uds disponibles)';
                    
                    const option = $('<option></option>')
                        .val(stock.id_lugar)
                        .text(optionText)
                        .data('stock-id', stock.id)
                        .data('stock-cantidad', disponibleReal); // Guardamos el disponible real
                    
                    // Deshabilitar si no hay stock real
                    if (disponibleReal <= 0) {
                        option.prop('disabled', true);
                        option.text(stock.lugar_nombre + ' (Sin stock disponible)');
                    }
                        
                    selectUbicacion.append(option);
                });
                
                // Actualizar stock total visual
                $('#modal-rapido-producto-stock-total').text(totalStockReal + ' uds');
                
                // Seleccionar primera opción disponible
                const firstAvailable = selectUbicacion.find('option:not(:disabled)').first();
                if (firstAvailable.length > 0) {
                    selectUbicacion.val(firstAvailable.val());
                }
                
                // Si solo hay una ubicación, ocultar selector (pero mantener lógica)
                if (producto.stocks_disponibles.length === 1) {
                    // Si la única ubicación no tiene stock, mostrar error o alerta visual
                    if (selectUbicacion.find('option:not(:disabled)').length === 0) {
                         $('#modal-rapido-ubicacion-container').show(); // Mostrar para que vea que no hay stock
                    } else {
                         $('#modal-rapido-ubicacion-container').hide();
                    }
                } else {
                    $('#modal-rapido-ubicacion-container').show();
                }
            } else {
                console.warn('El producto no tiene stocks_disponibles');
                $('#modal-rapido-ubicacion-container').hide();
            }
            
            // Actualizar stock de ubicación seleccionada
            actualizarStockUbicacionRapido();
            
            // Resetear cantidad
            $('#modal-rapido-cantidad').val(1);
            
            // Sonido
            reproducirSonidoA4POS();
            
            // Abrir modal de forma robusta
            const modalElement = document.getElementById('modalConfirmacionRapida');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Modal abierto');
            } else {
                console.error('Elemento modalConfirmacionRapida no encontrado en el DOM');
                alert('Error: No se encontró el modal de confirmación en la página.');
            }
        } catch (e) {
            console.error('Error al mostrar modal:', e);
            alert('Error al mostrar el modal: ' + e.message);
        }
    }
    
    function actualizarStockUbicacionRapido() {
        const selectedOption = $('#modal-rapido-ubicacion option:selected');
        const stockCantidad = selectedOption.data('stock-cantidad') || 0;
        
        $('#modal-rapido-stock-ubicacion').text(stockCantidad);
        $('#modal-rapido-cantidad').attr('max', stockCantidad);
    }
    
    // Event listener para cambio de ubicación
    $('#modal-rapido-ubicacion').on('change', actualizarStockUbicacionRapido);
    
    // Event listener para botón confirmar
    $('#btn-confirmar-rapido').on('click', function() {
        if (!productoRapidoActual) return;
        
        const selectedOption = $('#modal-rapido-ubicacion option:selected');
        const idLugar = selectedOption.val();
        const stockId = selectedOption.data('stock-id');
        const stockCantidad = selectedOption.data('stock-cantidad') || 0;
        const cantidad = parseInt($('#modal-rapido-cantidad').val()) || 1;
        
        // Validar cantidad
        if (cantidad > stockCantidad) {
            alert('La cantidad excede el stock disponible en esta ubicación');
            return;
        }
        
        // Crear objeto de stock para agregar la tarjeta
        const productoConStock = {
            ...productoRapidoActual,
            stock_seleccionado: {
                id: stockId,
                id_lugar: idLugar,
                cantidad: stockCantidad,
                lugar_nombre: selectedOption.text().split(' (')[0]
            }
        };
        
        // Agregar la tarjeta de producto
        addProductCard();
        
        // Obtener la última tarjeta agregada (la que acabamos de crear)
        const card = $('.product-item').last();
        const cardIndex = card.data('card-index');
        
        // Set product data
        card.find('.product-id').val(productoRapidoActual.id);
        card.find('.product-lugar-id').val(idLugar);
        
        // Update display name
        const displayName = (productoRapidoActual.marca || '') + ' ' + (productoRapidoActual.modelo || '') + ' (' + productoConStock.stock_seleccionado.lugar_nombre + ')';
        card.find('.product-name-display').text(displayName);
        
        // Set stock and cost
        const costo = parseFloat(productoRapidoActual.costo || 0);
        const precioVenta = parseFloat(productoRapidoActual.precio_venta || 0);
        
        card.find('.btn-select-producto').attr('data-stock', stockCantidad);
        card.find('.btn-select-producto').attr('data-costo', costo);
        card.find('.btn-select-producto').attr('data-precio', precioVenta);
        
        card.find('.stock-remaining').val(stockCantidad - cantidad);
        card.find('.product-cost').val(costo.toFixed(2));
        card.find('.product-quantity').attr('max', stockCantidad).val(cantidad);
        
        updateTotals();
        
        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmacionRapida')).hide();
        
        // Reanudar scanner
        reanudarScannerPOS();
    });
    
    // Event listener para cerrar modal
    $('#modalConfirmacionRapida').on('hidden.bs.modal', function() {
        productoRapidoActual = null;
        reanudarScannerPOS();
    });
    
    function reproducirSonidoA4POS() {
        if (!audioContextPOS) {
            audioContextPOS = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        const oscillator = audioContextPOS.createOscillator();
        const gainNode = audioContextPOS.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContextPOS.destination);
        
        oscillator.frequency.value = 1780; // A4
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContextPOS.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContextPOS.currentTime + 0.1);
        
        oscillator.start(audioContextPOS.currentTime);
        oscillator.stop(audioContextPOS.currentTime + 0.2);
    }
", \yii\web\View::POS_END);
?>

