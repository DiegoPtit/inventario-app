<?php

/** @var yii\web\View $this */
/** @var app\models\Productos[] $productos */
/** @var app\models\Clientes[] $clientes */
/** @var float $cobrosCerradas */
/** @var float $cobrosAbiertas */
/** @var array $cobrosParaMostrar */
/** @var float $valorInventario */
/** @var float $valorRecaudado */
/** @var float $proporcionDeuda */
/** @var float $proporcionRecaudado */
/** @var int $clientesSolventes */
/** @var int $clientesMorosos */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Desglose principal';

// Registrar CSS personalizado para las tarjetas de productos
$this->registerCss('
.carousel-container {
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.carousel-container::-webkit-scrollbar {
    height: 8px;
}

.carousel-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.carousel-container::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.carousel-container::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 350px;
    display: flex;
    flex-direction: column;
    min-width: 280px;
    max-width: 280px;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.product-card-header {
    height: 200px;
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
}

.product-card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.product-card-body {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px 0;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    min-height: 2.8rem;
}

.product-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-card-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #28a745;
}

.product-card-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
}

.product-card-conversions .conversion-line {
    display: block;
}

.product-card-conversions strong {
    color: #495057;
    font-weight: 600;
}

/* Estilos para conversiones en otras secciones */
.conversion-subtexts {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
}

.conversion-subtexts .conversion-line {
    display: block;
}

.conversion-subtexts strong {
    color: #495057;
    font-weight: 600;
}

.analytics-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 8px;
    line-height: 1.3;
    text-align: center;
}

.cobro-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
    text-align: right;
}

.product-card-category {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-products i {
    font-size: 4rem;
    margin-bottom: 20px;
}

.carousel-scroll {
    display: flex;
    gap: 20px;
    padding: 0 20px 20px 20px;
}

.clients-table {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.clients-table table {
    min-width: 600px;
}

.badge-solvente {
    background-color: #28a745;
    color: white;
}

.badge-moroso {
    background-color: #dc3545;
    color: white;
}

.table-responsive-custom {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table-responsive-custom table {
    margin-bottom: 0;
}

.clients-carousel {
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.clients-carousel::-webkit-scrollbar {
    height: 8px;
}

.clients-carousel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.clients-carousel::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.clients-carousel::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.clients-scroll {
    display: flex;
    gap: 20px;
    padding: 0 20px 20px 20px;
}

.client-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
    min-width: 300px;
    max-width: 300px;
    height: 280px;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.client-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.client-card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    padding: 15px;
    color: white;
    text-align: center;
    flex-shrink: 0;
}

.client-card-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.3rem;
}

.client-card-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.client-card-body {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.client-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e9ecef;
}

.client-card-row:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.client-card-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
}

.client-card-value {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 120px;
}

.client-card-status {
    display: flex;
    align-items: center;
    justify-content: center;
}

.analytics-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 30px;
}

.analytics-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.analytics-card-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.analytics-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    text-align: center;
}

.analytics-card-body {
    padding: 25px;
}

.chart-container {
    display: flex;
    justify-content: center;
    margin-bottom: 25px;
    height: 300px;
    position: relative;
}

.chart-container canvas {
    max-width: 100%;
    max-height: 100%;
}

.analytics-summary {
    display: flex;
    justify-content: space-around;
    gap: 20px;
    margin-top: 20px;
}

.summary-item {
    text-align: center;
    flex: 1;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.summary-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
}

.summary-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.summary-indicator-closed {
    background-color: #28a745;
}

.summary-indicator-open {
    background-color: #ffc107;
}

.summary-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #333;
}

.amount-positive {
    font-weight: 600;
    color: #28a745;
}

.amount-pending {
    font-weight: 600;
    color: #ffc107;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.status-closed {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-open {
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.cobros-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.cobro-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
}

.cobro-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.cobro-card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.cobro-card-date {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.cobro-card-status {
    display: flex;
    align-items: center;
}

.cobro-card-body {
    padding: 20px;
}

.cobro-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f1f1;
}

.cobro-card-row:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.cobro-card-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    min-width: 140px;
}

.cobro-card-value {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 180px;
}

@media (max-width: 768px) {
    .cobros-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .cobro-card-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .cobro-card-value {
        text-align: left;
        max-width: 100%;
    }
    
    .cobro-card-label {
        min-width: auto;
    }
}

