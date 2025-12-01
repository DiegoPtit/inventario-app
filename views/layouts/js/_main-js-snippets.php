<?php
$userIsGuest = Yii::$app->user->isGuest;
$userIdentity = Yii::$app->user->identity;
$modalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->modalClosed ?? '0') : '0';
$dateModalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->dateModalClosed ?? '') : '';
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

// URLs para HEREDOC
$urlUpdateParallel = \yii\helpers\Url::to(['site/update-parallel-dollar-rate']);
$urlGetDataCierre = \yii\helpers\Url::to(['historico-inventarios/get-data-cierre']);
$urlRegistrarCierre = \yii\helpers\Url::to(['historico-inventarios/registrar-cierre']);
$urlIndexInventarios = \yii\helpers\Url::to(['historico-inventarios/index']);
$urlGetClientesPendientes = \yii\helpers\Url::to(['site/get-clientes-pendientes']);
$urlFacturaView = \yii\helpers\Url::to(['facturas/view', 'id' => '__ID__']);

// Variables JavaScript
$userIsGuestJS = $userIsGuest ? 'true' : 'false';

// Event listener condicional
$loadEventListener = (!$userIsGuest && $userIdentity) ? "
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (puedeVerModal()) {
                cargarClientesPendientes();
            }
        }, 1000); // Esperar 1 segundo después de cargar la página
    });" : "";

$this->registerJs(<<<JS
// JavaScript code goes here



JS
);


$this->registerJs(<<<JS
// Verificar si el modal existe antes de ejecutar el código
if (document.getElementById('modalNuevoCliente')) {
    let currentModalStep = 1;
    const totalModalSteps = 3;

    // Función para actualizar el paso del modal
    function updateModalStep() {
    // Actualizar contenido
    document.querySelectorAll('.step-content-modal').forEach(content => {
        content.classList.remove('active');
    });
    const activeContent = document.querySelector('.step-content-modal[data-step="' + currentModalStep + '"]');
    if (activeContent) {
        activeContent.classList.add('active');
    }
    
    // Actualizar indicadores
    document.querySelectorAll('.step-modal').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNum < currentModalStep) {
            step.classList.add('completed');
        } else if (stepNum === currentModalStep) {
            step.classList.add('active');
        }
    });
    
    // Actualizar barra de progreso
    const progress = ((currentModalStep - 1) / (totalModalSteps - 1)) * 100;
    const progressBar = document.getElementById('stepper-progress-modal');
    if (progressBar) {
        progressBar.style.width = progress + '%';
    }
    
    // Actualizar botones - CON VERIFICACIONES DE NULL
    const btnPrevious = document.getElementById('btn-modal-previous');
    const btnNext = document.getElementById('btn-modal-next');
    const btnSubmit = document.getElementById('btn-modal-submit');
    
    if (btnPrevious) {
        if (currentModalStep === 1) {
            btnPrevious.style.display = 'none';
        } else {
            btnPrevious.style.display = 'inline-block';
        }
    }
    
    if (btnNext && btnSubmit) {
        if (currentModalStep === totalModalSteps) {
            btnNext.style.display = 'none';
            btnSubmit.style.display = 'inline-block';
        } else {
            btnNext.style.display = 'inline-block';
            btnSubmit.style.display = 'none';
        }
    }
    }

    // Botón siguiente del modal
    const btnNext = document.getElementById('btn-modal-next');
    if (btnNext) {
        btnNext.addEventListener('click', function() {
            if (currentModalStep < totalModalSteps) {
                currentModalStep++;
                updateModalStep();
            }
        });
    }

    // Botón anterior del modal
    const btnPrevious = document.getElementById('btn-modal-previous');
    if (btnPrevious) {
        btnPrevious.addEventListener('click', function() {
            if (currentModalStep > 1) {
                currentModalStep--;
                updateModalStep();
            }
        });
    }

    // Click en los pasos del stepper del modal
    document.querySelectorAll('.step-modal').forEach((step, index) => {
        step.addEventListener('click', function() {
            currentModalStep = index + 1;
            updateModalStep();
        });
    });

    // Manejo de selección de status en el modal
    document.querySelectorAll('.status-card-modal').forEach(card => {
        card.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            
            // Remover selección de todas las tarjetas
            document.querySelectorAll('.status-card-modal').forEach(c => c.classList.remove('selected'));
            
            // Seleccionar esta tarjeta
            this.classList.add('selected');
            
            // Actualizar el input hidden
            const statusInput = document.getElementById('modal-cliente-status');
            if (statusInput) {
                statusInput.value = status;
            }
        });
    });

    // Reset del modal cuando se cierra
    const modal = document.getElementById('modalNuevoCliente');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            currentModalStep = 1;
            updateModalStep();
            
            const form = document.getElementById('form-nuevo-cliente-modal');
            if (form) form.reset();
            
            const statusInput = document.getElementById('modal-cliente-status');
            if (statusInput) statusInput.value = 'Solvente';
            
            document.querySelectorAll('.status-card-modal').forEach(c => c.classList.remove('selected'));
            const solventeCard = document.querySelector('.status-card-modal[data-status="Solvente"]');
            if (solventeCard) solventeCard.classList.add('selected');
        });
    }

    // Inicializar modal step solo si los elementos existen
    if (btnNext && btnPrevious) {
        updateModalStep();
    }
    
    // Exponer función global para resetear el modal
    window.resetModalClienteStep = function() {
        currentModalStep = 1;
        updateModalStep();
        
        const statusInput = document.getElementById('modal-cliente-status');
        if (statusInput) statusInput.value = 'Solvente';
        
        document.querySelectorAll('.status-card-modal').forEach(c => c.classList.remove('selected'));
        const solventeCard = document.querySelector('.status-card-modal[data-status="Solvente"]');
        if (solventeCard) {
            solventeCard.classList.add('selected');
        }
    };
}

