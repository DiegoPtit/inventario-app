// JavaScript para Modal Nuevo Cliente
(function () {
    let currentStepModal = 1;
    const totalStepsModal = 3;

    const modal = document.getElementById("modalNuevoCliente");

    if (!modal) return;

    const btnPrevious = document.getElementById("btn-modal-previous");
    const btnNext = document.getElementById("btn-modal-next");
    const btnSubmit = document.getElementById("btn-modal-submit");

    function updateStepModal() {
        const stepNames = ["Personal", "Contacto", "Estado"];

        // Actualizar contenido
        document.querySelectorAll(".step-content-modal").forEach(content => {
            content.classList.remove("active");
            content.style.display = "none";
        });

        const activeContent = document.querySelector(
            '.step-content-modal[data-step="' + currentStepModal + '"]'
        );
        if (activeContent) {
            activeContent.classList.add("active");
            activeContent.style.display = "block";
        }

        // Actualizar barra de progreso
        const progress = ((currentStepModal - 1) / (totalStepsModal - 1)) * 100;
        const progressBar = document.getElementById("progress-bar-fill-modal");
        const progressText = document.getElementById("progress-step-text-modal");
        const progressPercentage = document.getElementById("progress-percentage-modal");

        if (progressBar) {
            progressBar.style.width = progress + "%";
        }

        if (progressText) {
            progressText.textContent =
                "Paso " + currentStepModal + " de " + totalStepsModal + ": " + stepNames[currentStepModal - 1];
        }

        if (progressPercentage) {
            progressPercentage.textContent = Math.round(progress) + "%";
        }

        // Actualizar botones - with null checks
        if (btnPrevious) {
            if (currentStepModal === 1) {
                btnPrevious.style.display = "none";
            } else {
                btnPrevious.style.display = "inline-block";
            }
        }

        if (btnNext && btnSubmit) {
            if (currentStepModal === totalStepsModal) {
                btnNext.style.display = "none";
                btnSubmit.style.display = "inline-block";
            } else {
                btnNext.style.display = "inline-block";
                btnSubmit.style.display = "none";
            }
        }
    }

    // Eventos de navegación
    btnNext.addEventListener("click", function () {
        if (currentStepModal < totalStepsModal) {
            currentStepModal++;
            updateStepModal();
        }
    });

    btnPrevious.addEventListener("click", function () {
        if (currentStepModal > 1) {
            currentStepModal--;
            updateStepModal();
        }
    });

    // Reset al abrir modal
    modal.addEventListener("show.bs.modal", function () {
        currentStepModal = 1;
        updateStepModal();
        document.getElementById("form-nuevo-cliente-modal").reset();
        document.getElementById("modal-cliente-status").value = "Solvente";

        // Reset status cards
        document.querySelectorAll(".status-card-modal").forEach(card => card.classList.remove("selected"));
        const solventeCard = document.querySelector('.status-card-modal[data-status="Solvente"]');
        if (solventeCard) {
            solventeCard.classList.add("selected");
        }
    });

    // Envío del formulario
    /*
    btnSubmit.addEventListener("click", function () {
        const formData = new FormData(document.getElementById("form-nuevo-cliente-modal"));

        fetch("?r=clientes/create-ajax", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Cliente registrado correctamente");
                    location.reload();
                } else {
                    //No hacemos nada porque despues dañamos el bendito codigo
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error al registrar el cliente");
            });
    });*/

    // Manejo de selección de status
    document.querySelectorAll(".status-card-modal").forEach(card => {
        card.addEventListener("click", function () {
            const status = this.getAttribute("data-status");

            document.querySelectorAll(".status-card-modal").forEach(c => c.classList.remove("selected"));
            this.classList.add("selected");
            document.getElementById("modal-cliente-status").value = status;
        });
    });

    // Inicializar solo si los elementos existen
    if (btnPrevious && btnNext && btnSubmit) {
        updateStepModal();
    }
})();

// Funcionalidad para Modal de Detalles de Cobros
const modalDetallesCobros = document.getElementById("modalDetallesCobros");

// Variable global para almacenar los precios del dólar
let preciosGlobales = null;

