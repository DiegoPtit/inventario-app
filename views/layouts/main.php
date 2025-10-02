<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use app\widgets\DollarPriceWidget;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon-2.ico')]);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous']);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap']);
$this->registerCss("body { font-family: 'Montserrat', sans-serif; }");
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header" class="fixed-top">
    <div class="navbar navbar-light shadow-sm d-flex justify-content-center align-items-center fluent-header" style="height: 60px; z-index: 1030;">
        <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand text-center d-flex align-items-center gap-3" style="font-weight: bold; background: linear-gradient(135deg, #71ce5d 0%, #2ab693 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <img src="<?= Yii::getAlias('@web/uploads/Logos/ico_01-10-2025.png') ?>" alt="Logo" class="logo-app" style="height: 40px; width: auto;">
            <?= Html::encode(Yii::$app->name) ?>
        </a>
    </div>
</header>

<main id="main" class="flex-shrink-0" role="main" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 140px);">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="fixed-bottom shadow-sm fluent-footer" style="height: 80px; z-index: 1029;">
    <div class="container h-100" style="padding-left: 40px; padding-right: 40px;">
        <div class="d-flex justify-content-between align-items-center h-100">
            <div class="d-flex justify-content-center align-items-center gap-5">
                <a href="<?= Yii::$app->homeUrl ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-house fs-4"></i>
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['site/menu']) ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-list-task fs-4"></i>
                </a>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="text-decoration-none text-dark" style="margin-right: 30px;">
                        <i class="bi bi-box-arrow-in-right fs-4"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/logout']) ?>" class="text-decoration-none text-dark" data-method="post" style="margin-right: 30px;">
                        <i class="bi bi-box-arrow-right fs-4"></i>
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <?= DollarPriceWidget::widget() ?>
            </div>
        </div>
    </div>
</footer>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNuevoClienteLabel">
                    <i class="bi bi-person-plus-fill"></i>
                    Registrar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                
                <!-- Indicador de Pasos -->
                <div class="stepper-container-modal mb-4">
                    <div class="stepper-modal">
                        <div class="stepper-progress-modal" id="stepper-progress-modal"></div>
                        
                        <div class="step-modal active" data-step="1">
                            <div class="step-circle-modal">
                                <span class="step-number-modal">1</span>
                                <i class="bi bi-check"></i>
                            </div>
                            <div class="step-label-modal">Personal</div>
                        </div>
                        
                        <div class="step-modal" data-step="2">
                            <div class="step-circle-modal">
                                <span class="step-number-modal">2</span>
                                <i class="bi bi-check"></i>
                            </div>
                            <div class="step-label-modal">Contacto</div>
                        </div>
                        
                        <div class="step-modal" data-step="3">
                            <div class="step-circle-modal">
                                <span class="step-number-modal">3</span>
                                <i class="bi bi-check"></i>
                            </div>
                            <div class="step-label-modal">Estado</div>
                        </div>
                    </div>
                </div>

                <form id="form-nuevo-cliente-modal">
                    <!-- PASO 1: Información Personal -->
                    <div class="step-content-modal active" data-step="1">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-person-vcard text-primary"></i>
                            Información Personal
                        </h5>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal-cliente-nombre" name="nombre" placeholder="Nombre completo del cliente" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Documento de Identidad</label>
                                <input type="text" class="form-control" id="modal-cliente-documento" name="documento_identidad" placeholder="Cédula, RNC, Pasaporte...">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Edad</label>
                                <input type="number" class="form-control" id="modal-cliente-edad" name="edad" min="0" max="150" placeholder="Edad del cliente">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: Información de Contacto -->
                    <div class="step-content-modal" data-step="2">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-telephone text-primary"></i>
                            Información de Contacto
                        </h5>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="tel" class="form-control" id="modal-cliente-telefono" name="telefono" placeholder="Número de teléfono">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" class="form-control" id="modal-cliente-ubicacion" name="ubicacion" placeholder="Dirección completa del cliente">
                        </div>
                    </div>

                    <!-- PASO 3: Estado del Cliente -->
                    <div class="step-content-modal" data-step="3">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-clipboard-check text-primary"></i>
                            Estado del Cliente
                        </h5>
                        
                        <input type="hidden" id="modal-cliente-status" name="status" value="Solvente">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="status-card-modal status-solvente selected" data-status="Solvente">
                                    <div class="status-icon-modal text-success">
                                        <i class="bi bi-check-circle-fill fs-1"></i>
                                    </div>
                                    <div class="status-title-modal fw-bold">Solvente</div>
                                    <small class="text-muted">Cliente al día con sus pagos</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="status-card-modal status-moroso" data-status="Moroso">
                                    <div class="status-icon-modal text-danger">
                                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                    </div>
                                    <div class="status-title-modal fw-bold">Moroso</div>
                                    <small class="text-muted">Cliente con pagos pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" id="btn-modal-previous" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btn-modal-next">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="btn-modal-submit" style="display: none;">
                    <i class="bi bi-check-circle"></i> Registrar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Actualizar Precio Paralelo -->