// Manejo del modal de actualización de precio paralelo
if (document.getElementById('modalActualizarPrecioParalelo')) {
    // Manejar checkbox de confirmación
    document.getElementById('confirmar-actualizacion-precio').addEventListener('change', function() {
        document.getElementById('btn-confirmar-actualizacion-precio').disabled = !this.checked;
    });
    
    // Manejar clic en botón de confirmar actualización
    document.getElementById('btn-confirmar-actualizacion-precio').addEventListener('click', function() {
        const precio = document.getElementById('precio-paralelo-input').value;
        const observaciones = document.getElementById('observaciones-precio-paralelo').value;
        
        if (!precio || precio <= 0) {
            alert('Por favor ingrese un precio válido mayor a 0');
            return;
        }
        
        const confirmacion = confirm('¿Está seguro de actualizar el precio paralelo a ' + precio + ' VES?');
        
        if (!confirmacion) {
            return;
        }
        
        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
        
        // Preparar datos del formulario
        const formData = new FormData();
        formData.append('precio_paralelo', precio);
        formData.append('observaciones', observaciones);
        formData.append('{$csrfParam}', '{$csrfToken}');
        
        // Enviar datos
        fetch('{$urlUpdateParallel}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Precio paralelo actualizado exitosamente!');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarPrecioParalelo'));
                modal.hide();
                
                // Refrescar widget de precios
                if (typeof refreshWidgetData === 'function') {
                    refreshWidgetData();
                }
            } else {
                alert('Error al actualizar el precio: ' + (data.message || 'Error desconocido'));
                document.getElementById('btn-confirmar-actualizacion-precio').disabled = false;
                document.getElementById('btn-confirmar-actualizacion-precio').innerHTML = '<i class="bi bi-check-circle"></i> Actualizar Precio';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el precio paralelo');
            document.getElementById('btn-confirmar-actualizacion-precio').disabled = false;
            document.getElementById('btn-confirmar-actualizacion-precio').innerHTML = '<i class="bi bi-check-circle"></i> Actualizar Precio';
        });
    });
    
    // Reset del modal cuando se cierra
    document.getElementById('modalActualizarPrecioParalelo').addEventListener('hidden.bs.modal', function() {
        document.getElementById('form-actualizar-precio-paralelo').reset();
        document.getElementById('confirmar-actualizacion-precio').checked = false;
        document.getElementById('btn-confirmar-actualizacion-precio').disabled = true;
    });
}