// Función para obtener precios del dólar
async function obtenerPreciosDolar() {
    if (preciosGlobales) {
        return preciosGlobales;
    }

    try {
        const response = await fetch("?r=site/dollar-prices");
        const data = await response.json();
        if (data.success && data.data && data.data.length >= 2) {
            // Asumiendo que el primer precio es oficial (BCV) y el segundo es paralelo (USDT)
            preciosGlobales = {
                precioOficial: parseFloat(data.data[0].precio.replace(/\./g, '').replace(',', '.')),
                precioParalelo: parseFloat(data.data[1].precio.replace(/\./g, '').replace(',', '.'))
            };
            return preciosGlobales;
        }
    } catch (error) {
        console.error("Error obteniendo precios:", error);
    }
    return null;
}

// Función para convertir entre monedas
function convertCurrency(amount, fromCurrency, toCurrency) {
    if (!preciosGlobales) return amount;
    if (fromCurrency === toCurrency) return amount;

    const value = parseFloat(amount) || 0;

    // Convertir a VES (moneda base)
    let amountInVES = 0;
    if (fromCurrency === 'USDT') {
        amountInVES = value * preciosGlobales.precioParalelo;
    } else if (fromCurrency === 'BCV') {
        amountInVES = value * preciosGlobales.precioOficial;
    } else if (fromCurrency === 'VES') {
        amountInVES = value;
    }

    // Convertir de VES a moneda destino
    let converted = 0;
    if (toCurrency === 'USDT') {
        converted = amountInVES / preciosGlobales.precioParalelo;
    } else if (toCurrency === 'BCV') {
        converted = amountInVES / preciosGlobales.precioOficial;
    } else if (toCurrency === 'VES') {
        converted = amountInVES;
    }

    return parseFloat(converted.toFixed(2));
}

// Función para formatear montos según moneda
function formatAmount(amount, currency) {
    const value = parseFloat(amount) || 0;
    if (currency === 'VES') {
        return 'Bs. ' + value.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        return '$' + value.toFixed(2);
    }
}

if (modalDetallesCobros) {
    modalDetallesCobros.addEventListener("show.bs.modal", async function (event) {
        const button = event.relatedTarget;

        console.log("Modal abierto, botón:", button);

        if (!button) {
            console.error("No se encontró el botón que activó el modal");
            return;
        }

        // Obtener precios del dólar
        await obtenerPreciosDolar();

        // Obtener datos del botón
        const facturaId = button.getAttribute("data-factura-id");
        const facturaCodigo = button.getAttribute("data-factura-codigo");
        const facturaCurrency = button.getAttribute("data-factura-currency") || 'USDT';
        const clienteNombre = button.getAttribute("data-cliente-nombre");
        const facturaFecha = button.getAttribute("data-factura-fecha");
        const cobrosDataStr = button.getAttribute("data-cobros");

        console.log("Datos obtenidos:", {
            facturaId,
            facturaCodigo,
            facturaCurrency,
            clienteNombre,
            facturaFecha,
            cobrosDataStr
        });

        // Actualizar título del modal
        const tituloElement = document.getElementById("modal-titulo-factura");
        if (tituloElement) {
            tituloElement.textContent = "Detalles de Pagos - " + facturaCodigo;
        }

        // Actualizar resumen
        const clienteElement = document.getElementById("modal-cliente-nombre");
        if (clienteElement) {
            clienteElement.textContent = clienteNombre || "N/A";
        }

        const fechaElement = document.getElementById("modal-factura-fecha");
        if (fechaElement) {
            fechaElement.textContent = facturaFecha || "N/A";
        }

        // Calcular total cobrado EN LA MONEDA DE LA FACTURA
        const totalElement = document.getElementById("modal-total-cobrado");
        if (totalElement) {
            let totalCobradoEnMonedaFactura = 0;

            try {
                const cobrosData = JSON.parse(cobrosDataStr);
                cobrosData.forEach(cobro => {
                    const montoConvertido = convertCurrency(
                        parseFloat(cobro.monto) || 0,
                        cobro.currency || 'USDT',
                        facturaCurrency
                    );
                    totalCobradoEnMonedaFactura += montoConvertido;
                });
                // Asegurar que sea un número válido
                totalCobradoEnMonedaFactura = parseFloat(totalCobradoEnMonedaFactura) || 0;
                totalCobradoEnMonedaFactura = parseFloat(totalCobradoEnMonedaFactura.toFixed(2));
            } catch (error) {
                console.error("Error calculando total:", error);
                totalCobradoEnMonedaFactura = 0;
            }

            const currencyLabel = facturaCurrency === 'VES' ? 'VES' :
                facturaCurrency === 'BCV' ? 'BCV' : 'USDT';
            totalElement.innerHTML = formatAmount(totalCobradoEnMonedaFactura, facturaCurrency) +
                ` <small class="text-muted" style="font-size: 0.65em;">(${currencyLabel})</small>`;
        }

        // Actualizar botón Ver Factura
        const btnVerFactura = document.getElementById("btn-ver-factura");
        if (btnVerFactura) {
            // Usamos la ruta por query string para evitar dependencias de PHP
            btnVerFactura.href = "?r=facturas/view&id=" + encodeURIComponent(facturaId);
        }

        // Generar tarjetas de cobros
        const container = document.getElementById("modal-cobros-container");
        if (!container) {
            console.error("No se encontró el contenedor de cobros");
            return;
        }

        container.innerHTML = "";

        try {
            const cobrosData = JSON.parse(cobrosDataStr);
            console.log("Cobros parseados:", cobrosData);

            if (!cobrosData || cobrosData.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No hay cobros registrados</p>';
                return;
            }

            cobrosData.forEach((cobro, index) => {
                const card = window.createPaymentCard(cobro, index);
                container.appendChild(card);
            });
        } catch (error) {
            console.error("Error al parsear cobros:", error);
            container.innerHTML = '<p class="text-danger text-center">Error al cargar los cobros</p>';
        }
    });
}