<div class="modal fade" id="modalActualizarPrecioParalelo" tabindex="-1" aria-labelledby="modalActualizarPrecioParaleloLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalActualizarPrecioParaleloLabel">
                    <i class="bi bi-currency-exchange"></i>
                    Actualizar Precio Paralelo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="alert alert-warning d-flex align-items-start gap-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <div>
                        <strong>Importante:</strong> Ingrese el precio actual del dólar paralelo obtenido de fuentes confiables como Binance P2P, AirTM, o casas de cambio locales.
                    </div>
                </div>

                <form id="form-actualizar-precio-paralelo">
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-currency-dollar text-success"></i>
                            Precio Paralelo (VES)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">VES</span>
                            <input type="number" step="0.01" class="form-control" id="precio-paralelo-input" name="precio_paralelo" placeholder="Ej: 179.50" required>
                        </div>
                        <small class="text-muted">Ingrese el precio actual del dólar paralelo</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-pencil-square text-info"></i>
                            Observaciones (Opcional)
                        </label>
                        <textarea class="form-control" id="observaciones-precio-paralelo" name="observaciones" rows="3" placeholder="Fuente del precio, notas adicionales..."></textarea>
                        <small class="text-muted">Ej: "Precio obtenido de Binance P2P", "Casa de cambio local"</small>
                    </div>

                    <div class="form-check mb-3 p-3" style="background: #fff3cd; border-radius: 10px; border: 2px solid #ffc107;">
                        <input class="form-check-input" type="checkbox" id="confirmar-actualizacion-precio" name="confirmar_actualizacion" value="1">
                        <label class="form-check-label fw-bold" for="confirmar-actualizacion-precio">
                            <i class="bi bi-check-circle text-warning"></i>
                            Confirmo que el precio ingresado es correcto y actualizado
                        </label>
                        <small class="d-block mt-2 text-muted">
                            Esta acción actualizará el precio paralelo en el sistema.
                        </small>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btn-confirmar-actualizacion-precio" disabled>
                    <i class="bi bi-check-circle"></i> Actualizar Precio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Recordatorio de Cobros Pendientes -->
<div class="modal fade" id="modalRecordatorioCobros" tabindex="-1" aria-labelledby="modalRecordatorioCobrosLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalRecordatorioCobrosLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Recordatorio: Facturas Pendientes de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div id="loading-cobros-pendientes" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando clientes con facturas pendientes...</p>
                </div>
                
                <div id="contenido-cobros-pendientes" style="display: none;">
                    <div class="alert alert-warning d-flex align-items-start gap-3 mb-4" role="alert">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                        <div>
                            <strong>Recordatorio de Cobros</strong><br>
                            Los siguientes clientes tienen facturas pendientes de pago. Haz clic en cada cliente para ver sus facturas y gestionar los cobros.
                        </div>
                    </div>
                    
                    <div class="accordion" id="accordionClientesPendientes">
                        <!-- Los clientes se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cierre de Inventario -->
