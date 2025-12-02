<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\HistoricoMovimientos;

/** @var yii\web\View $this */
/** @var app\models\Productos $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

// Crear título
$titulo_partes = array_filter([
    $model->marca,
    $model->modelo,
    $model->color
]);
$titulo = implode(' - ', $titulo_partes);
if (empty($titulo)) {
    $titulo = 'Producto #' . $model->id;
}

$this->title = $titulo;
\yii\web\YiiAsset::register($this);

// Procesar fotos
$fotosArray = [];
if (!empty($model->fotos)) {
    $fotosDecoded = json_decode($model->fotos, true);
    if (is_array($fotosDecoded)) {
        $fotosArray = $fotosDecoded;
    }
}

// Obtener las facturas en las que aparece el producto
$itemsFactura = $model->getItemsFacturas()
    ->with(['factura', 'factura.cliente'])
    ->orderBy(['created_at' => SORT_DESC])
    ->all();

// Obtener el stock del producto en diferentes lugares
$stocks = $model->getStocks()
    ->with(['lugar'])
    ->orderBy(['cantidad' => SORT_DESC])
    ->all();

// Histórico de movimientos
$historicoMovimientos = HistoricoMovimientos::find()
    ->where(['id_producto' => $model->id])
    ->with(['producto', 'lugarOrigen', 'lugarDestino', 'factura', 'factura.cliente'])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(10)
    ->all();

// Preparar datos para el gráfico
$stockPorLugar = [];
foreach ($stocks as $stock) {
    if ($stock->lugar && $stock->cantidad > 0) {
        $stockPorLugar[] = [
            'nombre' => $stock->lugar->nombre,
            'cantidad' => $stock->cantidad
        ];
    }
}
?>

<style>
.producto-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header - Carrusel */
.producto-header {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.carousel-container {
    max-height: 500px;
    overflow: hidden;
    background: #f8f9fa;
}

.carousel-item {
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carousel-placeholder {
    width: 100%;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
}

.carousel-placeholder i {
    font-size: 8rem;
    color: #adb5bd;
}

/* Cuerpo - Especificaciones */
.producto-body {
    margin-bottom: 30px;
}

.especificaciones-titulo {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
}

.especificaciones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 15px;
    margin-bottom: 40px;
}

.especificacion-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 12px;
}

.especificacion-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #546e7a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.especificacion-icon i {
    font-size: 1.2rem;
    color: #ffffff;
}

.especificacion-content {
    flex: 1;
    min-width: 0;
}

.especificacion-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

.especificacion-value {
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 600;
    word-wrap: break-word;
}

.especificacion-value.texto-largo {
    font-size: 0.9rem;
    line-height: 1.4;
    font-weight: 500;
}

.especificacion-conversions {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #e9ecef;
}

.especificacion-conversions .conversion-line {
    font-size: 0.7rem;
    color: #6c757d;
    display: block;
    margin-top: 2px;
}

.especificacion-conversions .conversion-line strong {
    color: #495057;
    font-weight: 600;
}

/* Lista de Facturas */
.facturas-section {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-bottom: 30px;
}