// Funciones globales para el modal de cobros
window.createPaymentCard = function (cobro) {
    const card = document.createElement("div");
    card.className = "modal-payment-card";

    const fecha = new Date(cobro.fecha).toLocaleDateString("es-ES");
    const monto = parseFloat(cobro.monto).toFixed(2);
    const currency = cobro.currency || 'USDT';
    const currencyLabel = currency === 'VES' ? 'VES' :
        currency === 'BCV' ? 'BCV' : 'USDT';
    const metodoPago = cobro.metodo_pago || "No especificado";
    const nota = cobro.nota || "";

    // Formatear monto según su currency
    const montoFormateado = formatAmount(parseFloat(monto), currency);

    card.innerHTML = `
        <div class="modal-payment-card-header" onclick="window.togglePaymentCard(this)">
            <div class="modal-payment-card-title">
                <span class="modal-payment-date">
                    <i class="bi bi-calendar3"></i> ${fecha}
                </span>
                <div>
                    <span class="modal-payment-amount">${montoFormateado} <small class="text-muted" style="font-size: 0.7em;">(${currencyLabel})</small></span>
                </div>
                <span class="modal-payment-method">${metodoPago}</span>
            </div>
            <i class="bi bi-chevron-down modal-payment-chevron"></i>
        </div>
        <div class="modal-payment-card-body">
            <div class="modal-payment-detail-row">
                <span class="modal-payment-detail-label"><i class="bi bi-hash"></i> ID:</span>
                <span class="modal-payment-detail-value">${cobro.id}</span>
            </div>
            <div class="modal-payment-detail-row">
                <span class="modal-payment-detail-label"><i class="bi bi-calendar-check"></i> Fecha:</span>
                <span class="modal-payment-detail-value">${fecha}</span>
            </div>
            <div class="modal-payment-detail-row">
                <span class="modal-payment-detail-label"><i class="bi bi-cash"></i> Monto:</span>
                <span class="modal-payment-detail-value text-success">${montoFormateado} <small class="text-muted" style="font-size: 0.8em;">(${currencyLabel})</small></span>
            </div>
            <div class="modal-payment-detail-row">
                <span class="modal-payment-detail-label"><i class="bi bi-credit-card"></i> Método de Pago:</span>
                <span class="modal-payment-detail-value">${metodoPago}</span>
            </div>
            ${nota ? `
                <div class="modal-payment-nota">
                    <div class="modal-payment-nota-label">
                        <i class="bi bi-sticky"></i> Nota:
                    </div>
                    <div class="modal-payment-nota-text">${nota}</div>
                </div>
            ` : ""}
        </div>
    `;

    return card;
};

window.togglePaymentCard = function (header) {
    const body = header.nextElementSibling;
    const chevron = header.querySelector(".modal-payment-chevron");

    if (body.classList.contains("show")) {
        body.classList.remove("show");
        chevron.classList.remove("rotated");
        header.classList.remove("expanded");
    } else {
        body.classList.add("show");
        chevron.classList.add("rotated");
        header.classList.add("expanded");
    }
};

