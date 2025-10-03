<?php

use app\models\Productos;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProductosSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = Yii::t('app', 'Productos');

// Registrar CSS personalizado para las tarjetas de productos
$this->registerCss('
.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 380px;
    display: flex;
    flex-direction: column;
    border: 1px solid #e9ecef;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    text-decoration: none;
    color: inherit;
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
    font-size: 1.1rem;
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

.product-card-details {
    margin-bottom: 15px;
}

.product-card-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.85rem;
}

.product-card-label {
    font-weight: 500;
    color: #6c757d;
}

.product-card-value {
    font-weight: 600;
    color: #333;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 120px;
}

.product-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.product-card-price {
    font-size: 1.2rem;
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

.product-card-category {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}


.products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-top: 30px;
}

.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-products i {
    font-size: 4rem;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .product-card {
        height: auto;
        min-height: 350px;
    }
    
    .product-card-detail {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .product-card-value {
        text-align: left;
        max-width: 100%;
    }
}

.search-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.search-bar-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.search-input-modern {
    border: 2px solid #e9ecef;
    border-radius: 25px;
    padding: 12px 20px;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.search-input-modern:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

.search-input-modern::placeholder {
    color: #adb5bd;
}

.clear-search-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: #6c757d;
    border: none;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
}

.clear-search-btn:hover {
    background: #495057;
}

.clear-search-btn.show {
    display: flex;
}

.search-info {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 8px;
    font-style: italic;
}

.filters-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.filters-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    width: 100%;
    background-color: #f8f9fa;
}

.filter-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
    background-color: white;
}

.filter-input-date {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    width: 100%;
    background-color: #f8f9fa;
}

.filter-input-date:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
    background-color: white;
}

.filter-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 6px;
    display: block;
}