// Manejo del modal de cierre de inventario
if (document.getElementById('modalCierreInventario')) {
    // Evento cuando se abre el modal
    document.getElementById('modalCierreInventario').addEventListener('show.bs.modal', function() {
        // Mostrar loading y ocultar formulario
        document.getElementById('loading-cierre').style.display = 'block';
        document.getElementById('form-cierre-inventario').style.display = 'none';
        document.getElementById('btn-confirmar-cierre').disabled = true;
        
        // Hacer petición AJAX para obtener los datos
        fetch('{$urlGetDataCierre}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Función helper para convertir datetime de PHP a formato datetime-local
                // De 'YYYY-MM-DD HH:MM:SS' a 'YYYY-MM-DDTHH:MM'
                function formatDatetimeLocal(datetimeStr) {
                    if (!datetimeStr) return '';
                    // Remover los segundos y reemplazar espacio por T
                    return datetimeStr.substring(0, 16).replace(' ', 'T');
                }
                
                // Llenar el formulario con los datos
                document.getElementById('cierre-fecha-inicio').value = formatDatetimeLocal(data.data.fecha_inicio);
                document.getElementById('cierre-fecha-cierre').value = formatDatetimeLocal(data.data.fecha_cierre);
                document.getElementById('cierre-cantidad').value = data.data.cantidad_productos;
                document.getElementById('cierre-valor').value = data.data.valor;
                
                // Ocultar loading y mostrar formulario
                document.getElementById('loading-cierre').style.display = 'none';
                document.getElementById('form-cierre-inventario').style.display = 'block';
            } else {
                alert('Error: ' + (data.message || 'No se pudieron cargar los datos'));
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalCierreInventario'));
                modal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del cierre de inventario');
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCierreInventario'));
            modal.hide();
        });
    });
    
    // Manejar checkbox de confirmación
    document.getElementById('cierre-confirmar').addEventListener('change', function() {
        document.getElementById('btn-confirmar-cierre').disabled = !this.checked;
    });
    
    // Manejar clic en botón de confirmar cierre
    document.getElementById('btn-confirmar-cierre').addEventListener('click', function() {
        const confirmacion = confirm('¿Está seguro de que desea cerrar el inventario? Esta acción no se puede deshacer.');
        
        if (!confirmacion) {
            return;
        }
        
        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando...';
        
        // Función helper para convertir datetime-local a formato MySQL
        // De 'YYYY-MM-DDTHH:MM' a 'YYYY-MM-DD HH:MM:SS'
        function formatDatetimeMySQL(datetimeLocalStr) {
            if (!datetimeLocalStr) return '';
            // Reemplazar T por espacio y agregar :00 para los segundos
            return datetimeLocalStr.replace('T', ' ') + ':00';
        }
        
        // Preparar datos del formulario
        const formData = new FormData();
        formData.append('fecha_inicio', formatDatetimeMySQL(document.getElementById('cierre-fecha-inicio').value));
        formData.append('fecha_cierre', formatDatetimeMySQL(document.getElementById('cierre-fecha-cierre').value));
        formData.append('cantidad_productos', document.getElementById('cierre-cantidad').value);
        formData.append('valor', document.getElementById('cierre-valor').value);
        formData.append('nota', document.getElementById('cierre-nota').value);
        formData.append('confirmar_cierre', document.getElementById('cierre-confirmar').checked ? '1' : '0');
        formData.append('{$csrfParam}', '{$csrfToken}');
        
        // Enviar datos
        fetch('{$urlRegistrarCierre}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Cierre de inventario registrado exitosamente!');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalCierreInventario'));
                modal.hide();
                
                // Redirigir a la vista del inventario cerrado o recargar la página
                window.location.href = '{$urlIndexInventarios}';
            } else {
                alert('Error al registrar el cierre: ' + (data.message || 'Error desconocido'));
                document.getElementById('btn-confirmar-cierre').disabled = false;
                document.getElementById('btn-confirmar-cierre').innerHTML = '<i class="bi bi-check-circle"></i> Registrar Cierre';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al registrar el cierre de inventario');
            document.getElementById('btn-confirmar-cierre').disabled = false;
            document.getElementById('btn-confirmar-cierre').innerHTML = '<i class="bi bi-check-circle"></i> Registrar Cierre';
        });
    });
    
    // Reset del modal cuando se cierra
    document.getElementById('modalCierreInventario').addEventListener('hidden.bs.modal', function() {
        document.getElementById('form-cierre-inventario').reset();
        document.getElementById('cierre-confirmar').checked = false;
        document.getElementById('btn-confirmar-cierre').disabled = true;
        document.getElementById('loading-cierre').style.display = 'block';
        document.getElementById('form-cierre-inventario').style.display = 'none';
    });
}

