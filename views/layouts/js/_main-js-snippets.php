<?php
$userIsGuest = Yii::$app->user->isGuest;
$userIdentity = Yii::$app->user->identity;
$modalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->modalClosed ?? '0') : '0';
$dateModalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->dateModalClosed ?? '') : '';

$this->registerJs("
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
    const activeContent = document.querySelector('.step-content-modal[data-step=\"' + currentModalStep + '\"]');
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
            const solventeCard = document.querySelector('.status-card-modal[data-status=\"Solvente\"]');
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
        const solventeCard = document.querySelector('.status-card-modal[data-status=\"Solvente\"]');
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
        this.innerHTML = '<span class=\"spinner-border spinner-border-sm me-2\"></span>Actualizando...';
        
        // Preparar datos del formulario
        const formData = new FormData();
        formData.append('precio_paralelo', precio);
        formData.append('observaciones', observaciones);
        formData.append('" . Yii::$app->request->csrfParam . "', '" . Yii::$app->request->csrfToken . "');
        
        // Enviar datos
        fetch('" . \yii\helpers\Url::to(['site/update-parallel-dollar-rate']) . "', {
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
                document.getElementById('btn-confirmar-actualizacion-precio').innerHTML = '<i class=\"bi bi-check-circle\"></i> Actualizar Precio';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el precio paralelo');
            document.getElementById('btn-confirmar-actualizacion-precio').disabled = false;
            document.getElementById('btn-confirmar-actualizacion-precio').innerHTML = '<i class=\"bi bi-check-circle\"></i> Actualizar Precio';
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
        fetch('" . \yii\helpers\Url::to(['historico-inventarios/get-data-cierre']) . "', {
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
        this.innerHTML = '<span class=\"spinner-border spinner-border-sm me-2\"></span>Registrando...';
        
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
        formData.append('" . Yii::$app->request->csrfParam . "', '" . Yii::$app->request->csrfToken . "');
        
        // Enviar datos
        fetch('" . \yii\helpers\Url::to(['historico-inventarios/registrar-cierre']) . "', {
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
                window.location.href = '" . \yii\helpers\Url::to(['historico-inventarios/index']) . "';
            } else {
                alert('Error al registrar el cierre: ' + (data.message || 'Error desconocido'));
                document.getElementById('btn-confirmar-cierre').disabled = false;
                document.getElementById('btn-confirmar-cierre').innerHTML = '<i class=\"bi bi-check-circle\"></i> Registrar Cierre';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al registrar el cierre de inventario');
            document.getElementById('btn-confirmar-cierre').disabled = false;
            document.getElementById('btn-confirmar-cierre').innerHTML = '<i class=\"bi bi-check-circle\"></i> Registrar Cierre';
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
    
    // Función para verificar si estamos en una fecha válida para mostrar el modal
    function esFechaValidaParaModal() {
        const hoy = new Date();
        const dia = hoy.getDate();
        
        // Días 1, 2, 3 o día 15 de cualquier mes
        return (dia >= 1 && dia <= 3) || dia === 15;
    }
    
    // Función para obtener la fecha actual en formato Y-m-d
    function getFechaActual() {
        const hoy = new Date();
        const year = hoy.getFullYear();
        const month = String(hoy.getMonth() + 1).padStart(2, '0');
        const day = String(hoy.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    
    // Función para verificar si el usuario puede ver el modal
    function puedeVerModal() {
        const userIsGuest = " . ($userIsGuest ? 'true' : 'false') . ";
        const modalClosed = '" . $modalClosed . "';
        const dateModalClosed = '" . $dateModalClosed . "';
        
        if (userIsGuest) {
            return false;
        }
        
        const fechaActual = getFechaActual();
        
        // Si el modal fue cerrado hoy, no mostrarlo
        if (modalClosed === '1' && dateModalClosed) {
            const fechaCierre = dateModalClosed.split(' ')[0]; // Obtener solo la fecha
            if (fechaCierre === fechaActual) {
                return false;
            }
        }
        
        // Si estamos en una fecha válida, resetear el estado si es necesario
        if (esFechaValidaParaModal() && modalClosed === '1') {
            // Resetear el estado del modal
            fetch('" . \yii\helpers\Url::to(['site/reset-modal-cobros']) . "', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '" . Yii::$app->request->csrfParam . "=" . Yii::$app->request->csrfToken . "'
            });
            return true;
        }
        
        return esFechaValidaParaModal();
    }
    
    // Función para cargar clientes con facturas pendientes
    function cargarClientesPendientes() {
        fetch('" . \yii\helpers\Url::to(['site/get-clientes-pendientes']) . "', {
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
            const totalPendiente = cliente.facturas.reduce((sum, f) => sum + parseFloat(f.saldo_pendiente), 0);
            const statusBadgeClass = cliente.status === 'Moroso' ? 'bg-danger' : 'bg-warning text-dark';
            
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item cliente-accordion-item';
            accordionItem.innerHTML = `
                <h2 class=\"accordion-header cliente-accordion-header\" id=\"heading-\${clienteId}\">
                    <button class=\"accordion-button cliente-accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse-\${clienteId}\" aria-expanded=\"false\" aria-controls=\"collapse-\${clienteId}\">
                        <div class=\"d-flex align-items-center justify-content-between w-100\">
                            <div class=\"d-flex align-items-center gap-2\">
                                <i class=\"bi bi-person-fill\"></i>
                                <span>\${cliente.nombre}</span>
                                <span class=\"badge \${statusBadgeClass} cliente-info-badge\">\${cliente.status}</span>
                            </div>
                            <div class=\"text-end me-3\">
                                <small class=\"text-muted d-block\">Total Pendiente:</small>
                                <strong class=\"text-danger\">$\${totalPendiente.toFixed(2)}</strong>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id=\"collapse-\${clienteId}\" class=\"accordion-collapse collapse\" aria-labelledby=\"heading-\${clienteId}\" data-bs-parent=\"#accordionClientesPendientes\">
                    <div class=\"accordion-body\" style=\"padding: 25px;\">
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
            const facturaUrl = '" . \yii\helpers\Url::to(['facturas/view', 'id' => '__ID__']) . "'.replace('__ID__', factura.id);
            
            html += `
                <div class=\"factura-item\" onclick=\"window.location.href='\${facturaUrl}'\">
                    <div class=\"factura-header\">
                        <div class=\"factura-codigo\">
                            <i class=\"bi bi-receipt\"></i> \${factura.codigo}
                        </div>
                        <span class=\"factura-status-badge\">
                            <i class=\"bi bi-exclamation-circle\"></i> Pendiente
                        </span>
                    </div>
                    
                    \${factura.concepto ? `<p class=\"text-muted mb-3\"><i class=\"bi bi-file-text me-2\"></i>\${factura.concepto}</p>` : ''}
                    
                    <div class=\"factura-details\">
                        <div class=\"factura-detail-item\">
                            <div class=\"factura-detail-label\">Monto Total</div>
                            <div class=\"factura-detail-value total\">$\${parseFloat(factura.monto_final).toFixed(2)}</div>
                        </div>
                        <div class=\"factura-detail-item\">
                            <div class=\"factura-detail-label\">Total Pagado</div>
                            <div class=\"factura-detail-value pagado\">$\${parseFloat(factura.total_pagado).toFixed(2)}</div>
                        </div>
                        <div class=\"factura-detail-item\">
                            <div class=\"factura-detail-label\">Saldo Pendiente</div>
                            <div class=\"factura-detail-value pendiente\">$\${parseFloat(factura.saldo_pendiente).toFixed(2)}</div>
                        </div>
                    </div>
                    
                    <div class=\"text-end mt-3\">
                        <small class=\"text-muted\">
                            <i class=\"bi bi-calendar\"></i> Fecha: \${factura.fecha}
                        </small>
                    </div>
                </div>
            `;
        });
        
        return html;
    }
    
    // Evento cuando se cierra el modal
    document.getElementById('modalRecordatorioCobros').addEventListener('hidden.bs.modal', function() {
        if (modalRecordatorioAbierto) {
            // Marcar el modal como cerrado para este usuario
            fetch('" . \yii\helpers\Url::to(['site/cerrar-modal-cobros']) . "', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '" . Yii::$app->request->csrfParam . "=" . Yii::$app->request->csrfToken . "'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Modal cerrado y registrado');
                }
            })
            .catch(error => {
                console.error('Error al cerrar modal:', error);
            });
            
            modalRecordatorioAbierto = false;
        }
    });
    
    // Verificar y abrir el modal cuando se carga la página
    " . (!$userIsGuest && $userIdentity ? "
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (puedeVerModal()) {
                cargarClientesPendientes();
            }
        }, 1000); // Esperar 1 segundo después de cargar la página
    });
    " : "") . "
}
", \yii\web\View::POS_END);


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