.cobro-card-cerrada {
    border-left: 4px solid #28a745;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.cobros-detalle {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.btn-detalle {
    background: none;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.8rem;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-detalle:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

.btn-detalle i {
    transition: transform 0.3s ease;
}

.btn-detalle.active i {
    transform: rotate(180deg);
}

.detalle-content {
    margin-top: 10px;
    padding: 10px;
    background: rgba(248, 249, 250, 0.5);
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.detalle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid rgba(233, 236, 239, 0.5);
    font-size: 0.8rem;
}

.detalle-row:last-child {
    border-bottom: none;
}

.detalle-fecha {
    color: #6c757d;
    font-weight: 500;
    min-width: 80px;
}

.detalle-monto {
    color: #28a745;
    font-weight: 600;
    text-align: right;
    min-width: 80px;
}

.detalle-metodo {
    background: rgba(108, 117, 125, 0.1);
    color: #495057;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
    text-transform: capitalize;
}

@media (max-width: 768px) {
    .detalle-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .detalle-fecha,
    .detalle-monto {
        min-width: auto;
        text-align: left;
    }
}
');

?>

<div class="site-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-5">
            <h2 class="text-start">Productos en Inventario</h2>
            <div class="text-start mt-3">
                <?= Html::a('Ver todos <i class="bi bi-arrow-right"></i>', Url::to(['productos/index']), [
                    'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
        </div>

        <?php if (empty($productos)): ?>
            <div class="no-products">
                <i class="bi bi-box-seam"></i>
                <h3>No hay productos disponibles</h3>
                <p>¡Agrega productos al inventario para mostrarlos aquí!</p>
            </div>
        <?php else: ?>
            <div class="carousel-container">
                <div class="carousel-scroll">
                    <?php foreach ($productos as $producto): ?>
                        <?= Html::beginTag('a', [
                            'href' => Url::to(['productos/view', 'id' => $producto->id]),
                            'class' => 'product-card'
                        ]) ?>
                            <div class="product-card-header">
                                <?php
                                $fotos = null;
                                if (!empty($producto->fotos)) {
                                    $fotosArray = json_decode($producto->fotos, true);
                                    if (is_array($fotosArray) && !empty($fotosArray)) {
                                        $fotos = reset($fotosArray); // Obtener la primera foto
                                    }
                                }
                                ?>
                                
                                <?php if ($fotos): ?>
                                    <?= Html::img(Yii::getAlias('@web') . '/' . $fotos, [
                                        'alt' => Html::encode($producto->marca . ' ' . $producto->modelo),
                                        'class' => 'product-card-image'
                                    ]) ?>
                                <?php else: ?>
                                    <div class="product-card-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-card-body">
                                <?php
                                // Crear título concatenando marca, modelo, color y descripción
                                $titulo_partes = array_filter([
                                    $producto->marca,
                                    $producto->modelo,
                                    $producto->color,
                                    $producto->descripcion
                                ]);
                                $titulo = implode(' - ', $titulo_partes);
                                if (empty($titulo)) {
                                    $titulo = 'Producto #' . $producto->id;
                                }
                                ?>
                                
                                <h3 class="product-card-title" title="<?= Html::encode($titulo) ?>">
                                    <?= Html::encode($titulo) ?>
                                </h3>
                                
                                <div class="product-card-footer">
                                    <div>
                                        <span class="product-card-price">
                                            $<?= number_format($producto->precio_venta, 2) ?>
                                        </span>
                                        
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $precioVes = $producto->precio_venta * $precioParalelo->precio_ves;
                                            $precioUsdOficial = $precioVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="product-card-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($precioVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($precioUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($producto->categoria): ?>
                                        <span class="product-card-category">
                                            <?= Html::encode($producto->categoria->titulo) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="product-card-category">
                                            Sin categoría
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?= Html::endTag('a') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Botón para agregar más productos -->
        <div class="text-center mt-4">
            <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Agregar más productos al Inventario', Url::to(['productos/create']), [
                'class' => 'btn btn-outline-success btn-lg w-100'
            ]) ?>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Carrusel de Clientes -->
        <div class="row">
            <div class="col-12">
            <div class="text-center mb-5">
            <h2 class="text-start">Clientes Registrados</h2>
            <div class="text-start mt-3">
                <?= Html::a('Ver todos <i class="bi bi-arrow-right"></i>', Url::to(['clientes/index']), [
                    'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
        </div>
                
                <?php if (empty($clientes)): ?>
                    <div class="no-products">
                        <i class="bi bi-person-x"></i>
                        <h4>No hay clientes registrados</h4>
                        <p>¡Registra clientes para mostrarlos aquí!</p>
                    </div>
                <?php else: ?>
                    <div class="clients-carousel">
                        <div class="clients-scroll">
                            <?php foreach ($clientes as $cliente): ?>
                                <?= Html::beginTag('a', [
                                    'href' => Url::to(['clientes/view', 'id' => $cliente->id]),
                                    'class' => 'client-card'
                                ]) ?>
                                    <div class="client-card-header">
                                        <div class="client-card-avatar">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <h4 class="client-card-name" title="<?= Html::encode($cliente->nombre) ?>">
                                            <?= Html::encode($cliente->nombre) ?>
                                        </h4>
                                    </div>
                                    
                                    <div class="client-card-body">
                                        <div class="client-card-row">
                                            <span class="client-card-label">
                                                <i class="bi bi-card-text me-2"></i>
                                                Documento:
                                            </span>
                                            <span class="client-card-value" title="<?= Html::encode($cliente->documento_identidad ?: 'N/A') ?>">
                                                <?= Html::encode($cliente->documento_identidad ?: 'N/A') ?>
                                            </span>
                                        </div>
                                        
                                        <div class="client-card-row">
                                            <span class="client-card-label">
                                                <i class="bi bi-telephone me-2"></i>
                                                Teléfono:
                                            </span>
                                            <span class="client-card-value" title="<?= Html::encode($cliente->telefono ?: 'N/A') ?>">
                                                <?= Html::encode($cliente->telefono ?: 'N/A') ?>
                                            </span>
                                        </div>
                                        
                                        <div class="client-card-row">
                                            <span class="client-card-label">
                                                <i class="bi bi-shield-check me-2"></i>
                                                Status:
                                            </span>
                                            <div class="client-card-status">
                                                <?php if ($cliente->isStatusSolvente()): ?>
                                                    <span class="badge badge-solvente px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Solvente
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-moroso px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Moroso
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?= Html::endTag('a') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Botón para agregar más productos -->
                <div class="text-center mt-4">
                    <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Añadir más clientes', Url::to(['clientes/create']), [
                        'class' => 'btn btn-outline-success btn-lg w-100'
                    ]) ?>
                </div>
                
                <!-- Gráfica de Morosos vs Solventes -->
                <div class="row mt-5">
                    <div class="col-md-6 offset-md-3">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">Estado de Clientes: Morosos vs Solventes</h5>
                            </div>
                            <div class="analytics-card-body">
                                <div class="chart-container">
                                    <canvas id="clientesStatusChart"></canvas>
                                </div>
                                
                                <div class="analytics-summary">
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator" style="background-color: #28a745;"></span>
                                            Solventes
                                        </div>
                                        <div class="summary-value">
                                            <?= $clientesSolventes ?>
                                        </div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator" style="background-color: #dc3545;"></span>
                                            Morosos
                                        </div>
                                        <div class="summary-value">
                                            <?= $clientesMorosos ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Sección de Análisis de Facturas -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Análisis de Cobros de Facturas</h3>
                
                <!-- Gráfico de Pastel -->
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">Estado de Cobros: Total Cobrado vs Saldo Pendiente</h5>
                            </div>
                            <div class="analytics-card-body">
                                <div class="chart-container">
                                    <canvas id="facturasPieChart"></canvas>
                                </div>
                                
                                <div class="analytics-summary">
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator summary-indicator-closed"></span>
                                            Total Cobrado (Cerradas)
                                        </div>
                                        <div class="summary-value">
                                            $<?= number_format($cobrosCerradas, 2) ?>
                                        </div>
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $cobrosCerradasVes = $cobrosCerradas * $precioParalelo->precio_ves;
                                            $cobrosCerradasUsdOficial = $cobrosCerradasVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="analytics-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($cobrosCerradasVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($cobrosCerradasUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator summary-indicator-open"></span>
                                            Saldo Pendiente (Abiertas)
                                        </div>
                                        <div class="summary-value">
                                            $<?= number_format($cobrosAbiertas, 2) ?>
                                        </div>
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $cobrosAbiertasVes = $cobrosAbiertas * $precioParalelo->precio_ves;
                                            $cobrosAbiertasUsdOficial = $cobrosAbiertasVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="analytics-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($cobrosAbiertasVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($cobrosAbiertasUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Histórico de Cobros -->
                <h4 class="mb-4">Histórico de Cobros</h4>
                
                <?php if (empty($cobrosParaMostrar)): ?>
                    <div class="no-products">
                        <i class="bi bi-receipt"></i>
                        <h4>No hay registros de cobros</h4>
                        <p>¡Los cobros aparecerán aquí cuando se registren!</p>
                    </div>
                <?php else: ?>
                    <div class="cobros-grid">
                        <?php foreach ($cobrosParaMostrar as $item): ?>
                            <?php if ($item['tipo'] === 'agrupado'): ?>
                                <!-- Tarjeta para factura cerrada (cobros agrupados) -->
                                <div class="cobro-card cobro-card-cerrada">
                                    <div class="cobro-card-header">
                                        <div class="cobro-card-status">
                                            <span class="status-badge status-closed">
                                                <i class="bi bi-check-circle"></i> Cerrada
                                            </span>
                                        </div>
                                        <div class="cobro-card-date">
                                            <?= date('d/m/Y', strtotime($item['factura']->fecha)) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="cobro-card-body">
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-person me-2"></i>
                                                Cliente:
                                            </span>
                                            <span class="cobro-card-value">
                                                <?= Html::encode($item['cliente'] ? $item['cliente']->nombre : 'N/A') ?>
                                            </span>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-receipt me-2"></i>
                                                Factura:
                                            </span>
                                            <span class="cobro-card-value">
                                                <?= Html::encode($item['factura']->codigo) ?>
                                            </span>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-collection me-2"></i>
                                                Total Cobros:
                                            </span>
                                            <span class="cobro-card-value">
                                                <?= count($item['cobros']) ?> pagos
                                            </span>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-cash me-2"></i>
                                                Monto Total:
                                            </span>
                                            <div>
                                                <span class="cobro-card-value amount-positive">
                                                    $<?= number_format($item['totalCobrado'], 2) ?>
                                                </span>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $totalCobradoVes = $item['totalCobrado'] * $precioParalelo->precio_ves;
                                                    $totalCobradoUsdOficial = $totalCobradoVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="cobro-conversions">
                                                        <span class="conversion-line">Bs. <?= number_format($totalCobradoVes, 2, ',', '.') ?></span>
                                                        <span class="conversion-line"><strong>$<?= number_format($totalCobradoUsdOficial, 2) ?></strong> (BCV)</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Detalle expandible de los cobros -->
                                        <div class="cobros-detalle">
                                            <button class="btn-detalle" onclick="toggleDetalle(this)">
                                                <i class="bi bi-chevron-down"></i>
                                                Ver detalle de pagos
                                            </button>
                                            <div class="detalle-content" style="display: none;">
                                                <?php foreach ($item['cobros'] as $index => $cobroDetalle): ?>
                                                    <div class="detalle-row">
                                                        <span class="detalle-fecha">
                                                            <?= date('d/m/Y', strtotime($cobroDetalle->fecha)) ?>
                                                        </span>
                                                        <div>
                                                            <span class="detalle-monto">
                                                                $<?= number_format($cobroDetalle->monto, 2) ?>
                                                            </span>
                                                            <?php if ($precioParalelo && $precioOficial): ?>
                                                                <?php 
                                                                $cobroDetalleVes = $cobroDetalle->monto * $precioParalelo->precio_ves;
                                                                $cobroDetalleUsdOficial = $cobroDetalleVes / $precioOficial->precio_ves;
                                                                ?>
                                                                <div class="cobro-conversions" style="font-size: 0.6rem; margin-top: 2px;">
                                                                    <span class="conversion-line">Bs. <?= number_format($cobroDetalleVes, 2, ',', '.') ?></span>
                                                                    <span class="conversion-line"><strong>$<?= number_format($cobroDetalleUsdOficial, 2) ?></strong> (BCV)</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($cobroDetalle->metodo_pago): ?>
                                                            <span class="detalle-metodo">
                                                                <?= Html::encode($cobroDetalle->metodo_pago) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Tarjeta para cobro individual (factura abierta) -->
                                <div class="cobro-card">
                                    <div class="cobro-card-header">
                                        <div class="cobro-card-status">
                                            <?php if ($item['esCerrada']): ?>
                                                <span class="status-badge status-closed">
                                                    <i class="bi bi-check-circle"></i> Cerrada
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-open">
                                                    <i class="bi bi-clock"></i> Abierta
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="cobro-card-date">
                                            <?= $item['cobro']->factura ? date('d/m/Y', strtotime($item['cobro']->factura->fecha)) : 'N/A' ?>
                                        </div>
                                    </div>
                                    
                                    <div class="cobro-card-body">
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-person me-2"></i>
                                                Cliente:
                                            </span>
                                            <span class="cobro-card-value">
                                                <?= Html::encode($item['cobro']->cliente ? $item['cobro']->cliente->nombre : 'N/A') ?>
                                            </span>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-receipt me-2"></i>
                                                Factura:
                                            </span>
                                            <span class="cobro-card-value">
                                                <?= Html::encode($item['cobro']->factura ? $item['cobro']->factura->codigo : 'N/A') ?>
                                            </span>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-cash me-2"></i>
                                                Monto Pagado:
                                            </span>
                                            <div>
                                                <span class="cobro-card-value amount-positive">
                                                    $<?= number_format($item['cobro']->monto, 2) ?>
                                                </span>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $cobroMontoVes = $item['cobro']->monto * $precioParalelo->precio_ves;
                                                    $cobroMontoUsdOficial = $cobroMontoVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="cobro-conversions">
                                                        <span class="conversion-line">Bs. <?= number_format($cobroMontoVes, 2, ',', '.') ?></span>
                                                        <span class="conversion-line"><strong>$<?= number_format($cobroMontoUsdOficial, 2) ?></strong> (BCV)</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="cobro-card-row">
                                            <span class="cobro-card-label">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Monto Restante:
                                            </span>
                                            <div>
                                                <span class="cobro-card-value <?= $item['montoRestante'] > 0 ? 'amount-pending' : 'amount-positive' ?>">
                                                    $<?= number_format($item['montoRestante'], 2) ?>
                                                </span>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $montoRestanteVes = $item['montoRestante'] * $precioParalelo->precio_ves;
                                                    $montoRestanteUsdOficial = $montoRestanteVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="cobro-conversions">
                                                        <span class="conversion-line">Bs. <?= number_format($montoRestanteVes, 2, ',', '.') ?></span>
                                                        <span class="conversion-line"><strong>$<?= number_format($montoRestanteUsdOficial, 2) ?></strong> (BCV)</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Sección de Estado Financiero -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Estado Financiero</h3>
                
                <!-- Contenedor con tres columnas de valores -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">
                                    <i class="bi bi-box-seam me-2"></i>
                                    Valor de Inventario
                                </h5>
                            </div>
                            <div class="analytics-card-body text-center">
                                <div class="summary-value" style="font-size: 2.5rem; color: #007bff;">
                                    $<?= number_format($valorInventario, 2) ?>
                                </div>
                                <?php if ($precioParalelo && $precioOficial): ?>
                                    <?php 
                                    $valorInventarioVes = $valorInventario * $precioParalelo->precio_ves;
                                    $valorInventarioUsdOficial = $valorInventarioVes / $precioOficial->precio_ves;
                                    ?>
                                    <div class="analytics-conversions">
                                        <span class="conversion-line">Bs. <?= number_format($valorInventarioVes, 2, ',', '.') ?></span>
                                        <span class="conversion-line"><strong>$<?= number_format($valorInventarioUsdOficial, 2) ?></strong> (BCV)</span>
                                    </div>
                                <?php endif; ?>
                                <p class="text-muted mt-2">Inversión total acumulada en todos los inventarios cerrados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">
                                    <i class="bi bi-cash-stack me-2"></i>
                                    Valor Recaudado
                                </h5>
                            </div>
                            <div class="analytics-card-body text-center">
                                <div class="summary-value" style="font-size: 2.5rem; color: #28a745;">
                                    $<?= number_format($valorRecaudado, 2) ?>
                                </div>
                                <?php if ($precioParalelo && $precioOficial): ?>
                                    <?php 
                                    $valorRecaudadoVes = $valorRecaudado * $precioParalelo->precio_ves;
                                    $valorRecaudadoUsdOficial = $valorRecaudadoVes / $precioOficial->precio_ves;
                                    ?>
                                    <div class="analytics-conversions">
                                        <span class="conversion-line">Bs. <?= number_format($valorRecaudadoVes, 2, ',', '.') ?></span>
                                        <span class="conversion-line"><strong>$<?= number_format($valorRecaudadoUsdOficial, 2) ?></strong> (BCV)</span>
                                    </div>
                                <?php endif; ?>
                                <p class="text-muted mt-2">Total de cobros realizados</p>
                            </div>
                        </div>
                    </div>
                    <?php 
                    // Calcular el diferencial solo si el valor recaudado supera al valor de inventario
                    $diferencial = $valorRecaudado - $valorInventario;
                    if ($diferencial > 0): 
                    ?>
                    <div class="col-md-4">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">
                                    <i class="bi bi-graph-up-arrow me-2"></i>
                                    Diferencial Positivo
                                </h5>
                            </div>
                            <div class="analytics-card-body text-center">
                                <div class="summary-value" style="font-size: 2.5rem; color: #17a2b8;">
                                    $<?= number_format($diferencial, 2) ?>
                                </div>
                                <?php if ($precioParalelo && $precioOficial): ?>
                                    <?php 
                                    $diferencialVes = $diferencial * $precioParalelo->precio_ves;
                                    $diferencialUsdOficial = $diferencialVes / $precioOficial->precio_ves;
                                    ?>
                                    <div class="analytics-conversions">
                                        <span class="conversion-line">Bs. <?= number_format($diferencialVes, 2, ',', '.') ?></span>
                                        <span class="conversion-line"><strong>$<?= number_format($diferencialUsdOficial, 2) ?></strong> (BCV)</span>
                                    </div>
                                <?php endif; ?>
                                <p class="text-muted mt-2">Exceso de recaudación sobre inventario</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Gráficas -->
                <div class="row">
                    <!-- Gráfica de Barras -->
                    <div class="col-md-6">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">Comparativa Financiera</h5>
                            </div>
                            <div class="analytics-card-body">
                                <div class="chart-container">
                                    <canvas id="financialBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfica de Tarta -->
                    <div class="col-md-6">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <h5 class="analytics-card-title">Proporción: Inventario vs Recaudado</h5>
                            </div>
                            <div class="analytics-card-body">
                                <div class="chart-container">
                                    <canvas id="deudaRecaudadoChart"></canvas>
                                </div>
                                
                                <div class="analytics-summary">
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator" style="background-color: #ffc107;"></span>
                                            Valor Invertido (Inventario)
                                        </div>
                                        <div class="summary-value">
                                            $<?= number_format($proporcionDeuda, 2) ?>
                                        </div>
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $proporcionDeudaVes = $proporcionDeuda * $precioParalelo->precio_ves;
                                            $proporcionDeudaUsdOficial = $proporcionDeudaVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="analytics-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($proporcionDeudaVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($proporcionDeudaUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">
                                            <span class="summary-indicator" style="background-color: #28a745;"></span>
                                            Recaudado
                                        </div>
                                        <div class="summary-value">
                                            $<?= number_format($proporcionRecaudado, 2) ?>
                                        </div>
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $proporcionRecaudadoVes = $proporcionRecaudado * $precioParalelo->precio_ves;
                                            $proporcionRecaudadoUsdOficial = $proporcionRecaudadoVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="analytics-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($proporcionRecaudadoVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($proporcionRecaudadoUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Registrar Chart.js desde CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js', [
    'position' => \yii\web\View::POS_HEAD
]);