// ============================================================
// MODAL DE GENERAR REPORTE
// ============================================================
if (document.getElementById('modalGenerarReporte')) {
    // Mostrar/ocultar selector de lugar según tipo de reporte
    document.getElementById('tipo-reporte').addEventListener('change', function() {
        const selectorLugar = document.getElementById('selector-lugar');
        const idLugar = document.getElementById('id-lugar');
        
        if (this.value === 'por-lugar') {
            selectorLugar.style.display = 'block';
            idLugar.required = true;
        } else {
            selectorLugar.style.display = 'none';
            idLugar.required = false;
            idLugar.value = '';
        }
    });
    
    // Manejar clic en botón de generar reporte
    document.getElementById('btn-generar-reporte').addEventListener('click', function() {
        const tipoReporte = document.getElementById('tipo-reporte').value;
        const idLugar = document.getElementById('id-lugar').value;
        
        if (tipoReporte === 'por-lugar' && !idLugar) {
            alert('Por favor seleccione un almacén');
            return;
        }
        
        // Enviar el formulario
        document.getElementById('form-generar-reporte').submit();
        
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerarReporte'));
        modal.hide();
    });
    
    // Reset del modal cuando se cierra
    document.getElementById('modalGenerarReporte').addEventListener('hidden.bs.modal', function() {
        document.getElementById('form-generar-reporte').reset();
        document.getElementById('selector-lugar').style.display = 'none';
        document.getElementById('id-lugar').required = false;
    });
}