// =====================
// Modal Nuevo Lugar
// =====================
(function () {
    const modalLugar = document.getElementById("modalNuevoLugar");
    if (modalLugar) {
        let currentLugarStep = 1;
        const totalLugarSteps = 2;

        const btnNext = document.getElementById("btn-lugar-next");
        const btnPrev = document.getElementById("btn-lugar-previous");
        const btnSubmit = document.getElementById("btn-lugar-submit");
        const progressBarFill = document.getElementById("progress-bar-fill-lugar");
        const progressText = document.getElementById("progress-step-text-lugar");
        const progressPercentage = document.getElementById("progress-percentage-lugar");

        function updateLugarStep() {
            // Actualizar contenido
            const steps = modalLugar.querySelectorAll(".step-content-lugar");
            steps.forEach(content => content.classList.remove("active"));

            const activeStep = modalLugar.querySelector(
                '.step-content-lugar[data-step="' + currentLugarStep + '"]'
            );
            if (activeStep) {
                activeStep.classList.add("active");
            }

            // Actualizar barra de progreso
            if (progressBarFill && progressText && progressPercentage) {
                const progress = ((currentLugarStep - 1) / (totalLugarSteps - 1)) * 100;
                progressBarFill.style.width = progress + "%";
                progressText.textContent =
                    "Paso " + currentLugarStep + " de " + totalLugarSteps + ": " +
                    (currentLugarStep === 1 ? "Información Básica" : "Ubicación y Detalles");
                progressPercentage.textContent = Math.round(progress) + "%";
            }

            // Actualizar botones
            if (btnPrev) btnPrev.style.display = currentLugarStep === 1 ? "none" : "inline-block";

            if (currentLugarStep === totalLugarSteps) {
                if (btnNext) btnNext.style.display = "none";
                if (btnSubmit) btnSubmit.style.display = "inline-block";
            } else {
                if (btnNext) btnNext.style.display = "inline-block";
                if (btnSubmit) btnSubmit.style.display = "none";
            }
        }

        // Navegación
        if (btnNext) {
            btnNext.addEventListener("click", function () {
                if (currentLugarStep < totalLugarSteps) {
                    currentLugarStep++;
                    updateLugarStep();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener("click", function () {
                if (currentLugarStep > 1) {
                    currentLugarStep--;
                    updateLugarStep();
                }
            });
        }

        // Enviar formulario
        if (btnSubmit) {
            btnSubmit.addEventListener("click", function () {
                const form = document.getElementById("form-nuevo-lugar");
                const formData = new FormData(form);

                // Validar campos requeridos básicos
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                fetch("?r=lugares/create-ajax", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-Token": yii.getCsrfToken()
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Agregar al dropdown
                            const dropdown = document.getElementById("productos-id_lugar");
                            if (dropdown) {
                                const option = new Option(data.lugar.nombre, data.lugar.id, true, true);
                                dropdown.add(option);
                                dropdown.value = data.lugar.id; // Seleccionar el nuevo valor
                            }

                            // Cerrar modal y resetear
                            const modalInstance = bootstrap.Modal.getInstance(modalLugar);
                            if (modalInstance) {
                                modalInstance.hide();
                            } else {
                                // Fallback si no hay instancia
                                const newModal = new bootstrap.Modal(modalLugar);
                                newModal.hide();
                            }

                            form.reset();
                            currentLugarStep = 1;
                            updateLugarStep();

                            // Notificar
                            alert("Lugar registrado exitosamente");
                        } else {
                            alert("Error: " + (data.message || "No se pudo registrar el lugar"));
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error al registrar el lugar");
                    });
            });
        }

        // Reset al cerrar
        modalLugar.addEventListener("hidden.bs.modal", function () {
            const form = document.getElementById("form-nuevo-lugar");
            if (form) form.reset();
            currentLugarStep = 1;
            updateLugarStep();
        });

        // Inicializar
        updateLugarStep();

    }
})();

// =====================
// Modal Nueva Categoría
// =====================
(function () {
    const modalCategoria = document.getElementById("modalNuevaCategoria");
    if (modalCategoria) {
        const btnSubmit = document.getElementById("btn-categoria-submit");

        if (btnSubmit) {
            btnSubmit.addEventListener("click", function () {
                const form = document.getElementById("form-nueva-categoria");
                const formData = new FormData(form);

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                fetch("?r=categorias/create-ajax", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-Token": yii.getCsrfToken()
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Agregar al dropdown
                            const dropdown = document.getElementById("productos-id_categoria");
                            if (dropdown) {
                                const option = new Option(data.categoria.titulo, data.categoria.id, true, true);
                                dropdown.add(option);
                                dropdown.value = data.categoria.id; // Seleccionar el nuevo valor
                            }

                            // Cerrar modal y resetear
                            const modalInstance = bootstrap.Modal.getInstance(modalCategoria);
                            if (modalInstance) {
                                modalInstance.hide();
                            } else {
                                const newModal = new bootstrap.Modal(modalCategoria);
                                newModal.hide();
                            }

                            form.reset();

                            // Notificar
                            alert("Categoría registrada exitosamente");
                        } else {
                            alert("Error: " + (data.message || "No se pudo registrar la categoría"));
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error al registrar la categoría");
                    });
            });
        }

        // Reset al cerrar
        modalCategoria.addEventListener("hidden.bs.modal", function () {
            const form = document.getElementById("form-nueva-categoria");
            if (form) form.reset();
        });
    }
})();

// =====================
// Modal Selección de Producto
// =====================
(function () {
    let productosData = [];
    let productoSeleccionado = null;
    let modalMode = "entradas"; // 'entradas' o 'salidas'
    let filtroLugarActivo = null;
    let onProductoSeleccionado = null; // Callback function
    let getStockReservado = null; // Función para obtener stock ya seleccionado

    const modal = document.getElementById("modalSeleccionProducto");
    if (!modal) return;

    const busquedaInput = document.getElementById("busqueda-producto-modal");
    const productosGrid = document.getElementById("productos-grid-modal");
    const loadingDiv = document.getElementById("loading-productos-modal");
    const sinResultadosDiv = document.getElementById("sin-resultados-modal");
    const btnAceptar = document.getElementById("btn-aceptar-seleccion-producto");
    const btnCancelar = document.getElementById("btn-cancelar-seleccion-producto");
    const filtrosLugaresContainer = document.getElementById("filtros-lugares-container");
    const filtrosLugares = document.getElementById("filtros-lugares");

    // Función para abrir el modal
    window.abrirModalSeleccionProducto = function (mode, callback, options) {
        modalMode = mode;
        onProductoSeleccionado = callback;
        productoSeleccionado = null;
        filtroLugarActivo = null;

        // Opciones adicionales (opcional)
        if (options && typeof options.getStockReservado === 'function') {
            getStockReservado = options.getStockReservado;
        } else {
            getStockReservado = null;
        }

        if (btnAceptar) {
            btnAceptar.disabled = true;
        }
        if (busquedaInput) {
            busquedaInput.value = "";
        }

        // Mostrar filtros solo para salidas
        if (mode === "salidas") {
            if (filtrosLugaresContainer) {
                filtrosLugaresContainer.style.display = "block";
            }
            cargarFiltrosLugares();
        } else {
            if (filtrosLugaresContainer) {
                filtrosLugaresContainer.style.display = "none";
            }
        }

        // Abrir modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Cargar productos
        cargarProductos();
    };

    // Función para cargar filtros de lugares
    function cargarFiltrosLugares() {
        if (!filtrosLugares) return;

        fetch("?r=lugares/lista")
            .then(response => response.json())
            .then(data => {
                filtrosLugares.innerHTML = `
                    <button class="filtro-lugar-btn active" data-lugar-id="">
                        <i class="bi bi-funnel"></i> Todos
                    </button>
                `;

                data.forEach(lugar => {
                    const btn = document.createElement("button");
                    btn.className = "filtro-lugar-btn";
                    btn.dataset.lugarId = lugar.id;
                    btn.innerHTML = `<i class="bi bi-geo-alt"></i> ${lugar.nombre}`;
                    filtrosLugares.appendChild(btn);
                });

                // Agregar eventos a los filtros
                document.querySelectorAll(".filtro-lugar-btn").forEach(btn => {
                    btn.addEventListener("click", function () {
                        document.querySelectorAll(".filtro-lugar-btn").forEach(b =>
                            b.classList.remove("active")
                        );
                        this.classList.add("active");
                        filtroLugarActivo = this.dataset.lugarId || null;
                        renderizarProductos();
                    });
                });
            })
            .catch(error => {
                console.error("Error cargando lugares:", error);
            });
    }

    // Función para cargar productos
    function cargarProductos() {
        if (loadingDiv) loadingDiv.style.display = "block";
        if (productosGrid) productosGrid.style.display = "none";
        if (sinResultadosDiv) sinResultadosDiv.style.display = "none";

        const url =
            modalMode === "salidas"
                ? "?r=productos/lista-con-stock"
                : "?r=productos/lista-todos";

        fetch(url)
            .then(response => response.json())
            .then(data => {
                productosData = data;
                if (loadingDiv) loadingDiv.style.display = "none";
                renderizarProductos();
            })
            .catch(error => {
                console.error("Error cargando productos:", error);
                if (loadingDiv) loadingDiv.style.display = "none";
                if (sinResultadosDiv) sinResultadosDiv.style.display = "block";
            });
    }

    // Función para renderizar productos
    function renderizarProductos() {
        const textoBusqueda = (busquedaInput?.value || "").toLowerCase().trim();

        let productosFiltrados = productosData;

        // Filtrar por búsqueda
        if (textoBusqueda) {
            productosFiltrados = productosFiltrados.filter(p => {
                const marca = (p.marca || "").toLowerCase();
                const modelo = (p.modelo || "").toLowerCase();
                const descripcion = (p.descripcion || "").toLowerCase();
                return (
                    marca.includes(textoBusqueda) ||
                    modelo.includes(textoBusqueda) ||
                    descripcion.includes(textoBusqueda)
                );
            });
        }

        // Filtrar por lugar (solo en modo salidas)
        if (modalMode === "salidas" && filtroLugarActivo) {
            productosFiltrados = productosFiltrados.filter(p => {
                if (p.stocks && Array.isArray(p.stocks)) {
                    return p.stocks.some(s => s.id_lugar == filtroLugarActivo);
                }
                return false;
            });
        }

        // Mostrar resultado
        if (!productosGrid || !sinResultadosDiv) return;

        if (productosFiltrados.length === 0) {
            productosGrid.style.display = "none";
            sinResultadosDiv.style.display = "block";
            return;
        }

        productosGrid.style.display = "block";
        sinResultadosDiv.style.display = "none";
        productosGrid.innerHTML = "";

        if (modalMode === "entradas") {
            renderizarProductosEntradas(productosFiltrados);
        } else {
            renderizarProductosSalidas(productosFiltrados);
        }
    }

    // Renderizar productos para entradas
    function renderizarProductosEntradas(productos) {
        productos.forEach(producto => {
            const stockTotal = producto.stock_total || 0;
            const precioVenta = parseFloat(producto.precio_venta || 0);

            const card = document.createElement("div");
            card.className = "col-12";
            card.innerHTML = `
                <div class="producto-card-modal" data-producto-id="${producto.id}">
                    <div class="producto-card-title">${producto.marca || ""} ${producto.modelo || ""}</div>
                    <div class="producto-card-description">${producto.descripcion || "Sin descripción"}</div>
                    <div class="producto-card-info">
                        <div class="producto-info-item">
                            <span class="producto-info-label">Stock Total</span>
                            <span class="producto-info-value stock">${stockTotal} uds</span>
                        </div>
                        <div class="producto-info-item">
                            <span class="producto-info-label">Precio Venta</span>
                            <span class="producto-info-value precio">$${precioVenta.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;

            card.querySelector(".producto-card-modal").addEventListener("click", function () {
                seleccionarProducto(producto, this);
            });

            productosGrid.appendChild(card);
        });
    }

    // Renderizar productos para salidas
    function renderizarProductosSalidas(productos) {
        productos.forEach(producto => {
            // Obtener stocks del producto
            const stocks = producto.stocks || [];

            // Filtrar por lugar si hay un filtro activo
            const stocksFiltrados = filtroLugarActivo
                ? stocks.filter(s => s.id_lugar == filtroLugarActivo)
                : stocks;

            // Crear una tarjeta por cada stock
            stocksFiltrados.forEach(stock => {
                const precioVenta = parseFloat(producto.precio_venta || 0);
                const cantidadBD = stock.cantidad || 0;

                // Calcular stock reservado (ya seleccionado en otras tarjetas)
                let stockReservado = 0;
                if (getStockReservado && typeof getStockReservado === 'function') {
                    stockReservado = getStockReservado(producto.id, stock.id_lugar);
                }

                // Stock disponible real = stock en BD - stock ya seleccionado
                const cantidadDisponible = cantidadBD - stockReservado;

                // Determinar si el producto está deshabilitado
                const deshabilitado = cantidadDisponible <= 0;
                const clasesAdicionales = deshabilitado ? ' opacity-50' : '';
                const estiloPointer = deshabilitado ? 'cursor: not-allowed;' : 'cursor: pointer;';

                const card = document.createElement("div");
                card.className = "col-12";
                card.innerHTML = `
                    <div class="producto-card-modal${clasesAdicionales}" data-producto-id="${producto.id}" data-stock-id="${stock.id}" data-lugar-id="${stock.id_lugar}" style="${estiloPointer}">
                        <div class="producto-card-badge">${stock.lugar_nombre || "Sin ubicación"}</div>
                        <div class="producto-card-title">${producto.marca || ""} ${producto.modelo || ""}</div>
                        <div class="producto-card-description">${producto.descripcion || "Sin descripción"}</div>
                        <div class="producto-card-info">
                            <div class="producto-info-item">
                                <span class="producto-info-label">Stock Disponible</span>
                                <span class="producto-info-value stock ${deshabilitado ? 'text-danger' : ''}">${cantidadDisponible} uds</span>
                            </div>
                            <div class="producto-info-item">
                                <span class="producto-info-label">Precio Venta</span>
                                <span class="producto-info-value precio">$${precioVenta.toFixed(2)}</span>
                            </div>
                        </div>
                        ${stockReservado > 0 ? `<div class="text-warning text-center mt-2" style="font-size: 0.75rem;"><i class="bi bi-exclamation-triangle"></i> ${stockReservado} uds. ya seleccionadas</div>` : ''}
                        ${deshabilitado ? `<div class="text-danger text-center mt-2" style="font-size: 0.75rem;"><i class="bi bi-x-circle"></i> Sin stock disponible</div>` : ''}
                    </div>
                `;

                const prodData = {
                    ...producto,
                    stock_seleccionado: {
                        ...stock,
                        cantidad: cantidadDisponible  // Usar cantidad disponible real
                    }
                };

                // Solo permitir selección si hay stock disponible
                if (!deshabilitado) {
                    card.querySelector(".producto-card-modal").addEventListener("click", function () {
                        seleccionarProducto(prodData, this);
                    });
                }

                productosGrid.appendChild(card);
            });
        });
    }

    // Función para seleccionar producto
    function seleccionarProducto(producto, element) {
        // Remover selección de todas las tarjetas
        document.querySelectorAll(".producto-card-modal").forEach(card => {
            card.classList.remove("selected");
        });

        // Seleccionar esta tarjeta
        element.classList.add("selected");
        productoSeleccionado = producto;
        if (btnAceptar) {
            btnAceptar.disabled = false;
        }
    }

    // Buscar productos
    if (busquedaInput) {
        busquedaInput.addEventListener("input", function () {
            renderizarProductos();
        });
    }

    // Botón aceptar
    if (btnAceptar) {
        btnAceptar.addEventListener("click", function () {
            if (productoSeleccionado && onProductoSeleccionado) {
                onProductoSeleccionado(productoSeleccionado);
                const bsModal = bootstrap.Modal.getInstance(modal);
                bsModal.hide();
            }
        });
    }

    // Botón cancelar - limpiar selección
    if (btnCancelar) {
        btnCancelar.addEventListener("click", function () {
            productoSeleccionado = null;
            if (btnAceptar) {
                btnAceptar.disabled = true;
            }
        });
    }

    // Limpiar al cerrar modal
    modal.addEventListener("hidden.bs.modal", function () {
        productoSeleccionado = null;
        productosData = [];
        if (productosGrid) productosGrid.innerHTML = "";
        if (busquedaInput) busquedaInput.value = "";
        if (btnAceptar) btnAceptar.disabled = true;
    });
})();

// =====================
// Modal Cierre de Inventario
// =====================
(function () {
    const modalCierre = document.getElementById("modalCierreInventario");
    if (!modalCierre) return;

    const loadingDiv = document.getElementById("loading-cierre");
    const formDiv = document.getElementById("form-cierre-inventario");
    const btnConfirmar = document.getElementById("btn-confirmar-cierre");
    const checkboxConfirmar = document.getElementById("cierre-confirmar");

    // Cuando se abre el modal, cargar los datos
    modalCierre.addEventListener("show.bs.modal", function () {
        // Mostrar loading y ocultar formulario
        if (loadingDiv) loadingDiv.style.display = "block";
        if (formDiv) formDiv.style.display = "none";
        if (btnConfirmar) btnConfirmar.disabled = true;
        if (checkboxConfirmar) checkboxConfirmar.checked = false;

        // Cargar datos del servidor
        fetch("?r=historico-inventarios/get-data-cierre")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Función para convertir fecha MySQL a formato datetime-local
                    function convertToDateTimeLocal(mysqlDateTime) {
                        // Convierte "YYYY-MM-DD HH:MM:SS" a "YYYY-MM-DDTHH:MM"
                        if (!mysqlDateTime) return "";
                        return mysqlDateTime.substring(0, 16).replace(" ", "T");
                    }

                    // Llenar el formulario con los datos
                    document.getElementById("cierre-fecha-inicio").value = convertToDateTimeLocal(data.data.fecha_inicio);
                    document.getElementById("cierre-fecha-cierre").value = convertToDateTimeLocal(data.data.fecha_cierre);
                    document.getElementById("cierre-cantidad").value = data.data.cantidad_productos;
                    document.getElementById("cierre-valor").value = data.data.valor;

                    // Ocultar loading y mostrar formulario
                    if (loadingDiv) loadingDiv.style.display = "none";
                    if (formDiv) formDiv.style.display = "block";
                } else {
                    alert("Error al cargar los datos: " + (data.message || "Error desconocido"));
                    const modalInstance = bootstrap.Modal.getInstance(modalCierre);
                    if (modalInstance) modalInstance.hide();
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error al cargar los datos del inventario");
                const modalInstance = bootstrap.Modal.getInstance(modalCierre);
                if (modalInstance) modalInstance.hide();
            });
    });

    // Habilitar/deshabilitar botón según checkbox
    if (checkboxConfirmar) {
        checkboxConfirmar.addEventListener("change", function () {
            if (btnConfirmar) {
                btnConfirmar.disabled = !this.checked;
            }
        });
    }

    // Enviar formulario de cierre
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", function () {
            if (!checkboxConfirmar.checked) {
                alert("Debe confirmar que desea cerrar el inventario");
                return;
            }

            // Recolectar datos del formulario
            const formData = new FormData();
            formData.append("fecha_inicio", document.getElementById("cierre-fecha-inicio").value);
            formData.append("fecha_cierre", document.getElementById("cierre-fecha-cierre").value);
            formData.append("cantidad_productos", document.getElementById("cierre-cantidad").value);
            formData.append("valor", document.getElementById("cierre-valor").value);
            formData.append("nota", document.getElementById("cierre-nota").value);
            formData.append("confirmar_cierre", checkboxConfirmar.value);

            // Deshabilitar botón mientras se procesa
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

            // Enviar al servidor
            fetch("?r=historico-inventarios/registrar-cierre", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": yii.getCsrfToken()
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Cierre de inventario registrado exitosamente");
                        // Redirigir a la vista del cierre creado
                        window.location.href = "?r=historico-inventarios/view&id=" + data.inventario_id;
                    } else {
                        alert("Error: " + (data.message || "No se pudo registrar el cierre"));
                        // Restaurar botón
                        btnConfirmar.disabled = false;
                        btnConfirmar.innerHTML = 'Registrar Cierre';
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error al registrar el cierre de inventario");
                    // Restaurar botón
                    btnConfirmar.disabled = false;
                    btnConfirmar.innerHTML = 'Registrar Cierre';
                });
        });
    }

    // Limpiar al cerrar el modal
    modalCierre.addEventListener("hidden.bs.modal", function () {
        if (formDiv) {
            const form = formDiv;
            // Limpiar campos
            document.getElementById("cierre-nota").value = "";
            if (checkboxConfirmar) checkboxConfirmar.checked = false;
            if (btnConfirmar) {
                btnConfirmar.disabled = true;
                btnConfirmar.innerHTML = 'Registrar Cierre';
            }
        }
    });
})();

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