<div class="modal fade" id="modalCierreInventario" tabindex="-1" aria-labelledby="modalCierreInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalCierreInventarioLabel">
                    <i class="bi bi-cash-coin"></i>
                    Registrar Cierre de Inventario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div id="loading-cierre" class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Calculando datos del inventario...</p>
                </div>
                
                <form id="form-cierre-inventario" style="display: none;">
                    <div class="alert alert-info d-flex align-items-start gap-3" role="alert">
                        <i class="bi bi-info-circle-fill fs-4"></i>
                        <div>
                            <strong>Información:</strong> Este proceso cerrará el período de inventario actual y registrará un resumen de las entradas realizadas en el rango de fechas especificado.
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-check text-primary"></i>
                                Fecha-Hora de Inicio
                            </label>
                            <input type="datetime-local" class="form-control" id="cierre-fecha-inicio" name="fecha_inicio" readonly>
                            <small class="text-muted">Fecha y hora desde la cual se calculan las entradas</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-x text-danger"></i>
                                Fecha-Hora de Cierre
                            </label>
                            <input type="datetime-local" class="form-control" id="cierre-fecha-cierre" name="fecha_cierre" readonly>
                            <small class="text-muted">Fecha y hora actual del cierre</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-box-seam text-warning"></i>
                                Cantidad de Productos
                            </label>
                            <input type="number" class="form-control" id="cierre-cantidad" name="cantidad_productos" readonly>
                            <small class="text-muted">Total de productos ingresados en el período</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-currency-dollar text-success"></i>
                                Valor Total
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="cierre-valor" name="valor" readonly>
                            </div>
                            <small class="text-muted">Valor total del inventario en el período</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-pencil-square text-info"></i>
                            Notas / Observaciones
                        </label>
                        <textarea class="form-control" id="cierre-nota" name="nota" rows="4" placeholder="Ingrese cualquier observación o nota relevante sobre este cierre de inventario..."></textarea>
                    </div>

                    <div class="form-check mb-3 p-3" style="background: #fff3cd; border-radius: 10px; border: 2px solid #ffc107;">
                        <input class="form-check-input" type="checkbox" id="cierre-confirmar" name="confirmar_cierre" value="1">
                        <label class="form-check-label fw-bold" for="cierre-confirmar">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Confirmo que deseo cerrar el inventario con los datos mostrados
                        </label>
                        <small class="d-block mt-2 text-muted">
                            Esta acción creará un registro permanente del cierre de inventario.
                        </small>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-confirmar-cierre" disabled>
                    <i class="bi bi-check-circle"></i> Registrar Cierre
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// CSS para Fluent Design y modales
$this->registerCss("
/* Fluent Design - Efecto Blur para Header y Footer */
.fluent-header {
    background: rgba(255, 255, 255, 0.7) !important;
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

.fluent-footer {
    background: rgba(255, 255, 255, 0.7) !important;
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.08);
}

/* Mejoras adicionales para el efecto Fluent */
.fluent-header::before,
.fluent-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.6) 100%);
    pointer-events: none;
    z-index: -1;
}

/* Ajustes para mejor legibilidad */
.fluent-header .navbar-brand,
.fluent-footer a {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Efecto hover mejorado para enlaces del footer */
.fluent-footer a:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

/* Soporte para navegadores que no soportan backdrop-filter */
@supports not (backdrop-filter: blur(40px)) {
    .fluent-header {
        background: rgba(255, 255, 255, 1) !important;
    }
    
    .fluent-footer {
        background: rgba(255, 255, 255, 1) !important;
    }
}

/* Dark mode support (opcional) */
@media (prefers-color-scheme: dark) {
    .fluent-header {
        background: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .fluent-footer {
        background: rgba(255, 255, 255, 0.7) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .fluent-header::before,
    .fluent-footer::before {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.6) 100%);
    }
}
.stepper-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.stepper-modal::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: #e9ecef;
    z-index: 0;
}

.stepper-progress-modal {
    position: absolute;
    top: 25px;
    left: 0;
    height: 3px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    z-index: 1;
    transition: width 0.3s ease;
    width: 0%;
}

.step-modal {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
    cursor: pointer;
}