.facturas-titulo {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 18px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.facturas-titulo i {
    color: #546e7a;
}

.facturas-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.factura-item {
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 15px;
    align-items: center;
}

.factura-item:last-child {
    border-bottom: none;
}

.factura-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.factura-codigo {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
}

.factura-detalles {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.5;
}

.factura-detalles-mobile {
    display: none;
}

.factura-cantidad {
    background: #546e7a;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.no-facturas {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-facturas i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

/* Lista de Stock */
.stock-section {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-bottom: 30px;
}

.stock-titulo {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 18px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stock-titulo i {
    color: #546e7a;
}

.stock-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.stock-item {
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stock-item:last-child {
    border-bottom: none;
}

.stock-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.stock-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #546e7a;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stock-icon i {
    font-size: 1rem;
}

.stock-lugar {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.stock-cantidad {
    background: #546e7a;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
}

.stock-cantidad.bajo {
    background: #dc3545;
}

.stock-cantidad.medio {
    background: #ffc107;
}

.stock-cantidad.alto {
    background: #28a745;
}

.no-stock {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-stock i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

/* Footer - Botones */
.producto-footer {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
}

.btn-action {
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border: 2px solid;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-editar {
    color: #0d6efd;
    border-color: #0d6efd;
    background: transparent;
}

.btn-editar:hover {
    background: #0d6efd;
    color: white;
}

.btn-borrar {
    color: #dc3545;
    border-color: #dc3545;
    background: transparent;
}

.btn-borrar:hover {
    background: #dc3545;
    color: white;
}

/* Sección de Código de Barras */
.barcode-section {
    width: 100%;
    margin: 40px 0 30px 0;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.barcode-contenedor {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.barcode-contenedor svg {
    max-width: 100%;
    height: auto;
}

@media (max-width: 768px) {
    .especificaciones-grid {
        grid-template-columns: 1fr;
    }
    
    .producto-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }

    /* Stock mobile-friendly */
    .stock-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 0;
    }

    .stock-info {
        width: 100%;
    }

    .stock-cantidad {
        align-self: flex-end;
    }

    .stock-section,
    .facturas-section {
        padding: 16px;
    }

    /* Facturas mobile-friendly */
    .factura-item {
        padding: 14px 0;
        gap: 8px;
    }

    .factura-detalles {
        display: none;
    }

    .factura-detalles-mobile {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
    }

    .factura-cantidad {
        align-self: flex-end;
        padding: 5px 12px;
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .stock-icon {
        width: 32px;
        height: 32px;
    }

    .stock-icon i {
        font-size: 0.9rem;
    }

    .stock-lugar {
        font-size: 0.9rem;
    }

    .stock-cantidad {
        padding: 5px 12px;
        font-size: 0.75rem;
    }

    .stock-titulo,
    .facturas-titulo {
        font-size: 1.15rem;
    }

    .factura-codigo {
        font-size: 0.9rem;
    }

    .factura-detalles-mobile {
        font-size: 0.75rem;
    }

    .especificacion-card {
        padding: 14px;
    }

    .especificacion-icon {
        width: 36px;
        height: 36px;
    }

    .especificacion-icon i {
        font-size: 1.1rem;
    }
}

/* Secciones con separador */
.section-separator { padding: 40px 0; border-top: 2px solid #e9ecef; }
.section-title-separator { font-size: 1.5rem; font-weight: 700; color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.section-title-separator i { color: #546e7a; }

/* Gráfico de stock */
.chart-container-stock { position: relative; height: 300px; display: flex; justify-content: center; align-items: center; margin-top: 20px; }

/* Histórico de movimientos */
.historico-lista { list-style: none; padding: 0; margin: 0; }
.historico-item { padding: 14px 16px; border-left: 3px solid #e9ecef; margin-bottom: 10px; background: #f8f9fa; border-radius: 6px; }
.historico-item.entrada { border-left-color: #28a745; }
.historico-item.salida { border-left-color: #546e7a; }
.historico-item.venta { border-left-color: #007bff; }
.historico-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.historico-accion { font-weight: 600; font-size: 1rem; color: #2c3e50; }
.historico-cantidad { background: white; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; color: #546e7a; border: 1px solid #e9ecef; }
.historico-detalles { color: #6c757d; font-size: 0.85rem; line-height: 1.6; }
.historico-fecha { color: #adb5bd; font-size: 0.75rem; margin-top: 6px; }
.no-data-chart { text-align: center; padding: 40px; color: #6c757d; }
.no-data-chart i { font-size: 3rem; margin-bottom: 15px; color: #adb5bd; }

@media (max-width: 768px) {
    .section-separator { padding: 30px 0; }
}
</style>

<div class="producto-view-container">

    <h2><?= Html::encode($titulo) ?></h2>

    <div class="text-start mb-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Ver todos', Url::to(['productos/index']), [
            'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
            'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
        ]) ?>
    </div>
    
    <!-- HEADER - CARRUSEL DE FOTOS -->
    <div class="producto-header">
        <?php if (!empty($fotosArray)): ?>
            <div id="productCarousel" class="carousel slide carousel-container" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($fotosArray as $index => $foto): ?>
                        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="<?= $index ?>" 
                                class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                                aria-label="Foto <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($fotosArray as $index => $foto): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <?= Html::img(Yii::getAlias('@web') . '/' . $foto, [
                                'alt' => Html::encode($titulo),
                                'class' => 'img-fluid'
                            ]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($fotosArray) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="carousel-placeholder">
                <i class="bi bi-image"></i>
            </div>
        <?php endif; ?>
    </div>

    <!-- CUERPO - ESPECIFICACIONES -->
    <div class="producto-body">
        <h2 class="especificaciones-titulo">Especificaciones del Producto</h2>
        
        <div class="especificaciones-grid">
            <!-- Marca -->
            <?php if (!empty($model->marca)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-tag-fill"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Marca</div>
                    <div class="especificacion-value"><?= Html::encode($model->marca) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Modelo -->
            <?php if (!empty($model->modelo)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Modelo</div>
                    <div class="especificacion-value"><?= Html::encode($model->modelo) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Descripción -->
            <?php if (!empty($model->descripcion)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-card-text"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Descripción</div>
                    <div class="especificacion-value texto-largo"><?= Html::encode($model->descripcion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contenido Neto + Unidad de Medida -->
            <?php if (!empty($model->contenido_neto) || !empty($model->unidad_medida)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-rulers"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Contenido Neto</div>
                    <div class="especificacion-value">
                        <?= $model->contenido_neto ? Html::encode($model->contenido_neto) : '' ?>
                        <?= $model->unidad_medida ? Html::encode($model->unidad_medida) : '' ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Costo -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Costo</div>
                    <div class="especificacion-value">$<?= number_format($model->costo, 2) ?></div>
                    
                    <?php if ($precioParalelo && $precioOficial): ?>
                        <?php 
                        $costoVes = $model->costo * $precioParalelo->precio_ves;
                        $costoUsdOficial = $costoVes / $precioOficial->precio_ves;
                        ?>
                        <div class="especificacion-conversions">
                            <span class="conversion-line">Bs. <?= number_format($costoVes, 2, ',', '.') ?></span>
                            <span class="conversion-line"><strong>$<?= number_format($costoUsdOficial, 2) ?></strong> (BCV)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Precio de Venta -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Precio de Venta</div>
                    <div class="especificacion-value">$<?= number_format($model->precio_venta, 2) ?></div>
                    
                    <?php if ($precioParalelo && $precioOficial): ?>
                        <?php 
                        $precioVentaVes = $model->precio_venta * $precioParalelo->precio_ves;
                        $precioVentaUsdOficial = $precioVentaVes / $precioOficial->precio_ves;
                        ?>
                        <div class="especificacion-conversions">
                            <span class="conversion-line">Bs. <?= number_format($precioVentaVes, 2, ',', '.') ?></span>
                            <span class="conversion-line"><strong>$<?= number_format($precioVentaUsdOficial, 2) ?></strong> (BCV)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Categoría -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-bookmark-fill"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Categoría</div>
                    <div class="especificacion-value">
                        <?= $model->categoria ? Html::encode($model->categoria->titulo) : 'Sin categoría' ?>
                    </div>
                </div>
            </div>

            <!-- Color -->
            <?php if (!empty($model->color)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-palette-fill"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Color</div>
                    <div class="especificacion-value"><?= Html::encode($model->color) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SKU -->
            <?php if (!empty($model->sku)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-upc-scan"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">SKU</div>
                    <div class="especificacion-value"><?= Html::encode($model->sku) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Fecha de Registro -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Fecha de Registro</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></div>
                </div>
            </div>

            <!-- Última Actualización -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Última Actualización</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></div>
                </div>
            </div>
        </div>

        <!-- LISTA DE FACTURAS -->
        <div class="facturas-section">
            <h3 class="facturas-titulo">
                <i class="bi bi-receipt"></i>
                Facturas donde aparece este producto
            </h3>
            
            <?php if (!empty($itemsFactura)): ?>
                <ul class="facturas-lista">
                    <?php foreach ($itemsFactura as $item): ?>
                        <li class="factura-item">
                            <div class="factura-info">
                                <div class="factura-codigo">
                                    <i class="bi bi-file-text"></i>
                                    <?= Html::encode($item->factura->codigo) ?>
                                </div>
                                <div class="factura-detalles">
                                    <?php if ($item->factura->cliente): ?>
                                        <i class="bi bi-person"></i> <?= Html::encode($item->factura->cliente->nombre) ?> • 
                                    <?php endif; ?>
                                    <i class="bi bi-calendar3"></i> <?= Yii::$app->formatter->asDate($item->factura->fecha) ?> • 
                                    $<?= number_format($item->precio_unitario, 2) ?>/ud
                                    <?php if ($precioParalelo && $precioOficial): ?>
                                        <?php 
                                        $precioUnitarioVes = $item->precio_unitario * $precioParalelo->precio_ves;
                                        $precioUnitarioUsdOficial = $precioUnitarioVes / $precioOficial->precio_ves;
                                        ?>
                                        <span style="color: #adb5bd;">•</span> Bs. <?= number_format($precioUnitarioVes, 2, ',', '.') ?>
                                    <?php endif; ?>
                                </div>
                                <div class="factura-detalles-mobile">
                                    <?php if ($item->factura->cliente): ?>
                                        <i class="bi bi-person"></i> <?= Html::encode($item->factura->cliente->nombre) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="factura-cantidad">
                                <?= $item->cantidad ?> ud
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-facturas">
                    <i class="bi bi-inbox"></i>
                    <p>Este producto no ha sido incluido en ninguna factura aún.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- LISTA DE STOCK POR UBICACIÓN -->
        <div class="stock-section">
            <h3 class="stock-titulo">
                <i class="bi bi-box-seam"></i>
                Ubicaciones y Stock de este producto
            </h3>
            
            <?php if (!empty($stocks)): ?>
                <ul class="stock-lista">
                    <?php foreach ($stocks as $stock): ?>
                        <?php
                        // Determinar clase de color basado en cantidad
                        $colorClase = '';
                        if ($stock->cantidad == 0) {
                            $colorClase = 'bajo';
                        } elseif ($stock->cantidad <= 10) {
                            $colorClase = 'medio';
                        } elseif ($stock->cantidad > 10) {
                            $colorClase = 'alto';
                        }
                        ?>
                        <li class="stock-item">
                            <div class="stock-info">
                                <div class="stock-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="stock-lugar">
                                    <?= $stock->lugar ? Html::encode($stock->lugar->nombre) : 'Ubicación no especificada' ?>
                                </div>
                            </div>
                            <div class="stock-cantidad <?= $colorClase ?>">
                                <?= $stock->cantidad ?> unidad<?= $stock->cantidad != 1 ? 'es' : '' ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-stock">
                    <i class="bi bi-box"></i>
                    <p>No hay información de stock registrada para este producto.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- DISTRIBUCIÓN DE STOCK POR UBICACIÓN -->
        <div class="section-separator">
            <h3 class="section-title-separator"><i class="bi bi-pie-chart-fill"></i> Distribución de Stock por Ubicación</h3>
            <?php if (!empty($stockPorLugar)): ?>
                <div class="chart-container-stock">
                    <canvas id="stockChartProducto"></canvas>
                </div>
            <?php else: ?>
                <div class="no-data-chart">
                    <i class="bi bi-inbox"></i>
                    <p>No hay stock disponible para este producto</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- HISTÓRICO DE MOVIMIENTOS -->
        <div class="section-separator">
            <h3 class="section-title-separator"><i class="bi bi-clock-history"></i> Histórico de Movimientos del Producto</h3>
            <?php if (!empty($historicoMovimientos)): ?>
                <ul class="historico-lista">
                    <?php foreach ($historicoMovimientos as $movimiento): ?>
                        <?php
                        $accionClase = '';
                        $accionTexto = '';
                        $accionIcon = '';
                        
                        if ($movimiento->isAccionEntrada()) {
                            $accionClase = 'entrada';
                            $accionTexto = 'ENTRADA';
                            $accionIcon = 'bi-box-arrow-in-down';
                        } elseif ($movimiento->isAccionSalida()) {
                            $accionClase = 'salida';
                            $accionTexto = 'SALIDA';
                            $accionIcon = 'bi-box-arrow-up';
                        } elseif ($movimiento->isAccionVenta()) {
                            $accionClase = 'venta';
                            $accionTexto = 'VENTA';
                            $accionIcon = 'bi-cart-check';
                        }
                        ?>
                        <li class="historico-item <?= $accionClase ?>">
                            <div class="historico-header">
                                <div class="historico-accion">
                                    <i class="bi <?= $accionIcon ?>"></i> <?= $accionTexto ?>
                                </div>
                                <div class="historico-cantidad"><?= $movimiento->cantidad ?> unidades</div>
                            </div>
                            <div class="historico-detalles">
                                <?php if ($movimiento->isAccionVenta() && $movimiento->factura): ?>
                                    <i class="bi bi-receipt"></i>
                                    Factura: <strong><?= Html::encode($movimiento->factura->codigo) ?></strong>
                                    <?php if ($movimiento->factura->cliente): ?>
                                        <br><i class="bi bi-person-fill"></i>
                                        Cliente: <strong><?= Html::encode($movimiento->factura->cliente->nombre) ?></strong>
                                    <?php endif; ?>
                                    <?php if ($movimiento->lugarOrigen): ?>
                                        <br><i class="bi bi-geo-alt"></i>
                                        Desde: <strong><?= Html::encode($movimiento->lugarOrigen->nombre) ?></strong>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($movimiento->lugarOrigen && $movimiento->lugarDestino): ?>
                                        <i class="bi bi-arrow-left-right"></i>
                                        De: <strong><?= Html::encode($movimiento->lugarOrigen->nombre) ?></strong> 
                                        → A: <strong><?= Html::encode($movimiento->lugarDestino->nombre) ?></strong>
                                    <?php elseif ($movimiento->lugarOrigen): ?>
                                        <i class="bi bi-geo-alt"></i>
                                        Desde: <strong><?= Html::encode($movimiento->lugarOrigen->nombre) ?></strong>
                                    <?php elseif ($movimiento->lugarDestino): ?>
                                        <i class="bi bi-geo"></i>
                                        Hacia: <strong><?= Html::encode($movimiento->lugarDestino->nombre) ?></strong>
                                    <?php endif; ?>
                                    <?php if ($movimiento->referencia_id): ?>
                                        <br><i class="bi bi-link-45deg"></i> Referencia: #<?= $movimiento->referencia_id ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="historico-fecha">
                                <i class="bi bi-clock"></i>
                                <?= Yii::$app->formatter->asDatetime($movimiento->created_at) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-data-chart">
                    <i class="bi bi-inbox"></i>
                    <p>No hay movimientos registrados para este producto</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CÓDIGO DE BARRAS DEL PRODUCTO -->
    <?php if (!empty($model->codigo_barra)): ?>
        <div class="barcode-section">
            <div class="barcode-contenedor">
                <svg id="barcode-producto"></svg>
            </div>
        </div>
    <?php endif; ?>

    <!-- FOOTER - BOTONES DE ACCIÓN -->
    <div class="producto-footer">
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-action btn-editar">
            <i class="bi bi-pencil-square"></i>
            Editar
        </a>
        
        <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display: inline;']) ?>
            <button type="submit" class="btn btn-action btn-borrar" 
                    onclick="return confirm('¿Está seguro de que desea eliminar este producto?');">
                <i class="bi bi-trash"></i>
                Borrar
            </button>
        <?= Html::endForm() ?>
    </div>

</div>

<?php
// Gráfico de Chart.js
if (!empty($stockPorLugar)) {
    $stockLabels = json_encode(array_column($stockPorLugar, 'nombre'));
    $stockData = json_encode(array_column($stockPorLugar, 'cantidad'));
    $colores = ['#546e7a', '#78909c', '#90a4ae', '#b0bec5', '#cfd8dc', '#607d8b', '#455a64', '#37474f'];
    $coloresJson = json_encode(array_slice($colores, 0, count($stockPorLugar)));

    $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
        'position' => \yii\web\View::POS_END
    ]);

    $js = <<<JS
(function() {
    function initChart() {
        const ctx = document.getElementById('stockChartProducto');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: $stockLabels,
                    datasets: [{
                        label: 'Stock por Ubicación',
                        data: $stockData,
                        backgroundColor: $coloresJson,
                        hoverOffset: 10,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { 
                                padding: 15, 
                                font: { size: 12, weight: '600' },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: label + ': ' + value + ' (' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    label += context.parsed + ' unidades';
                                    let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((context.parsed / sum) * 100).toFixed(1);
                                    label += ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else if (typeof Chart === 'undefined') {
            setTimeout(initChart, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
})();
JS;
    $this->registerJs($js, \yii\web\View::POS_END);
}

// Generar código de barras si existe
if (!empty($model->codigo_barra)) {
    $this->registerJsFile('https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js', [
        'position' => \yii\web\View::POS_END
    ]);
    
    $codigoBarra = $model->codigo_barra;
    $jsBarcodeScript = <<<JS
(function() {
    function initBarcode() {
        const barcodeElement = document.getElementById('barcode-producto');
        if (barcodeElement && typeof JsBarcode !== 'undefined') {
            JsBarcode("#barcode-producto", "$codigoBarra", {
                format: "CODE128",
                width: 3,
                height: 100,
                displayValue: true,
                fontSize: 16,
                margin: 0,
                background: "#ffffff"
            });
        } else if (typeof JsBarcode === 'undefined') {
            setTimeout(initBarcode, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBarcode);
    } else {
        initBarcode();
    }
})();
JS;
    $this->registerJs($jsBarcodeScript, \yii\web\View::POS_END);
}
?>
