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
$this->registerJsFile('https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js', ['position' => \yii\web\View::POS_HEAD]);
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

<!-- Header Mobile -->
<header id="header" class="fixed-top d-lg-none">
    <div class="navbar navbar-light shadow-sm d-flex justify-content-center align-items-center fluent-header" style="height: 60px; z-index: 1030;">
        <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand text-center d-flex align-items-center gap-3" style="font-weight: bold; background: linear-gradient(135deg, #71ce5d 0%, #2ab693 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <img src="<?= Yii::getAlias('@web/uploads/Logos/ico_01-10-2025.png') ?>" alt="Logo" class="logo-app" style="height: 40px; width: auto;">
            <?= Html::encode(Yii::$app->name) ?>
        </a>
    </div>
</header>

<!-- Sidebar Desktop -->
<aside id="sidebar" class="d-none d-lg-flex flex-column shadow-lg" style="position: fixed; left: 0; top: 0; width: 320px; height: 100vh; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(40px); z-index: 1030; border-right: 1px solid rgba(0, 0, 0, 0.08);">
    <!-- Sidebar Header -->
    <div class="sidebar-header text-center py-4 px-3" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08);">
        <a href="<?= Yii::$app->homeUrl ?>" class="text-decoration-none d-flex flex-column align-items-center gap-2">
            <img src="<?= Yii::getAlias('@web/uploads/Logos/ico_01-10-2025.png') ?>" alt="Logo" style="height: 50px; width: auto;">
            <div style="font-weight: bold; font-size: 1.25rem; background: linear-gradient(135deg, #71ce5d 0%, #2ab693 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                <?= Html::encode(Yii::$app->name) ?>
            </div>
        </a>
        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="mt-1">
                <strong class="d-block text-center" style="font-size: 0.95rem; color: #495057;">
                    <?= Html::encode(Yii::$app->user->identity->nombre) ?>
                </strong>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar Body - Menu -->
    <div class="sidebar-body flex-grow-1 overflow-auto py-3" style="scrollbar-width: thin;">
        <!-- HOME -->
        <a href="<?= Yii::$app->homeUrl ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#6c757d" style="margin-left: 0.5rem;">
            <i class="bi bi-house fs-5" style="color: #6c757d; width: 24px;"></i>
            <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Home</span>
        </a>

        <?php if (!Yii::$app->user->isGuest): ?>
            <nav class="sidebar-nav px-2">
                <!-- Inventarios -->
                <a href="<?= Yii::$app->urlManager->createUrl(['historico-inventarios/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#6c757d">
                    <i class="bi bi-archive fs-5" style="color: #6c757d; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Inventarios</span>
                </a>
                
                <!-- Entradas -->
                <a href="<?= Yii::$app->urlManager->createUrl(['entradas/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#28a745">
                    <i class="bi bi-box-arrow-in-down fs-5" style="color: #28a745; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Entradas</span>
                </a>
                
                <!-- Salidas -->
                <a href="<?= Yii::$app->urlManager->createUrl(['salidas/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#dc3545">
                    <i class="bi bi-box-arrow-up fs-5" style="color: #dc3545; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Salidas</span>
                </a>
                
                <!-- Clientes -->
                <a href="<?= Yii::$app->urlManager->createUrl(['clientes/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#007bff">
                    <i class="bi bi-people-fill fs-5" style="color: #007bff; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Clientes</span>
                </a>
                
                <!-- POS -->
                <a href="<?= Yii::$app->urlManager->createUrl(['pos/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#fd7e14">
                    <i class="bi bi-receipt fs-5" style="color: #fd7e14; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Punto de Venta (POS)</span>
                </a>
                
                <!-- Lugares -->
                <a href="<?= Yii::$app->urlManager->createUrl(['lugares/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#6f42c1">
                    <i class="bi bi-geo-alt-fill fs-5" style="color: #6f42c1; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Lugares</span>
                </a>
                
                <!-- Proveedores -->
                <a href="<?= Yii::$app->urlManager->createUrl(['proveedores/index']) ?>" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none px-3 py-3 rounded mb-2" data-color="#17a2b8">
                    <i class="bi bi-truck fs-5" style="color: #17a2b8; width: 24px;"></i>
                    <span class="sidebar-nav-text" style="color: #495057; font-weight: 500;">Proveedores</span>
                </a>
            </nav>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar Footer - Login/Logout -->
    <div class="sidebar-footer p-3" style="border-top: 1px solid rgba(0, 0, 0, 0.08);">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-person-circle"></i>
                <span>Iniciar Sesión</span>
            </a>
        <?php else: ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['site/logout']) ?>" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2" data-method="post">
                <i class="bi bi-power"></i>
                <span>Cerrar Sesión</span>
            </a>
        <?php endif; ?>
    </div>