.filters-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-clear-filters {
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-clear-filters:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-apply-filters {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-apply-filters:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}
');
?>
<div class="productos-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-5">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-arrow-left"></i> Volver al desglose de inventario', Url::to(['site/index']), [
                    'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
            <div class="text-start mt-3">
                <button type="button" class="btn btn-outline-success btn-sm fw-bold w-100" id="btn-cierre-inventario" style="background-color: #f8fff8; border-color: #c3e6cb; color: #155724; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <i class="bi bi-cash-coin"></i> Registrar Cierre de Inventario
                </button>
            </div>
            <div class="text-start mt-2">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Añadir Productos'), ['create'], [
                    'class' => 'btn btn-outline-success btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8fff8; border-color: #c3e6cb; color: #155724; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
            <div class="text-start mt-2">
                <button type="button" class="btn btn-outline-info btn-sm fw-bold w-100" id="btn-generar-reporte-modal" style="background-color: #f0f8ff; border-color: #b3d9ff; color: #0c5460; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <i class="bi bi-file-earmark-text"></i> Generar Reporte
                </button>
            </div>
        </div>

        <!-- Barra de búsqueda moderna -->
        <div class="search-bar-container">
            <div class="position-relative">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="search-input-modern" 
                    placeholder="🔍 Buscar productos por marca, modelo, descripción, color, SKU o código..."
                    value="<?= Html::encode(Yii::$app->request->get('search', '')) ?>"
                    autocomplete="off"
                >
                <button type="button" class="clear-search-btn" id="clearSearchBtn">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="search-info">
                <i class="bi bi-info-circle"></i> La búsqueda se actualiza automáticamente mientras escribes
            </div>
        </div>

        <!-- Filtros avanzados -->
        <div class="filters-container">
            <div class="filters-title">
                <i class="bi bi-funnel"></i>
                Filtros Avanzados
            </div>
            
            <form id="filtersForm" method="get" action="">
                <!-- Parámetro de ruta para Yii2 -->
                <input type="hidden" name="r" value="productos/index">
                
                <!-- Conservar el término de búsqueda si existe -->
                <?php if (!empty(Yii::$app->request->get('search'))): ?>
                    <input type="hidden" name="search" value="<?= Html::encode(Yii::$app->request->get('search')) ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <!-- Dropdowns en una fila -->
                    <div class="col-md-3">
                        <label class="filter-label">
                            <i class="bi bi-palette"></i> Color
                        </label>
                        <select name="ProductosSearch[color]" id="filter-color" class="filter-select">
                            <option value="">Todos los colores</option>
                            <?php foreach ($colores as $color): ?>
                                <option value="<?= Html::encode($color) ?>" 
                                    <?= $searchModel->color == $color ? 'selected' : '' ?>>
                                    <?= Html::encode($color) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label">
                            <i class="bi bi-rulers"></i> Unidad de Medida
                        </label>
                        <select name="ProductosSearch[unidad_medida]" id="filter-unidad-medida" class="filter-select">
                            <option value="">Todas las unidades</option>
                            <?php foreach ($unidadesMedida as $unidad): ?>
                                <option value="<?= Html::encode($unidad) ?>" 
                                    <?= $searchModel->unidad_medida == $unidad ? 'selected' : '' ?>>
                                    <?= Html::encode($unidad) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label">
                            <i class="bi bi-tags"></i> Categoría
                        </label>
                        <select name="ProductosSearch[id_categoria]" id="filter-categoria" class="filter-select">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $id => $titulo): ?>
                                <option value="<?= $id ?>" 
                                    <?= $searchModel->id_categoria == $id ? 'selected' : '' ?>>
                                    <?= Html::encode($titulo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label">
                            <i class="bi bi-geo-alt"></i> Lugar/Almacén
                        </label>
                        <select name="ProductosSearch[id_lugar]" id="filter-lugar" class="filter-select">
                            <option value="">Todos los lugares</option>
                            <?php foreach ($lugares as $id => $nombre): ?>
                                <option value="<?= $id ?>" 
                                    <?= $searchModel->id_lugar == $id ? 'selected' : '' ?>>
                                    <?= Html::encode($nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Filtros de fecha -->
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="filter-label">
                            <i class="bi bi-calendar-range"></i> Productos registrados del:
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input 
                            type="date" 
                            name="ProductosSearch[fecha_inicio]" 
                            id="filter-fecha-inicio" 
                            class="filter-input-date"
                            value="<?= Html::encode($searchModel->fecha_inicio) ?>"
                            placeholder="Fecha de inicio"
                        >
                    </div>
                    <div class="col-md-6">
                        <input 
                            type="date" 
                            name="ProductosSearch[fecha_fin]" 
                            id="filter-fecha-fin" 
                            class="filter-input-date"
                            value="<?= Html::encode($searchModel->fecha_fin) ?>"
                            placeholder="Fecha de fin"
                        >
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="filters-actions">
                    <button type="submit" class="btn-apply-filters">
                        <i class="bi bi-check-circle"></i> Aplicar Filtros
                    </button>
                    <button type="button" class="btn-clear-filters" id="clearFiltersBtn">
                        <i class="bi bi-x-circle"></i> Limpiar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Sección de búsqueda colapsable -->
        <div class="search-section" id="searchSection" style="display: none;">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php 
        $productos = $dataProvider->getModels(); 
        $totalProductos = $dataProvider->getTotalCount();
        $searchTerm = Yii::$app->request->get('search', '');
        ?>
        
        <div class="mb-3">
            <?php 
            // Verificar si hay filtros activos
            $filtrosActivos = [];
            if (!empty($searchTerm)) {
                $filtrosActivos[] = '<strong>Búsqueda:</strong> "' . Html::encode($searchTerm) . '"';
            }
            if (!empty($searchModel->color)) {
                $filtrosActivos[] = '<strong>Color:</strong> ' . Html::encode($searchModel->color);
            }
            if (!empty($searchModel->unidad_medida)) {
                $filtrosActivos[] = '<strong>Unidad:</strong> ' . Html::encode($searchModel->unidad_medida);
            }
            if (!empty($searchModel->id_categoria)) {
                $categoriaActiva = isset($categorias[$searchModel->id_categoria]) ? $categorias[$searchModel->id_categoria] : '';
                $filtrosActivos[] = '<strong>Categoría:</strong> ' . Html::encode($categoriaActiva);
            }
            if (!empty($searchModel->id_lugar)) {
                $lugarActivo = isset($lugares[$searchModel->id_lugar]) ? $lugares[$searchModel->id_lugar] : '';
                $filtrosActivos[] = '<strong>Lugar:</strong> ' . Html::encode($lugarActivo);
            }
            if (!empty($searchModel->fecha_inicio)) {
                $filtrosActivos[] = '<strong>Desde:</strong> ' . Html::encode($searchModel->fecha_inicio);
            }
            if (!empty($searchModel->fecha_fin)) {
                $filtrosActivos[] = '<strong>Hasta:</strong> ' . Html::encode($searchModel->fecha_fin);
            }
            ?>
            
            <?php if (!empty($filtrosActivos)): ?>
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bi bi-funnel-fill me-2 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="mb-1">
                            <strong>Filtros activos:</strong>
                            <span class="badge bg-primary ms-2"><?= $totalProductos ?> resultado<?= $totalProductos !== 1 ? 's' : '' ?></span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($filtrosActivos as $filtro): ?>
                                <span class="badge bg-light text-dark border" style="font-weight: normal;">
                                    <?= $filtro ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <small class="text-muted">
                    Mostrando <?= count($productos) ?> de <?= $totalProductos ?> productos
                </small>
            <?php endif; ?>
        </div>

        <?php if (empty($productos)): ?>
            <div class="products-grid">
                <div class="no-products">
                    <?php 
                    $hayFiltros = !empty($searchTerm) || !empty($searchModel->color) || 
                                  !empty($searchModel->unidad_medida) || !empty($searchModel->id_categoria) ||
                                  !empty($searchModel->id_lugar) || !empty($searchModel->fecha_inicio) || 
                                  !empty($searchModel->fecha_fin);
                    ?>
                    
                    <?php if ($hayFiltros): ?>
                        <i class="bi bi-search"></i>
                        <h3>No se encontraron productos</h3>
                        <p>No encontramos productos que coincidan con los filtros aplicados.</p>
                        <p class="text-muted">Intenta ajustar o limpiar los filtros para ver más resultados.</p>
                    <?php else: ?>
                        <i class="bi bi-box-seam"></i>
                        <h3>No hay productos disponibles</h3>
                        <p>¡Agrega productos al inventario para mostrarlos aquí!</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($productos as $producto): ?>
                    <?= Html::beginTag('a', [
                        'href' => Url::to(['view', 'id' => $producto->id]),
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
                                $producto->color
                            ]);
                            $titulo = implode(' - ', $titulo_partes);
                            if (empty($titulo)) {
                                $titulo = 'Producto #' . $producto->id;
                            }
                            ?>
                            
                            <h3 class="product-card-title" title="<?= Html::encode($titulo) ?>">
                                <?= Html::encode($titulo) ?>
                            </h3>
                            
                            <div class="product-card-details">
                                <?php if (!empty($producto->descripcion)): ?>
                                    <div class="product-card-detail">
                                        <span class="product-card-label">Descripción:</span>
                                        <span class="product-card-value" title="<?= Html::encode($producto->descripcion) ?>">
                                            <?= Html::encode(substr($producto->descripcion, 0, 20) . (strlen($producto->descripcion) > 20 ? '...' : '')) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($producto->sku)): ?>
                                    <div class="product-card-detail">
                                        <span class="product-card-label">SKU:</span>
                                        <span class="product-card-value"><?= Html::encode($producto->sku) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($producto->codigo_barra)): ?>
                                    <div class="product-card-detail">
                                        <span class="product-card-label">Código:</span>
                                        <span class="product-card-value" title="<?= Html::encode($producto->codigo_barra) ?>">
                                            <?= Html::encode(substr($producto->codigo_barra, 0, 15) . (strlen($producto->codigo_barra) > 15 ? '...' : '')) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-card-detail">
                                    <span class="product-card-label">Costo:</span>
                                    <span class="product-card-value">$<?= number_format($producto->costo, 2) ?></span>
                                </div>
                            </div>
                            
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
                                    
                                    <?php 
                                    // Calcular stock total del producto
                                    $stockTotal = 0;
                                    if ($producto->stocks) {
                                        foreach ($producto->stocks as $stock) {
                                            $stockTotal += $stock->cantidad;
                                        }
                                    }
                                    
                                    // Determinar color según la cantidad
                                    $stockColor = '';
                                    if ($stockTotal <= 0) {
                                        $stockColor = 'text-danger'; // Rojo
                                    } elseif ($stockTotal < 5) {
                                        $stockColor = 'text-warning'; // Dorado/Amarillo
                                    } else {
                                        $stockColor = 'text-success'; // Verde
                                    }
                                    ?>
                                    <br>
                                    <small class="<?= $stockColor ?>">
                                        Stock: <?= $stockTotal ?> unidades
                                    </small>
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
        <?php endif; ?>
        
        <!-- Paginación -->
        <?php if ($dataProvider->pagination !== false): ?>
            <div class="mt-4 d-flex justify-content-center">
                <?= \yii\widgets\LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkOptions' => ['class' => 'page-link'],
                    'pageCssClass' => 'page-item',
                    'prevPageCssClass' => 'page-item',
                    'nextPageCssClass' => 'page-item',
                    'firstPageCssClass' => 'page-item',
                    'lastPageCssClass' => 'page-item',
                    'disabledPageCssClass' => 'page-item disabled',
                    'activePageCssClass' => 'page-item active',
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// JavaScript para búsqueda en tiempo real y modal de cierre de inventario
$js = "
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');