// ============================================================
// MODAL DE RECORDATORIO DE COBROS PENDIENTES
// ============================================================
if (document.getElementById('modalRecordatorioCobros')) {
    
    // Variables globales para control del modal
    let modalRecordatorioAbierto = false;
    
    // Función para verificar si el usuario puede ver el modal
    function puedeVerModal() {
        const userIsGuest = {$userIsGuestJS};
        
        // Verificar que el usuario no sea invitado
        if (userIsGuest) {
            return false;
        }
        
        // Verificar si el modal ya fue mostrado en esta sesión del navegador
        // sessionStorage se mantiene mientras la pestaña está abierta
        // y se limpia cuando se cierra la pestaña/ventana
        const yaSeVio = sessionStorage.getItem('modalCobrosMostrado');
        
        if (yaSeVio === 'true') {
            return false;
        }
        
        return true;
    }
    
    // Función para cargar clientes con facturas pendientes
    function cargarClientesPendientes() {
        fetch('{$urlGetClientesPendientes}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Renderizar los clientes en el accordion
                renderClientesPendientes(data.data);
                
                // Ocultar loading y mostrar contenido
                document.getElementById('loading-cobros-pendientes').style.display = 'none';
                document.getElementById('contenido-cobros-pendientes').style.display = 'block';
                
                // Marcar en sessionStorage que el modal ya fue mostrado en esta sesión
                // Esto previene que aparezca en cada navegación interna
                sessionStorage.setItem('modalCobrosMostrado', 'true');
                
                // Abrir el modal
                const modal = new bootstrap.Modal(document.getElementById('modalRecordatorioCobros'));
                modal.show();
                modalRecordatorioAbierto = true;
            } else {
                // No hay clientes pendientes, no mostrar el modal
                console.log('No hay clientes con facturas pendientes');
            }
        })
        .catch(error => {
            console.error('Error al cargar clientes pendientes:', error);
        });
    }
    
    // Función para renderizar clientes en el accordion
    function renderClientesPendientes(clientes) {
        const accordion = document.getElementById('accordionClientesPendientes');
        accordion.innerHTML = '';
        
        
        clientes.forEach((cliente, index) => {
            const clienteId = 'cliente-' + cliente.id;
            const statusBadgeClass = cliente.status === 'Moroso' ? 'bg-danger' : 'bg-warning text-dark';
            
            // Calcular sumas por moneda
            const sumsByCurrency = {
                'USDT': 0,
                'BCV': 0,
                'VES': 0
            };
            
            cliente.facturas.forEach(f => {
                const currency = f.currency || 'USDT';
                sumsByCurrency[currency] += parseFloat(f.saldo_pendiente);
            });
            
            // Detectar qué monedas están presentes
            const currencies = Object.keys(sumsByCurrency).filter(curr => sumsByCurrency[curr] > 0);
            
            // Generar el display de moneda
            let currencyDisplay = '';
            if (currencies.length === 1) {
                // Una sola moneda
                currencyDisplay = currencies[0];
            } else {
                // Múltiples monedas: mostrar desglose
                const parts = [];
                if (sumsByCurrency['USDT'] > 0) {
                    parts.push(`\${sumsByCurrency['USDT'].toFixed(2)} (USDT)`);
                }
                if (sumsByCurrency['BCV'] > 0) {
                    parts.push(`\${sumsByCurrency['BCV'].toFixed(2)} (BCV)`);
                }
                if (sumsByCurrency['VES'] > 0) {
                    parts.push(`\${sumsByCurrency['VES'].toFixed(2)} (VES)`);
                }
                currencyDisplay = parts.join(' | ');
            }
            
            // Calcular total general (suma de todas las monedas)
            const totalPendiente = currencies.reduce((sum, curr) => sum + sumsByCurrency[curr], 0);
            
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item cliente-accordion-item';
            accordionItem.innerHTML = `
                <h2 class="accordion-header cliente-accordion-header" id="heading-\${clienteId}">
                    <button class="accordion-button cliente-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-\${clienteId}" aria-expanded="false" aria-controls="collapse-\${clienteId}">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-fill"></i>
                                <span>\${cliente.nombre}</span>
                                <span class="badge \${statusBadgeClass} cliente-info-badge">\${cliente.status}</span>
                            </div>
                            <div class="text-end me-3">
                                <small class="text-muted d-block">Total Pendiente:</small>
                                \${currencies.length === 1 ? `<strong class="text-danger">$\${totalPendiente.toFixed(2)}</strong><br><small class="text-muted" style="font-size: 0.7rem;">\${currencyDisplay}</small>` : `<small class="text-muted" style="font-size: 0.7rem;">\${currencyDisplay}</small>`}
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse-\${clienteId}" class="accordion-collapse collapse" aria-labelledby="heading-\${clienteId}" data-bs-parent="#accordionClientesPendientes">
                    <div class="accordion-body" style="padding: 25px;">
                        \${renderFacturasCliente(cliente.facturas)}
                    </div>
                </div>
            `;
            
            accordion.appendChild(accordionItem);
        });
    }
    
    // Función para renderizar las facturas de un cliente
    function renderFacturasCliente(facturas) {
        let html = '';
        
        facturas.forEach(factura => {
            const facturaUrl = '{$urlFacturaView}'.replace('__ID__', factura.id);
            const currency = factura.currency || 'USDT'; // Default to USDT if not specified
            
            html += `
                <div class="factura-item" onclick="window.location.href='\${facturaUrl}'">
                    <div class="factura-header">
                        <div class="factura-codigo">
                            <i class="bi bi-receipt"></i> \${factura.codigo}
                        </div>
                        <span class="factura-status-badge">
                            <i class="bi bi-exclamation-circle"></i> Pendiente
                        </span>
                    </div>
                    
                    \${factura.concepto ? `<p class="text-muted mb-3"><i class="bi bi-file-text me-2"></i>\${factura.concepto}</p>` : ''}
                    
                    <div class="factura-details">
                        <div class="factura-detail-item">
                            <div class="factura-detail-label">Monto Total</div>
                            <div class="factura-detail-value total">
                                $\${parseFloat(factura.monto_final).toFixed(2)}
                                <br><small class="text-muted" style="font-size: 0.75rem;">\${currency}</small>
                            </div>
                        </div>
                        <div class="factura-detail-item">
                            <div class="factura-detail-label">Total Pagado</div>
                            <div class="factura-detail-value pagado">
                                $\${parseFloat(factura.total_pagado).toFixed(2)}
                                <br><small class="text-muted" style="font-size: 0.75rem;">\${currency}</small>
                            </div>
                        </div>
                        <div class="factura-detail-item">
                            <div class="factura-detail-label">Saldo Pendiente</div>
                            <div class="factura-detail-value pendiente">
                                $\${parseFloat(factura.saldo_pendiente).toFixed(2)}
                                <br><small class="text-muted" style="font-size: 0.75rem;">\${currency}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Fecha: \${factura.fecha}
                        </small>
                    </div>
                </div>
            `;
        });
        
        return html;
    }
    
    // ============================================================
    // BELL NOTIFICATION HANDLER
    // ============================================================
    
    // Global variable to store pending count
    let pendingInvoicesCount = 0;
    
    // Function to update bell notification badge (both desktop and mobile)
    function updateBellNotification(count) {
        // Desktop bell elements
        const bellIcon = document.getElementById('bell-icon');
        const bellDot = document.getElementById('bell-notification-dot');
        const bellCount = document.getElementById('bell-notification-count');
        
        // Mobile bell elements
        const bellIconMobile = document.getElementById('bell-icon-mobile');
        const bellDotMobile = document.getElementById('bell-notification-dot-mobile');
        const bellCountMobile = document.getElementById('bell-notification-count-mobile');
        
        pendingInvoicesCount = count;
        
        // Update desktop bell
        if (bellIcon && bellDot && bellCount) {
            if (count > 0) {
                bellDot.style.display = 'block';
                bellCount.textContent = count;
                bellCount.style.display = 'block';
                bellIcon.style.color = '#dc3545';
            } else {
                bellDot.style.display = 'none';
                bellCount.style.display = 'none';
                bellIcon.style.color = '#6c757d';
            }
        }
        
        // Update mobile bell
        if (bellIconMobile && bellDotMobile && bellCountMobile) {
            if (count > 0) {
                bellDotMobile.style.display = 'block';
                bellCountMobile.textContent = count;
                bellCountMobile.style.display = 'block';
                bellIconMobile.style.color = '#dc3545';
            } else {
                bellDotMobile.style.display = 'none';
                bellCountMobile.style.display = 'none';
                bellIconMobile.style.color = '#6c757d';
            }
        }
    }
    
    // Function to check for pending invoices (silent check, no modal)
    function checkPendingInvoices() {
        const userIsGuest = {$userIsGuestJS};
        
        if (userIsGuest) {
            return;
        }
        
        fetch('{$urlGetClientesPendientes}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Count total clients with pending invoices (not total invoices)
                let totalPendingClients = data.data.length;
                
                // Update bell notification with client count
                updateBellNotification(totalPendingClients);
            } else {
                // No pending invoices
                updateBellNotification(0);
            }
        })
        .catch(error => {
            console.error('Error checking pending invoices:', error);
        });
    }
    
    // Handle bell icon click (Desktop)
    const bellContainer = document.getElementById('bell-notification-container');
    if (bellContainer) {
        bellContainer.addEventListener('click', function() {
            // Add hover effect
            const bellIcon = document.getElementById('bell-icon');
            if (bellIcon) {
                bellIcon.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    bellIcon.style.transform = 'scale(1)';
                }, 200);
            }
            
            // Open the modal directly
            if (puedeVerModal()) {
                cargarClientesPendientes();
            } else {
                // If modal was already shown, allow re-opening via bell click
                // Reset the sessionStorage flag temporarily
                sessionStorage.removeItem('modalCobrosMostrado');
                cargarClientesPendientes();
            }
        });
        
        // Add hover effect
        bellContainer.addEventListener('mouseenter', function() {
            const bellIcon = document.getElementById('bell-icon');
            if (bellIcon) {
                bellIcon.style.transform = 'rotate(15deg)';
            }
        });
        
        bellContainer.addEventListener('mouseleave', function() {
            const bellIcon = document.getElementById('bell-icon');
            if (bellIcon) {
                bellIcon.style.transform = 'rotate(0deg)';
            }
        });
    }
    
    // Handle bell icon click (Mobile)
    const bellContainerMobile = document.getElementById('bell-notification-container-mobile');
    if (bellContainerMobile) {
        bellContainerMobile.addEventListener('click', function() {
            // Add hover effect
            const bellIconMobile = document.getElementById('bell-icon-mobile');
            if (bellIconMobile) {
                bellIconMobile.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    bellIconMobile.style.transform = 'scale(1)';
                }, 200);
            }
            
            // Open the modal directly
            if (puedeVerModal()) {
                cargarClientesPendientes();
            } else {
                // If modal was already shown, allow re-opening via bell click
                // Reset the sessionStorage flag temporarily
                sessionStorage.removeItem('modalCobrosMostrado');
                cargarClientesPendientes();
            }
        });
    }
    
    // Check for pending invoices on page load (silent check)
    window.addEventListener('load', function() {
        setTimeout(function() {
            checkPendingInvoices();
        }, 500);
    });
    
    // ============================================================
    
    // Evento cuando se cierra el modal
    document.getElementById('modalRecordatorioCobros').addEventListener('hidden.bs.modal', function() {
        // Simplemente resetear la bandera cuando se cierra
        // Ya no registramos el cierre en el servidor porque queremos que aparezca cada vez
        modalRecordatorioAbierto = false;
    });
    
    // Verificar y abrir el modal cuando se carga la página
    {$loadEventListener}
}