</aside>

<main id="main" class="flex-shrink-0" role="main" style="padding-top: 60px; padding-bottom: 80px; min-height: calc(100vh - 140px);">
    <style>
        @media (min-width: 992px) {
            #main {
                margin-left: 320px;
                padding-top: 40px !important;
                padding-bottom: 40px !important;
                min-height: 100vh !important;
            }
        }
    </style>
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<!-- Footer Mobile -->
<footer id="footer" class="fixed-bottom shadow-sm fluent-footer d-lg-none" style="height: 80px; z-index: 1029;">
    <div class="container h-100" style="padding-left: 40px; padding-right: 40px;">
        <div class="d-flex justify-content-between align-items-center h-100">
            <div class="d-flex justify-content-center align-items-center gap-4" style="transform: scale(0.85); transform-origin: center;">
                <a href="<?= Yii::$app->homeUrl ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-house fs-4"></i>
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['site/menu']) ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-list-task fs-4"></i>
                </a>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="text-decoration-none text-dark" style="margin-right: 30px;">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/logout']) ?>" class="text-decoration-none text-dark" data-method="post" style="margin-right: 30px;">
                        <i class="bi bi-power fs-4"></i>
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center" style="transform: scale(0.85); transform-origin: center;">
                <?= DollarPriceWidget::widget() ?>
            </div>
        </div>
    </div>
</footer>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_nvo-cliente-modal.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_set-precio-paralelo-modal.php'; ?>


<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_cobros-pendientes-modal.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_cobros-detalles-modal.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-generar-reporte.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-cierre-inventario.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-nvo-lugar.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-nva-categoria.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-product-selection.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-barcode-scanner.php'; ?>

<!-- renderizamos en un archivo separado -->
<?php require __DIR__ . '/modals/_modal-pos-confirmation.php'; ?>



<?php
// CSS para Fluent Design, modales y sidebar
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

/* Sidebar Styles */
#sidebar {
    transition: all 0.3s ease;
}

/* Scrollbar personalizado para sidebar */
.sidebar-body::-webkit-scrollbar {
    width: 6px;
}

.sidebar-body::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-body::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

.sidebar-body::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Sidebar Navigation Items */
.sidebar-nav-item {
    transition: all 0.2s ease;
    position: relative;
    background: transparent;
}

.sidebar-nav-item:hover {
    background: rgba(0, 0, 0, 0.03);
    transform: translateX(4px);
}

.sidebar-nav-item:active {
    transform: translateX(2px);
}

/* Active state for current page */
.sidebar-nav-item.active {
    background: rgba(0, 0, 0, 0.05);
    font-weight: 600;
}

.sidebar-nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 70%;
    border-radius: 0 4px 4px 0;
}

/* Color indicators for each menu item */
.sidebar-nav-item[data-color='#6c757d']:hover,
.sidebar-nav-item[data-color='#6c757d'].active {
    border-left: 3px solid #6c757d;
}

.sidebar-nav-item[data-color='#28a745']:hover,
.sidebar-nav-item[data-color='#28a745'].active {
    border-left: 3px solid #28a745;
}

.sidebar-nav-item[data-color='#dc3545']:hover,
.sidebar-nav-item[data-color='#dc3545'].active {
    border-left: 3px solid #dc3545;
}

.sidebar-nav-item[data-color='#007bff']:hover,
.sidebar-nav-item[data-color='#007bff'].active {
    border-left: 3px solid #007bff;
}

.sidebar-nav-item[data-color='#fd7e14']:hover,
.sidebar-nav-item[data-color='#fd7e14'].active {
    border-left: 3px solid #fd7e14;
}

.sidebar-nav-item[data-color='#6f42c1']:hover,
.sidebar-nav-item[data-color='#6f42c1'].active {
    border-left: 3px solid #6f42c1;
}

.sidebar-nav-item[data-color='#17a2b8']:hover,
.sidebar-nav-item[data-color='#17a2b8'].active {
    border-left: 3px solid #17a2b8;
}

