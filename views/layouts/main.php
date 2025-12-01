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
            <div class="mt-3 d-flex flex-column align-items-center gap-2">
                <!-- Bell Icon with Notification -->
                <div class="position-relative" id="bell-notification-container" style="cursor: pointer;">
                    <i class="bi bi-bell-fill fs-4" id="bell-icon" style="color: #6c757d; transition: all 0.3s ease;"></i>
                    <!-- Red Micro-Dot Indicator (hidden by default) -->
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" 
                          id="bell-notification-dot" 
                          style="display: none; width: 10px; height: 10px;">
                        <span class="visually-hidden">Pending payments</span>
                    </span>
                    <!-- Badge with count -->
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                          id="bell-notification-count" 
                          style="display: none; font-size: 0.65rem; padding: 0.25em 0.45em;">
                        0
                    </span>
                </div>
                
                <!-- User Name -->
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
        <?php if (!Yii::$app->user->isGuest): ?>
            <!-- Dollar Price Widget -->
            <div class="mb-3 d-flex justify-content-center">
                <?= DollarPriceWidget::widget() ?>
            </div>
        <?php endif; ?>
        
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
            <div class="d-flex align-items-center gap-3" style="transform: scale(0.85); transform-origin: center;">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <!-- Bell Icon with Notification (Mobile) -->
                    <div class="position-relative" id="bell-notification-container-mobile" style="cursor: pointer;">
                        <i class="bi bi-bell-fill fs-4" id="bell-icon-mobile" style="color: #6c757d; transition: all 0.3s ease;"></i>
                        <!-- Red Micro-Dot Indicator (hidden by default) -->
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" 
                              id="bell-notification-dot-mobile" 
                              style="display: none; width: 8px; height: 8px;">
                            <span class="visually-hidden">Pending payments</span>
                        </span>
                        <!-- Badge with count -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              id="bell-notification-count-mobile" 
                              style="display: none; font-size: 0.6rem; padding: 0.2em 0.4em;">
                            0
                        </span>
                    </div>
                    
                    <!-- Mobile Dollar Price Display (alternating text) -->
                    <div id="mobile-dollar-price" 
                         class="mobile-dollar-price-display" 
                         onclick="openUpdatePriceModal()" 
                         style="cursor: pointer;"
                         title="Toca para actualizar precio paralelo">
                        <span id="mobile-price-text" class="mobile-price-text"></span>
                    </div>
                <?php endif; ?>
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

//Requerimos el css del layout 'main'
require __DIR__ . '/styles/_main-css.php';

//JavaScript para los modales y QuaggaJS (lector de codigo de barras) y modal de generar reporte
require __DIR__ . '/js/_main-js-snippets.php';

?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