// Datos para el gráfico
$totalCobros = $cobrosCerradas + $cobrosAbiertas;

// Preparar datos seguros para JavaScript
$cobrosCerradasJs = (float)$cobrosCerradas;
$cobrosAbiertasJs = (float)$cobrosAbiertas;
$totalCobrosJs = (float)$totalCobros;

$js = "
(function() {
    // Esperar a que Chart.js se cargue
    function initChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initChart, 100);
            return;
        }
        
        const ctx = document.getElementById('facturasPieChart');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }
        
        const chartData = {
            labels: ['Total Cobrado (Cerradas)', 'Saldo Pendiente (Abiertas)'],
            datasets: [{
                data: [" . $cobrosCerradasJs . ", " . $cobrosAbiertasJs . "],
                backgroundColor: [
                    '#28a745',
                    '#ffc107'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalCobrosJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': $' + value.toLocaleString('es-ES', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
})();
";

$this->registerJs($js, \yii\web\View::POS_END);

// JavaScript para el gráfico de Clientes (Morosos vs Solventes)
$clientesSolventesJs = (int)$clientesSolventes;
$clientesMorososJs = (int)$clientesMorosos;
$totalClientesJs = $clientesSolventesJs + $clientesMorososJs;

$jsClientes = "
(function() {
    function initClientesChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initClientesChart, 100);
            return;
        }
        
        const ctx = document.getElementById('clientesStatusChart');
        if (!ctx) {
            console.error('Canvas element clientesStatusChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Solventes', 'Morosos'],
            datasets: [{
                data: [" . $clientesSolventesJs . ", " . $clientesMorososJs . "],
                backgroundColor: [
                    '#28a745',
                    '#dc3545'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalClientesJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': ' + value + ' clientes (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating clientesStatusChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientesChart);
    } else {
        initClientesChart();
    }
})();
";

$this->registerJs($jsClientes, \yii\web\View::POS_END);

// JavaScript para gráfico de barras (Valor Inventario vs Valor Recaudado)
$valorInventarioJs = (float)$valorInventario;
$valorRecaudadoJs = (float)$valorRecaudado;

$jsBarChart = "
(function() {
    function initBarChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initBarChart, 100);
            return;
        }
        
        const ctx = document.getElementById('financialBarChart');
        if (!ctx) {
            console.error('Canvas element financialBarChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Valor de Inventario', 'Valor Recaudado'],
            datasets: [{
                label: 'Monto en \$',
                data: [" . $valorInventarioJs . ", " . $valorRecaudadoJs . "],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(40, 167, 69, 0.7)'
                ],
                borderColor: [
                    '#007bff',
                    '#28a745'
                ],
                borderWidth: 2
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            return '\$' + value.toLocaleString('es-ES', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '\$' + value.toLocaleString('es-ES');
                        }
                    }
                }
            },
            animation: {
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating financialBarChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBarChart);
    } else {
        initBarChart();
    }
})();
";