/* Sidebar footer button */
.sidebar-footer .btn {
    transition: all 0.2s ease;
}

.sidebar-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

/* Modal Selección de Producto */
.producto-card-modal {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.producto-card-modal:hover {
    border-color: #6c757d;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.producto-card-modal.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
}

.producto-card-modal.selected::before {
    content: '\f26b';
    font-family: 'bootstrap-icons';
    position: absolute;
    top: 10px;
    right: 10px;
    color: #28a745;
    font-size: 1.5rem;
    font-weight: bold;
}

.producto-card-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.producto-card-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e9ecef;
}

.producto-info-item {
    display: flex;
    flex-direction: column;
}

.producto-info-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.producto-info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
}

.producto-info-value.stock {
    color: #17a2b8;
}

.producto-info-value.precio {
    color: #28a745;
}

.producto-card-badge {
    display: inline-block;
    background: #6c757d;
    color: white;
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 10px;
    width: fit-content;
}

.producto-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

.filtro-lugar-btn {
    padding: 8px 16px;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    background: white;
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filtro-lugar-btn:hover {
    border-color: #6c757d;
    background: #f8f9fa;
}

.filtro-lugar-btn.active {
    border-color: #28a745;
    background: #28a745;
    color: white;
}

.filtro-lugar-btn i {
    margin-right: 5px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .producto-card-badge {
        font-size: 0.6rem;
        padding: 3px 8px;
        border-radius: 8px;
    }
    
    .producto-card-modal {
        padding: 15px;
    }
    
    .producto-card-title {
        font-size: 0.9rem;
    }
}



/* Modal Lugar - Barra de Progreso */
.progress-bar-container-lugar {
    margin-bottom: 30px;
}

.progress-bar-wrapper-lugar {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill-lugar {
    height: 100%;
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    width: 0%;
}

.progress-info-lugar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-step-text-lugar {
    font-weight: 600;
    color: #007bff;
}

.progress-percentage-lugar {
    font-weight: 500;
}

.step-content-lugar {
    display: none;
    animation: fadeInModal 0.3s ease;
}

.step-content-lugar.active {
    display: block;
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

// CSS y JavaScript para Modal de Detalles de Cobros
$this->registerCss("
/* Estilos para Modal de Detalles de Cobros */
.invoice-summary-modal {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.summary-item-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
}

.summary-label-modal {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.summary-value-modal {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

/* Barra de Progreso del Modal */
.progress-bar-container-modal {
    margin-bottom: 30px;
}

.progress-bar-wrapper-modal {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill-modal {
    height: 100%;
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    position: relative;
}

.progress-bar-fill-modal::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

.progress-info-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-step-text-modal {
    font-weight: 600;
    color: #007bff;
}

.progress-percentage-modal {
    font-weight: 500;
}

/* Tarjetas de pago en el modal */
.modal-payment-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.modal-payment-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.modal-payment-card-header {
    padding: 12px 16px;
    background: #f8f9fa;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s ease;
}

.modal-payment-card-header:hover {
    background: #e9ecef;
}

.modal-payment-card-header.expanded {
    background: #e7f3ff;
    border-bottom: 1px solid #e9ecef;
}

.modal-payment-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.modal-payment-date {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.modal-payment-amount {
    font-weight: 700;
    color: #28a745;
    font-size: 1rem;
}

.modal-payment-method {
    background: rgba(108, 117, 125, 0.1);
    color: #495057;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.modal-payment-chevron {
    color: #6c757d;
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.modal-payment-chevron.rotated {
    transform: rotate(180deg);
}

.modal-payment-card-body {
    padding: 16px;
    background: white;
    display: none;
}

.modal-payment-card-body.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-payment-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f1f1f1;
}

.modal-payment-detail-row:last-child {
    border-bottom: none;
}

.modal-payment-detail-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.85rem;
}

.modal-payment-detail-value {
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-align: right;
}

.modal-payment-nota {
    background: #fff3cd;
    padding: 10px;
    border-radius: 6px;
    margin-top: 10px;
    border-left: 3px solid #ffc107;
}

.modal-payment-nota-label {
    font-weight: 600;
    color: #856404;
    font-size: 0.8rem;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.modal-payment-nota-text {
    color: #856404;
    font-size: 0.85rem;
    font-style: italic;
}
");?>

<!-- Script del Lector de Código de Barras -->
<script>
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
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php
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
?>