JS
, \yii\web\View::POS_END);


//MODAL DE GENERACION DE REPORTE
$script = <<< JS
// =====================
// Modal Generar Reporte
// =====================
(function () {
    const tipoReporteSelect = document.getElementById("tipo-reporte");
    const selectorLugar = document.getElementById("selector-lugar");
    const idLugarSelect = document.getElementById("id-lugar");
    const btnGenerarReporte = document.getElementById("btn-generar-reporte");
    const formGenerarReporte = document.getElementById("form-generar-reporte");

    if (!tipoReporteSelect || !selectorLugar || !btnGenerarReporte || !formGenerarReporte) {
        console.log("Elementos del modal de generar reporte no encontrados");
        return;
    }

    // Mostrar/ocultar selector de lugar según el tipo de reporte
    tipoReporteSelect.addEventListener("change", function () {
        if (this.value === "por-lugar") {
            selectorLugar.style.display = "block";
            if (idLugarSelect) {
                idLugarSelect.required = true;
            }
        } else {
            selectorLugar.style.display = "none";
            if (idLugarSelect) {
                idLugarSelect.required = false;
                idLugarSelect.value = "";
            }
        }
    });

    // Generar reporte al hacer clic en el botón
    btnGenerarReporte.addEventListener("click", function () {
        // Validar que si es por lugar, se haya seleccionado uno
        if (tipoReporteSelect.value === "por-lugar") {
            if (!idLugarSelect || !idLugarSelect.value) {
                alert("Por favor, seleccione un almacén para generar el reporte.");
                return;
            }
        }

        // Enviar el formulario (se abrirá en una nueva pestaña por el target="_blank")
        formGenerarReporte.submit();

        // Cerrar el modal
        const modal = document.getElementById("modalGenerarReporte");
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    });

    // Resetear el formulario al abrir el modal
    const modalGenerarReporte = document.getElementById("modalGenerarReporte");
    if (modalGenerarReporte) {
        modalGenerarReporte.addEventListener("show.bs.modal", function () {
            tipoReporteSelect.value = "general";
            selectorLugar.style.display = "none";
            if (idLugarSelect) {
                idLugarSelect.value = "";
                idLugarSelect.required = false;
            }
        });
    }
})();
JS;
$this->registerJs($script);
//----------------------------------------------------------------------------