// Función para realizar la búsqueda
function performSearch(searchTerm) {
    const currentUrl = new URL(window.location.href);
    
    // Asegurar que siempre incluya el parámetro r
    currentUrl.searchParams.set('r', 'productos/index');
    
    if (searchTerm && searchTerm.trim() !== '') {
        currentUrl.searchParams.set('search', searchTerm.trim());
    } else {
        currentUrl.searchParams.delete('search');
    }
    
    // Mantener la página actual si existe
    if (!currentUrl.searchParams.has('page')) {
        currentUrl.searchParams.delete('page');
    }
    
    window.location.href = currentUrl.toString();
}

// Evento de input para búsqueda en tiempo real
searchInput.addEventListener('input', function(e) {
    const searchTerm = e.target.value;
    
    // Mostrar/ocultar botón de limpiar
    if (searchTerm && searchTerm.trim() !== '') {
        clearSearchBtn.classList.add('show');
    } else {
        clearSearchBtn.classList.remove('show');
    }
    
    // Limpiar el timeout anterior
    clearTimeout(searchTimeout);
    
    // Establecer un nuevo timeout para búsqueda (500ms después de dejar de escribir)
    searchTimeout = setTimeout(function() {
        performSearch(searchTerm);
    }, 500);
});

// Evento para limpiar búsqueda
clearSearchBtn.addEventListener('click', function() {
    searchInput.value = '';
    clearSearchBtn.classList.remove('show');
    performSearch('');
});

