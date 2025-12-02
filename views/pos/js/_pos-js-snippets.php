<?php

use yii\helpers\Url;

// Prepare all variables for the JavaScript
$editModeJs = $editMode ? 'true' : 'false';
$facturaIdJs = $facturaId ? $facturaId : 'null';
$precioParaleloJs = $precioParalelo ? $precioParalelo->precio_ves : 'null';
$precioOficialJs = $precioOficial ? $precioOficial->precio_ves : 'null';

// URLs
$urlGetClients = Url::to(['pos/get-clients']);
$urlGetClientDetails = Url::to(['pos/get-client-details']);
$urlGenerateCode = Url::to(['pos/generate-invoice-code']);
$urlGetProducts = Url::to(['pos/get-products']);
$urlProcessInvoice = Url::to(['pos/process-invoice']);
$urlUpdateInvoice = Url::to(['pos/update-invoice']);
$urlGetInvoiceData = Url::to(['pos/get-invoice-data']);

// Other URLs
$urlClientesCreate = Url::to(['clientes/create']);
$urlFacturasView = Url::to(['facturas/view', 'id' => '']);
$urlPosGetProductoByBarcode = Url::to(['pos/get-producto-by-barcode']);
$webAlias = Yii::getAlias('@web');

// CSRF
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$script = <<<JS
    let clientesData = [];
    let productosData = [];
    let productCardCounter = 0;
    const editMode = {$editModeJs};
    const facturaId = {$facturaIdJs};
    let originalItems = {}; // Para almacenar los items originales en modo edición
    
    // Dollar exchange rates
    const precioParalelo = {$precioParaleloJs};
    const precioOficial = {$precioOficialJs};
    
    // URLs for AJAX calls
    const urlGetClients = '{$urlGetClients}';
    const urlGetClientDetails = '{$urlGetClientDetails}';
    const urlGenerateCode = '{$urlGenerateCode}';
    const urlGetProducts = '{$urlGetProducts}';
    const urlProcessInvoice = '{$urlProcessInvoice}';
    const urlUpdateInvoice = '{$urlUpdateInvoice}';
    const urlGetInvoiceData = '{$urlGetInvoiceData}';
    
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
                    
                    let options = '<option value="">Seleccione un cliente</option>';
                    response.data.forEach(function(cliente) {
                        options += '<option value="' + cliente.id + '">' + cliente.nombre + 
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
        $('.carousel-page[data-currency="' + currencyPages[index] + '"]').addClass('active');
        
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
                window.location.href = '{$urlFacturasView}' + facturaId;
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
        $('#btn-process').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> ' + processingText);
        
        // Get selected currency
        const currency = $('#currency-selected').val();
        
        // Prepare data
        const postData = {
            items: items,
            subtotal: subtotal,
            monto_final: montoFinal,
            currency: currency,
            '{$csrfParam}': '{$csrfToken}'
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
                    window.location.href = '{$urlFacturasView}' + response.factura_id;
                } else {
                    alert('Error: ' + response.message);
                    const btnText = editMode ? '<i class="bi bi-check-circle"></i> Actualizar Factura' : '<i class="bi bi-check-circle"></i> Procesar Factura';
                    $('#btn-process').prop('disabled', false).html(btnText);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al procesar la solicitud: ' + error);
                const btnText = editMode ? '<i class="bi bi-check-circle"></i> Actualizar Factura' : '<i class="bi bi-check-circle"></i> Procesar Factura';
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
            '{$csrfParam}': '{$csrfToken}'
        };
        
        // Deshabilitar botón
        $('#btn-modal-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        
        // Enviar datos
        $.ajax({
            url: '{$urlClientesCreate}',
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
                    $('#btn-modal-submit').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Registrar Cliente');
                }
            },
            error: function(xhr, status, error) {
                alert('Error al registrar el cliente. Por favor, intente nuevamente.');
                $('#btn-modal-submit').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Registrar Cliente');
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
                    
                    let options = '<option value="">Seleccione un cliente</option>';
                    response.data.forEach(function(cliente) {
                        const selected = (cliente.id == clienteId) ? ' selected' : '';
                        options += '<option value="' + cliente.id + '"' + selected + '>' + cliente.nombre + 
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
            $('#scanner-status-pos').html('<i class="bi bi-exclamation-triangle text-danger"></i> Error: QuaggaJS no está cargado');
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
                $('#scanner-status-pos').html('<i class="bi bi-exclamation-triangle text-danger"></i> Error: ' + err.message);
                return;
            }
            
            scannerActivoPOS = true;
            Quagga.start();
            $('#scanner-status-pos').html('<i class="bi bi-camera-video text-success"></i> Cámara activa - Escanee un código');
        });
        
        // Limpiar listeners previos para evitar duplicados
        Quagga.offDetected(procesarCodigoEscaneadoPOS);
        Quagga.offProcessed(dibujarIndicadorDeteccion);
        
        // Agregar listener para detectar códigos
        Quagga.onDetected(procesarCodigoEscaneadoPOS);
        
        // Agregar listener para dibujar el indicador de detección
        Quagga.onProcessed(dibujarIndicadorDeteccion);
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
            $('#scanner-status-pos').html('<i class="bi bi-hand-index-thumb text-primary"></i> Toca la cámara para escanear de nuevo');
            $('#scanner-overlay-pos').css('border-color', '#6c757d'); // Cambiar borde a gris para indicar pausa
        }
    }
    
    // Función para dibujar el indicador de detección de código de barras
    function dibujarIndicadorDeteccion(result) {
        const drawingCtx = Quagga.canvas.ctx.overlay;
        const drawingCanvas = Quagga.canvas.dom.overlay;
        
        if (result) {
            // Limpiar canvas
            if (result.boxes) {
                drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                
                // Dibujar todas las cajas detectadas (en azul)
                result.boxes.filter(function (box) {
                    return box !== result.box;
                }).forEach(function (box) {
                    Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "blue", lineWidth: 2});
                });
            }
            
            // Dibujar la caja principal detectada (en verde)
            if (result.box) {
                Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 3});
            }
            
            // Si se detectó un código, dibujar la línea del código (en rojo)
            if (result.codeResult && result.codeResult.code) {
                Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
            }
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
            url: '{$urlPosGetProductoByBarcode}',
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
            // Esperar 3 segundos antes de reanudar
            $('#scanner-status-pos').html('<i class="bi bi-hourglass-split text-warning"></i> Esperando 3 segundos...');
            setTimeout(function() {
                reanudarScannerPOS();
            }, 3000);
            return;
        }
        
        // Incrementar cantidad
        card.find('.product-quantity').val(nuevaCantidad).trigger('input');
        
        // Sonido de confirmación
        reproducirSonidoA4POS();
        
        // Mostrar mensaje de espera
        $('#scanner-status-pos').html('<i class="bi bi-check-circle text-success"></i> Producto agregado - Esperando 3 segundos...');
        $('#scanner-overlay-pos').css('border-color', '#ffc107'); // Borde amarillo para indicar espera
        
        // Esperar 3 segundos antes de reanudar scanner
        setTimeout(function() {
            reanudarScannerPOS();
        }, 3000);
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
            $('#modal-rapido-producto-foto').attr('src', producto.foto_url || '{$webAlias}/images/no-image.png');
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
        
        // Limpiar variable antes de cerrar modal para evitar doble temporizador
        productoRapidoActual = null;
        
        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmacionRapida')).hide();
        
        // Mostrar mensaje de espera
        $('#scanner-status-pos').html('<i class="bi bi-check-circle text-success"></i> Producto agregado - Esperando 3 segundos...');
        $('#scanner-overlay-pos').css('border-color', '#ffc107'); // Borde amarillo para indicar espera
        
        // Esperar 3 segundos antes de reanudar scanner
        setTimeout(function() {
            reanudarScannerPOS();
        }, 3000);
    });
    
    // Event listener para cerrar modal (solo si se cancela)
    $('#modalConfirmacionRapida').on('hidden.bs.modal', function() {
        // Solo reanudar si el producto no fue confirmado (fue cancelado)
        if (productoRapidoActual !== null) {
            productoRapidoActual = null;
            // Mostrar mensaje de espera
            $('#scanner-status-pos').html('<i class="bi bi-x-circle text-danger"></i> Escaneo cancelado - Esperando 3 segundos...');
            $('#scanner-overlay-pos').css('border-color', '#ffc107');
            // Esperar 3 segundos antes de reanudar
            setTimeout(function() {
                reanudarScannerPOS();
            }, 3000);
        }
    });
    
    function reproducirSonidoA4POS() {
        if (!audioContextPOS) {
            audioContextPOS = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        const oscillator1 = audioContextPOS.createOscillator();
        const oscillator2 = audioContextPOS.createOscillator();
        const oscillator3 = audioContextPOS.createOscillator();
        const gainNode = audioContextPOS.createGain();
        
        oscillator1.connect(gainNode);
        oscillator2.connect(gainNode);
        oscillator3.connect(gainNode);
        gainNode.connect(audioContextPOS.destination);
        
        oscillator1.frequency.value = 1780; // Frecuencia original
        oscillator1.type = 'square';
        
        oscillator2.frequency.value = 440; // Nueva frecuencia
        oscillator2.type = 'square';
        
        oscillator3.frequency.value = 650; // Nueva frecuencia (E)
        oscillator3.type = 'square';
        
        gainNode.gain.setValueAtTime(0.42, audioContextPOS.currentTime); // 0.3 * 1.4 = 0.42 (40% más de ganancia)
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContextPOS.currentTime + 0.1);
        
        oscillator1.start(audioContextPOS.currentTime+0.1);
        oscillator2.start(audioContextPOS.currentTime);
        oscillator3.start(audioContextPOS.currentTime+0.1);
        
        oscillator1.stop(audioContextPOS.currentTime + 0.23);
        oscillator2.stop(audioContextPOS.currentTime + 0.23);
        oscillator3.stop(audioContextPOS.currentTime + 0.23);
    }
JS;

$this->registerJS($script, \yii\web\View::POS_END);