.step-circle-modal {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #adb5bd;
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.step-modal.active .step-circle-modal {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transform: scale(1.1);
}

.step-modal.completed .step-circle-modal {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.step-modal.completed .step-circle-modal i {
    display: block;
}

.step-circle-modal i {
    display: none;
}

.step-circle-modal .step-number-modal {
    display: block;
}

.step-modal.completed .step-circle-modal .step-number-modal {
    display: none;
}

.step-label-modal {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
}

.step-modal.active .step-label-modal {
    color: #007bff;
    font-weight: 600;
}

.step-modal.completed .step-label-modal {
    color: #28a745;
}

.step-content-modal {
    display: none;
    animation: fadeInModal 0.3s ease;
}

.step-content-modal.active {
    display: block;
}

@keyframes fadeInModal {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-title-modal {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-card-modal {
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.status-card-modal:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.status-card-modal.selected {
    border-color: #007bff;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
}

.status-card-modal.status-solvente.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
}

.status-card-modal.status-moroso.selected {
    border-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
}

.status-icon-modal {
    margin-bottom: 10px;
}

.status-title-modal {
    font-size: 1.1rem;
    margin-bottom: 5px;
}

/* Estilos para Modal de Recordatorio de Cobros */
.cliente-accordion-item {
    margin-bottom: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.cliente-accordion-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
}

.cliente-accordion-button {
    font-weight: 600;
    font-size: 1.1rem;
    padding: 20px;
    background: transparent !important;
    box-shadow: none !important;
}

.cliente-accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
    color: #856404;
}

.cliente-info-badge {
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 20px;
    margin-left: 10px;
}

.factura-item {
    background: #f8f9fa;
    border-left: 4px solid #dc3545;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.2s;
    cursor: pointer;
}

.factura-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.factura-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.factura-codigo {
    font-weight: 700;
    font-size: 1.1rem;
    color: #2c3e50;
}

.factura-status-badge {
    background: #dc3545;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.factura-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.factura-detail-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 8px;
}

.factura-detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.factura-detail-value {
    font-weight: 700;
    font-size: 1.1rem;
}

.factura-detail-value.total {
    color: #dc3545;
}

.factura-detail-value.pagado {
    color: #28a745;
}

.factura-detail-value.pendiente {
    color: #ffc107;
}

@media (max-width: 768px) {
    .factura-details {
        grid-template-columns: 1fr;
    }
}
");

// Variables para JavaScript
$userIsGuest = Yii::$app->user->isGuest;
$userIdentity = Yii::$app->user->identity;
$modalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->modalClosed ?? '0') : '0';
$dateModalClosed = (!$userIsGuest && $userIdentity) ? ($userIdentity->dateModalClosed ?? '') : '';

// JavaScript para el modal
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
    document.getElementById('stepper-progress-modal').style.width = progress + '%';
    
    // Actualizar botones
    const btnPrevious = document.getElementById('btn-modal-previous');
    const btnNext = document.getElementById('btn-modal-next');
    const btnSubmit = document.getElementById('btn-modal-submit');
    
    if (currentModalStep === 1) {
        btnPrevious.style.display = 'none';
    } else {
        btnPrevious.style.display = 'inline-block';
    }
    
    if (currentModalStep === totalModalSteps) {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-block';
    } else {
        btnNext.style.display = 'inline-block';
        btnSubmit.style.display = 'none';
        }
    }

    // Botón siguiente del modal
    document.getElementById('btn-modal-next').addEventListener('click', function() {
        if (currentModalStep < totalModalSteps) {
            currentModalStep++;
            updateModalStep();
        }
    });

    // Botón anterior del modal
    document.getElementById('btn-modal-previous').addEventListener('click', function() {
        if (currentModalStep > 1) {
            currentModalStep--;
            updateModalStep();
        }
    });

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
            document.getElementById('modal-cliente-status').value = status;
        });
    });

    // Reset del modal cuando se cierra
    document.getElementById('modalNuevoCliente').addEventListener('hidden.bs.modal', function() {
        currentModalStep = 1;
        updateModalStep();
        document.getElementById('form-nuevo-cliente-modal').reset();
        document.getElementById('modal-cliente-status').value = 'Solvente';
        document.querySelectorAll('.status-card-modal').forEach(c => c.classList.remove('selected'));
        document.querySelector('.status-card-modal[data-status=\"Solvente\"]').classList.add('selected');
    });

    // Inicializar modal step
    updateModalStep();
    
    // Exponer función global para resetear el modal
    window.resetModalClienteStep = function() {
        currentModalStep = 1;
        updateModalStep();
        document.getElementById('modal-cliente-status').value = 'Solvente';
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
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