$this->registerJs($jsBarChart, \yii\web\View::POS_END);

// JavaScript para gráfico de tarta (Deuda vs Recaudado)
$proporcionDeudaJs = (float)$proporcionDeuda;
$proporcionRecaudadoJs = (float)$proporcionRecaudado;
$totalProporcionJs = $proporcionDeudaJs + $proporcionRecaudadoJs;

$jsPieChart = "
(function() {
    function initDeudaPieChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initDeudaPieChart, 100);
            return;
        }
        
        const ctx = document.getElementById('deudaRecaudadoChart');
        if (!ctx) {
            console.error('Canvas element deudaRecaudadoChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Valor Invertido (Inventario)', 'Recaudado'],
            datasets: [{
                data: [" . $proporcionDeudaJs . ", " . $proporcionRecaudadoJs . "],
                backgroundColor: [
                    '#ffc107',
                    '#28a745'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalProporcionJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': \$' + value.toLocaleString('es-ES', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating deudaRecaudadoChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeudaPieChart);
    } else {
        initDeudaPieChart();
    }
})();
";

$this->registerJs($jsPieChart, \yii\web\View::POS_END);

// JavaScript para expandir/contraer detalles de cobros agrupados
$jsDetalle = "
function toggleDetalle(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        button.classList.add('active');
        button.innerHTML = '<i class=\"bi bi-chevron-up\"></i> Ocultar detalle de pagos';
    } else {
        content.style.display = 'none';
        button.classList.remove('active');
        button.innerHTML = '<i class=\"bi bi-chevron-down\"></i> Ver detalle de pagos';
    }
}
";

$this->registerJs($jsDetalle, \yii\web\View::POS_END);
?>