// Codigo para el lector de codigo de barras SEPARADO DEL LAYOUT
$this->registerJs(<<<JS
    console.log("Cargando QuaggaJS...");

    (function () {
    const modalLectorCodigoBarras = document.getElementById('modalLectorCodigoBarras');
    const barcodeResultContainer = document.getElementById('barcode-result-container');
    const barcodeResultInput = document.getElementById('barcode-result-input');
    const btnUsarCodigo = document.getElementById('btn-usar-codigo');
    const scannerStatus = document.getElementById('scanner-status');
    const codigoBarraHidden = document.getElementById('productos-codigo_barra');
    const codigoBarraDisplay = document.getElementById('codigo_barra_display');
    
    let isScanning = false;
    let detectedCode = null;

    if (!modalLectorCodigoBarras) {
        return;
    }

    // Inicializar QuaggaJS cuando se abre el modal
    modalLectorCodigoBarras.addEventListener('shown.bs.modal', function () {
        resetScanner();
        
        setTimeout(function() {
            const interactiveElement = document.querySelector('#interactive');
            
            if (!interactiveElement) {
                if (scannerStatus) {
                    scannerStatus.innerHTML = '<i class="bi bi-exclamation-triangle text-danger"></i> Error: Elemento del escáner no encontrado.';
                }
                return;
            }
            
            initQuagga();
        }, 500);
    });

    modalLectorCodigoBarras.addEventListener('hide.bs.modal', function () {
        stopQuagga();
    });

    if (btnUsarCodigo) {
        btnUsarCodigo.addEventListener('click', function () {
            if (detectedCode) {
                if (codigoBarraHidden) {
                    codigoBarraHidden.value = detectedCode;
                }
                if (codigoBarraDisplay) {
                    codigoBarraDisplay.value = detectedCode;
                }
                
                const modal = bootstrap.Modal.getInstance(modalLectorCodigoBarras);
                if (modal) {
                    modal.hide();
                }
            }
        });
    }

    function resetScanner() {
        detectedCode = null;
        isScanning = false;
        if (barcodeResultContainer) barcodeResultContainer.style.display = 'none';
        if (barcodeResultInput) barcodeResultInput.value = '';
        if (btnUsarCodigo) btnUsarCodigo.style.display = 'none';
        if (scannerStatus) {
            scannerStatus.innerHTML = '<i class="bi bi-camera-video"></i> Iniciando cámara...';
            scannerStatus.style.display = 'block';
        }
    }

    function initQuagga() {
        if (isScanning) {
            return;
        }

        if (typeof Quagga === 'undefined') {
            if (scannerStatus) {
                scannerStatus.innerHTML = '<i class="bi bi-exclamation-triangle text-danger"></i> Error: QuaggaJS no cargó correctamente.';
            }
            return;
        }

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                }
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 2,
            decoder: {
                readers: ["ean_reader", "ean_8_reader", "code_128_reader", "code_39_reader", "upc_reader", "upc_e_reader"],
                debug: {
                    drawBoundingBox: true,
                    showFrequency: true,
                    drawScanline: true,
                    showPattern: true
                }
            },
            locate: true,
            frequency: 10
        }, function(err) {
            if (err) {
                let errorMsg = err.message || 'Error desconocido';
                
                if (errorMsg.includes('getUserMedia') || errorMsg.includes('media')) {
                    errorMsg = 'No se puede acceder a la cámara. En dispositivos móviles, necesita usar HTTPS.';
                } else if (errorMsg.includes('Permission')) {
                    errorMsg = 'Permiso de cámara denegado. Por favor, permite el acceso a la cámara.';
                } else if (errorMsg.includes('target')) {
                    errorMsg = 'Error: No se encontró el elemento del escáner.';
                }
                
                if (scannerStatus) {
                    scannerStatus.innerHTML = '<i class="bi bi-exclamation-triangle text-danger"></i> ' + errorMsg;
                }
                return;
            }
            
            isScanning = true;
            
            try {
                Quagga.start();
                
                if (scannerStatus) {
                    scannerStatus.innerHTML = '<i class="bi bi-camera-video text-success"></i> Cámara activa. Enfoque el código de barras...';
                }
            } catch (e) {
                if (scannerStatus) {
                    scannerStatus.innerHTML = '<i class="bi bi-exclamation-triangle text-danger"></i> Error al iniciar la cámara: ' + e.message;
                }
            }
        });

        Quagga.onDetected(function(result) {
            if (result && result.codeResult && result.codeResult.code) {
                const code = result.codeResult.code;
                
                if (code.length >= 4) {
                    detectedCode = code;
                    if (barcodeResultInput) barcodeResultInput.value = code;
                    if (barcodeResultContainer) barcodeResultContainer.style.display = 'block';
                    if (btnUsarCodigo) btnUsarCodigo.style.display = 'inline-block';
                    if (scannerStatus) scannerStatus.style.display = 'none';
                    stopQuagga();
                }
            }
        });

        Quagga.onProcessed(function(result) {
            const drawingCtx = Quagga.canvas.ctx.overlay;
            const drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }
                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }
                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });
    }

    function stopQuagga() {
        if (isScanning) {
            Quagga.stop();
            isScanning = false;
        }
    }
})();
JS, \yii\web\View::POS_END);