// Mostrar botón de limpiar si ya hay un término de búsqueda
if (searchInput.value && searchInput.value.trim() !== '') {
    clearSearchBtn.classList.add('show');
}

// Permitir búsqueda al presionar Enter
searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        performSearch(e.target.value);
    }
});

// Manejar botón de limpiar filtros
document.getElementById('clearFiltersBtn').addEventListener('click', function() {
    // Limpiar todos los selects y inputs de fecha
    document.getElementById('filter-color').value = '';
    document.getElementById('filter-unidad-medida').value = '';
    document.getElementById('filter-categoria').value = '';
    document.getElementById('filter-lugar').value = '';
    document.getElementById('filter-fecha-inicio').value = '';
    document.getElementById('filter-fecha-fin').value = '';
    
    // Redirigir a la página sin filtros, conservando solo la búsqueda si existe
    const currentUrl = new URL(window.location.href);
    const searchParam = currentUrl.searchParams.get('search');
    
    let newUrl = currentUrl.origin + currentUrl.pathname + '?r=productos/index';
    if (searchParam) {
        newUrl += '&search=' + encodeURIComponent(searchParam);
    }
    
    window.location.href = newUrl;
});

function toggleSearch() {
    const searchSection = document.getElementById('searchSection');
    const toggleButton = document.getElementById('searchToggle');
    
    if (searchSection.style.display === 'none') {
        searchSection.style.display = 'block';
        toggleButton.innerHTML = '<i class=\"bi bi-x\"></i> Ocultar Búsqueda';
    } else {
        searchSection.style.display = 'none';
        toggleButton.innerHTML = '<i class=\"bi bi-search\"></i> Buscar Productos';
    }
}

// Manejar clic en botón de cierre de inventario
document.getElementById('btn-cierre-inventario').addEventListener('click', function(e) {
    e.preventDefault();
    
    const modalElement = document.getElementById('modalCierreInventario');
    
    if (!modalElement) {
        alert('Error: El modal no existe. Por favor, recargue la página.');
        return;
    }
    
    if (typeof bootstrap === 'undefined') {
        alert('Error: Bootstrap 5 no está cargado. Por favor, recargue la página.');
        return;
    }
    
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } catch (error) {
        console.error('Error al abrir modal:', error);
        alert('Error al abrir el modal: ' + error.message);
    }
});

// Manejar clic en botón de generar reporte
document.getElementById('btn-generar-reporte-modal').addEventListener('click', function(e) {
    e.preventDefault();
    
    const modalElement = document.getElementById('modalGenerarReporte');
    
    if (!modalElement) {
        alert('Error: El modal no existe. Por favor, recargue la página.');
        return;
    }
    
    if (typeof bootstrap === 'undefined') {
        alert('Error: Bootstrap 5 no está cargado. Por favor, recargue la página.');
        return;
    }
    
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } catch (error) {
        console.error('Error al abrir modal:', error);
        alert('Error al abrir el modal: ' + error.message);
    }
});
";

$this->registerJs($js, \yii\web\View::POS_END);
?>
