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
                <div class="total-value">$<span id="subtotal-display">0.00</span></div>
                
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
                <div>
                    <input type="number" 
                           id="monto-final" 
                           class="form-control form-control-lg text-center fw-bold" 
                           step="0.01" 
                           value="0.00"
                           style="font-size: 1.5rem; border: 2px solid #dee2e6;">
                </div>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <div class="conversion-subtexts">
                        <div class="conversion-item">
                            En Bolívares al cambio paralelo: <strong id="precio-venta-ves">Bs. 0,00</strong>
                        </div>
                        <div class="conversion-item">
                            En Dólares a Tasa BCV: <strong id="precio-venta-usd-oficial">$0.00</strong>
                        </div>
                    </div>
                <?php endif; ?>
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
                <select class="form-select product-select" required>
                    <option value="">Seleccione un producto</option>
                </select>
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
        
        // Listen for manual changes to precio de venta
        $('#monto-final').on('input', function() {
            const subtotal = parseFloat($('#subtotal-display').text()) || 0;
            const precioVenta = parseFloat($(this).val()) || 0;
            updateConversions(subtotal, precioVenta);
        });
        
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
        
        // Populate product dropdown
        const productSelect = clone.querySelector('.product-select');
        let options = '<option value=\"\">Seleccione un producto</option>';
        productosData.forEach(function(producto) {
            options += '<option value=\"' + producto.id_producto + '\" ' +
                       'data-lugar=\"' + producto.id_lugar + '\" ' +
                       'data-stock=\"' + producto.stock_disponible + '\" ' +
                       'data-costo=\"' + producto.costo + '\" ' +
                       'data-precio=\"' + producto.precio_venta + '\">' +
                       producto.display_name + '</option>';
        });
        productSelect.innerHTML = options;
        
        $('#products-grid').append(clone);
        attachProductCardEvents();
    }
    
    // Attach events to product items
    function attachProductCardEvents() {
        // Handle product selection
        $('.product-select').off('change').on('change', function() {
            const card = $(this).closest('.product-item');
            const selectedOption = $(this).find('option:selected');
            
            const stock = parseInt(selectedOption.data('stock')) || 0;
            const costo = parseFloat(selectedOption.data('costo')) || 0;
            
            card.find('.stock-remaining').val(stock);
            card.find('.product-cost').val(costo.toFixed(2));
            card.find('.product-quantity').attr('max', stock).val(1);
            
            updateTotals();
        });
        
        // Handle quantity change
        $('.product-quantity').off('input').on('input', function() {
            const card = $(this).closest('.product-item');
            const productSelect = card.find('.product-select');
            const selectedOption = productSelect.find('option:selected');
            const stock = parseInt(selectedOption.data('stock')) || 0;
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
            const productSelect = $(this).find('.product-select');
            const selectedOption = productSelect.find('option:selected');
            const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
            
            if (selectedOption.val()) {
                const costo = parseFloat(selectedOption.data('costo')) || 0;
                const precioVenta = parseFloat(selectedOption.data('precio')) || 0;
                
                subtotal += costo * quantity;
                precioVentaTotal += precioVenta * quantity;
            }
        });
        
        $('#subtotal-display').text(subtotal.toFixed(2));
        $('#monto-final').val(precioVentaTotal.toFixed(2));
        
        // Update currency conversions
        updateConversions(subtotal, precioVentaTotal);
    }
    
    // Update currency conversions
    function updateConversions(subtotal, precioVenta) {
        if (precioParalelo && precioOficial) {
            // Subtotal conversions
            const subtotalVes = subtotal * precioParalelo;
            const subtotalUsdOficial = subtotalVes / precioOficial;
            
            $('#subtotal-ves').text('Bs. ' + subtotalVes.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#subtotal-usd-oficial').text('$' + subtotalUsdOficial.toFixed(2));
            
            // Precio de venta conversions
            const precioVentaVes = precioVenta * precioParalelo;
            const precioVentaUsdOficial = precioVentaVes / precioOficial;
            
            $('#precio-venta-ves').text('Bs. ' + precioVentaVes.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#precio-venta-usd-oficial').text('$' + precioVentaUsdOficial.toFixed(2));
        }
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
        
        // Populate product dropdown
        const productSelect = clone.querySelector('.product-select');
        let options = '<option value=\"\">Seleccione un producto</option>';
        productosData.forEach(function(producto) {
            const selected = (producto.id_producto == itemData.id_producto && producto.id_lugar == itemData.id_lugar) ? ' selected' : '';
            options += '<option value=\"' + producto.id_producto + '\" ' +
                       'data-lugar=\"' + producto.id_lugar + '\" ' +
                       'data-stock=\"' + producto.stock_disponible + '\" ' +
                       'data-costo=\"' + producto.costo + '\" ' +
                       'data-precio=\"' + producto.precio_venta + '\"' + selected + '>' +
                       producto.display_name + '</option>';
        });
        productSelect.innerHTML = options;
        
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
            const productSelect = $(this).find('.product-select');
            const selectedOption = productSelect.find('option:selected');
            const idProducto = selectedOption.val();
            const idLugar = selectedOption.data('lugar');
            const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
            const stock = parseInt(selectedOption.data('stock')) || 0;
            const costo = parseFloat(selectedOption.data('costo')) || 0;
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
        
        // Prepare data
        const postData = {
            items: items,
            subtotal: subtotal,
            monto_final: montoFinal,
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
", \yii\web\View::POS_END);
?>

