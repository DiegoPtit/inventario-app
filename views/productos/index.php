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
        </div>

        <!-- Sección de búsqueda colapsable -->
        <div class="search-section" id="searchSection" style="display: none;">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php 
        $productos = $dataProvider->getModels(); 
        $totalProductos = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($productos) ?> de <?= $totalProductos ?> productos
            </small>
        </div>

        <?php if (empty($productos)): ?>
            <div class="products-grid">
                <div class="no-products">
                    <i class="bi bi-box-seam"></i>
                    <h3>No hay productos disponibles</h3>
                    <p>¡Agrega productos al inventario para mostrarlos aquí!</p>
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
// JavaScript para toggle de búsqueda y modal de cierre de inventario
$js = "
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
";

$this->registerJs($js, \yii\web\View::POS_END);
?>