// Mobile Dollar Price Display - JavaScript
$urlDollarPrices = \yii\helpers\Url::to(['site/dollar-prices']);

$this->registerJs(<<<JS
// ============================================================
// MOBILE DOLLAR PRICE DISPLAY - Alternating Text
// ============================================================
(function() {
    'use strict';
    
    const mobilePriceElement = document.getElementById('mobile-price-text');
    
    if (!mobilePriceElement) {
        return; // Element doesn't exist, exit
    }
    
    // State
    let prices = [];
    let currentIndex = 0;
    let rotationTimer = null;
    let updateTimer = null;
    let isUpdating = false;
    
    // Fetch prices from server
    function fetchPrices() {
        if (isUpdating) return;
        
        isUpdating = true;
        
        fetch('$urlDollarPrices', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                prices = data.data.map(p => ({
                    text: p.class === 'oficial' ? 'BCV' : 'PARALELO',
                    value: p.precio,
                    class: p.class === 'oficial' ? 'bcv' : 'paralelo'
                }));
                
                // Update display if we have prices
                if (prices.length > 0) {
                    updateDisplay(false);
                    
                    // Start rotation if we have more than one price
                    if (prices.length > 1 && !rotationTimer) {
                        rotationTimer = setInterval(rotatePrices, 4000);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching dollar prices:', error);
        })
        .finally(() => {
            isUpdating = false;
        });
    }
    
    // Update the display
    function updateDisplay(withFade = true) {
        if (prices.length === 0) return;
        
        const price = prices[currentIndex];
        
        const updateContent = () => {
            mobilePriceElement.textContent = price.text + ': ' + price.value;
            mobilePriceElement.className = 'mobile-price-text ' + price.class;
        };
        
        if (withFade) {
            // Fade out
            mobilePriceElement.classList.add('fade-out');
            
            setTimeout(() => {
                // Update content
                updateContent();
                
                // Fade in
                mobilePriceElement.classList.remove('fade-out');
            }, 500);
        } else {
            // No animation
            updateContent();
        }
    }
    
    // Rotate to next price
    function rotatePrices() {
        if (prices.length <= 1) return;
        
        currentIndex = (currentIndex + 1) % prices.length;
        updateDisplay(true);
    }
    
    // Cleanup function
    function cleanup() {
        if (rotationTimer) {
            clearInterval(rotationTimer);
            rotationTimer = null;
        }
        if (updateTimer) {
            clearInterval(updateTimer);
            updateTimer = null;
        }
    }
    
    // Initialize
    fetchPrices();
    
    // Update prices from server every 15 seconds
    updateTimer = setInterval(fetchPrices, 15000);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', cleanup);
    
})();
JS
, \yii\web\View::POS_END);

