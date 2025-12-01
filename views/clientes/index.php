<?php

use app\models\Clientes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var app\models\ClientesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Clientes');

// Registrar CSS personalizado para las tarjetas de clientes
$this->registerCss('
.client-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
    border: 1px solid #e9ecef;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    height: 100%;
    padding: 15px;
}

.client-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
    border-color: #007bff;
}

.client-card-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.client-name {
    font-size: 0.9rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 2px;
}

.client-document {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
}

.client-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #f1f1f1;
}

.client-phone {
    font-size: 0.75rem;
    color: #adb5bd;
    font-family: monospace;
}

.client-status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
}

.status-solvente { background-color: #d4edda; color: #155724; }
.status-moroso { background-color: #f8d7da; color: #721c24; }

.client-location-badge {
    font-size: 0.7rem;
    background: #f8f9fa;
    color: #6c757d;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.clients-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
    margin-top: 20px;
}

.no-clients {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-clients i {
    font-size: 4rem;
    margin-bottom: 20px;
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

.btn-search {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 12px 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 55px;
}

.btn-search:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.clear-search-btn-new {
    background: #6c757d;
    border: none;
    color: white;
    border-radius: 10px;
    padding: 12px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 50px;
}

.clear-search-btn-new:hover {
    background: #495057;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
}

.filters-chevron {
    font-size: 1.2rem;
    color: #6c757d;
    transition: transform 0.3s ease;
}

.filters-chevron.rotated {
    transform: rotate(180deg);
}

.filters-body {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.active-filters-container {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px 20px;
}

.active-filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.active-filters-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.active-filters-count {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
}

.active-filters-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-chip {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.filter-chip:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

.filter-chip-label {
    font-weight: 600;
    color: #6c757d;
}

.filter-chip-value {
    color: #495057;
}
');
?>
<div class="clientes-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-5">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-arrow-left"></i> Volver al desglose principal', Url::to(['site/index']), [
                    'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
            <div class="text-start mt-2">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Registrar Cliente'), ['create'], [
                    'class' => 'btn btn-outline-success btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8fff8; border-color: #c3e6cb; color: #155724; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
        </div>

        <!-- Barra de búsqueda moderna -->
        <div class="search-bar-container">
            <form id="searchForm" method="get" action="">
                <input type="hidden" name="r" value="clientes/index">
                <div class="position-relative d-flex gap-2">
                    <input 
                        type="text" 
                        id="searchInput" 
                        name="search"
                        class="search-input-modern" 
                        placeholder="Buscar por nombre, documento, ubicación o teléfono..."
                        value="<?= Html::encode(Yii::$app->request->get('search', '')) ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn-search" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                    <button type="button" class="clear-search-btn-new" id="clearSearchBtn" style="<?= empty(Yii::$app->request->get('search', '')) ? 'display: none;' : '' ?>">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Filtros avanzados colapsables -->
        <div class="filters-container">
            <div class="filters-header" id="filtersHeader" style="cursor: pointer;">
                <div class="filters-title">
                    <i class="bi bi-funnel"></i>
                    Filtros Avanzados
                </div>
                <i class="bi bi-chevron-down filters-chevron" id="filtersChevron"></i>
            </div>
            
            <div class="filters-body" id="filtersBody" style="display: none;">
                <form id="filtersForm" method="get" action="">
                    <!-- Parámetro de ruta para Yii2 -->
                    <input type="hidden" name="r" value="clientes/index">
                    
                    <!-- Conservar el término de búsqueda si existe -->
                    <?php if (!empty(Yii::$app->request->get('search'))): ?>
                        <input type="hidden" name="search" value="<?= Html::encode(Yii::$app->request->get('search')) ?>">
                    <?php endif; ?>
                    
                    <div class="row g-3">
                        <!-- Dropdowns en una fila -->
                        <div class="col-md-4">
                            <label class="filter-label">
                                <i class="bi bi-shield-check"></i> Status
                            </label>
                            <select name="ClientesSearch[status]" id="filter-status" class="filter-select">
                                <option value="">Todos los status</option>
                                <option value="Solvente" <?= $searchModel->status == 'Solvente' ? 'selected' : '' ?>>Solvente</option>
                                <option value="Moroso" <?= $searchModel->status == 'Moroso' ? 'selected' : '' ?>>Moroso</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">
                                <i class="bi bi-geo-alt"></i> Ubicación
                            </label>
                            <select name="ClientesSearch[ubicacion]" id="filter-ubicacion" class="filter-select">
                                <option value="">Todas las ubicaciones</option>
                                <?php foreach ($ubicaciones as $ubicacion): ?>
                                    <option value="<?= Html::encode($ubicacion) ?>" 
                                        <?= $searchModel->ubicacion == $ubicacion ? 'selected' : '' ?>>
                                        <?= Html::encode($ubicacion) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Espacio vacío para mantener la distribución -->
                        </div>
                    </div>
                    
                    <!-- Filtros de fecha -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label class="filter-label">
                                <i class="bi bi-calendar-range"></i> Clientes registrados del:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input 
                                type="date" 
                                name="ClientesSearch[fecha_inicio]" 
                                id="filter-fecha-inicio" 
                                class="filter-input-date"
                                value="<?= Html::encode($searchModel->fecha_inicio) ?>"
                                placeholder="Fecha de inicio"
                            >
                        </div>
                        <div class="col-md-6">
                            <input 
                                type="date" 
                                name="ClientesSearch[fecha_fin]" 
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
        </div>

        <!-- Sección de búsqueda colapsable -->
        <div class="search-section" id="searchSection" style="display: none;">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php 
        $clientes = $dataProvider->getModels(); 
        $totalClientes = $dataProvider->getTotalCount();
        $searchTerm = Yii::$app->request->get('search', '');
        ?>
        
        <div class="mb-3">
            <?php 
            // Verificar si hay filtros activos
            $filtrosActivos = [];
            if (!empty($searchTerm)) {
                $filtrosActivos[] = [
                    'tipo' => 'Búsqueda',
                    'valor' => $searchTerm
                ];
            }
            if (!empty($searchModel->status)) {
                $filtrosActivos[] = [
                    'tipo' => 'Status',
                    'valor' => $searchModel->status
                ];
            }
            if (!empty($searchModel->ubicacion)) {
                $filtrosActivos[] = [
                    'tipo' => 'Ubicación',
                    'valor' => $searchModel->ubicacion
                ];
            }
            if (!empty($searchModel->fecha_inicio)) {
                $filtrosActivos[] = [
                    'tipo' => 'Desde',
                    'valor' => $searchModel->fecha_inicio
                ];
            }
            if (!empty($searchModel->fecha_fin)) {
                $filtrosActivos[] = [
                    'tipo' => 'Hasta',
                    'valor' => $searchModel->fecha_fin
                ];
            }
            ?>
            
            <?php if (!empty($filtrosActivos)): ?>
                <div class="active-filters-container">
                    <div class="active-filters-header">
                        <span class="active-filters-text">
                            <i class="bi bi-funnel-fill"></i>
                            Filtros activos
                        </span>
                        <span class="active-filters-count"><?= $totalClientes ?> resultado<?= $totalClientes !== 1 ? 's' : '' ?></span>
                    </div>
                    <div class="active-filters-chips">
                        <?php foreach ($filtrosActivos as $filtro): ?>
                            <span class="filter-chip">
                                <span class="filter-chip-label"><?= Html::encode($filtro['tipo']) ?>:</span>
                                <span class="filter-chip-value"><?= Html::encode($filtro['valor']) ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <small class="text-muted">
                    Mostrando <?= count($clientes) ?> de <?= $totalClientes ?> clientes
                </small>
            <?php endif; ?>
        </div>

        <?php if (empty($clientes)): ?>
            <div class="clients-grid">
                <div class="no-clients">
                    <?php 
                    $hayFiltros = !empty($searchTerm) || !empty($searchModel->status) || 
                                  !empty($searchModel->ubicacion) ||
                                  !empty($searchModel->fecha_inicio) || !empty($searchModel->fecha_fin);
                    ?>
                    
                    <?php if ($hayFiltros): ?>
                        <i class="bi bi-search"></i>
                        <h3>No se encontraron clientes</h3>
                        <p>No encontramos clientes que coincidan con los filtros aplicados.</p>
                        <p class="text-muted">Intenta ajustar o limpiar los filtros para ver más resultados.</p>
                    <?php else: ?>
                        <i class="bi bi-person-x"></i>
                        <h3>No hay clientes registrados</h3>
                        <p>¡Registra clientes para mostrarlos aquí!</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="clients-grid">
                <?php foreach ($clientes as $cliente): ?>
                    <?= Html::beginTag('a', [
                        'href' => Url::to(['view', 'id' => $cliente->id]),
                        'class' => 'client-card'
                    ]) ?>
                        <div class="client-card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="client-name">
                                        <?= Html::encode($cliente->nombre) ?>
                                    </div>
                                    <div class="client-document">
                                        <?= Html::encode($cliente->documento_identidad ?: 'Sin documento') ?>
                                    </div>
                                    
                                    <?php if (!empty($cliente->telefono)): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-telephone"></i> <?= Html::encode($cliente->telefono) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($cliente->ubicacion)): ?>
                                    <span class="client-location-badge">
                                        <?= Html::encode($cliente->ubicacion) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="client-meta">
                                <div class="client-phone">
                                    ID: <?= $cliente->id ?>
                                </div>
                                
                                <?php if ($cliente->isStatusSolvente()): ?>
                                    <span class="client-status-badge status-solvente">
                                        Solvente
                                    </span>
                                <?php else: ?>
                                    <span class="client-status-badge status-moroso">
                                        Moroso
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
// JavaScript para búsqueda manual y filtros colapsables
$js = "
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');
const filtersHeader = document.getElementById('filtersHeader');
const filtersBody = document.getElementById('filtersBody');
const filtersChevron = document.getElementById('filtersChevron');

// Manejar toggle de filtros
if (filtersHeader) {
    filtersHeader.addEventListener('click', function() {
        if (filtersBody.style.display === 'none') {
            filtersBody.style.display = 'block';
            filtersChevron.classList.add('rotated');
        } else {
            filtersBody.style.display = 'none';
            filtersChevron.classList.remove('rotated');
        }
    });
}

// Mostrar/ocultar botón de limpiar búsqueda
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value;
        
        if (searchTerm && searchTerm.trim() !== '') {
            clearSearchBtn.style.display = 'flex';
        } else {
            clearSearchBtn.style.display = 'none';
        }
    });
}

// Evento para limpiar búsqueda
if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        
        // Redirigir sin el parámetro de búsqueda
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('search');
        
        // Mantener el parámetro r
        if (!currentUrl.searchParams.has('r')) {
            currentUrl.searchParams.set('r', 'clientes/index');
        }
        
        window.location.href = currentUrl.toString();
    });
}

// Manejar botón de limpiar filtros
const clearFiltersBtn = document.getElementById('clearFiltersBtn');
if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', function() {
        // Limpiar todos los selects y inputs de fecha
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-ubicacion').value = '';
        document.getElementById('filter-fecha-inicio').value = '';
        document.getElementById('filter-fecha-fin').value = '';
        
        // Redirigir a la página sin filtros, conservando solo la búsqueda si existe
        const currentUrl = new URL(window.location.href);
        const searchParam = currentUrl.searchParams.get('search');
        
        let newUrl = currentUrl.origin + currentUrl.pathname + '?r=clientes/index';
        if (searchParam) {
            newUrl += '&search=' + encodeURIComponent(searchParam);
        }
        
        window.location.href = newUrl;
    });
}

function toggleSearch() {
    const searchSection = document.getElementById('searchSection');
    const toggleButton = document.getElementById('searchToggle');
    
    if (searchSection.style.display === 'none') {
        searchSection.style.display = 'block';
        toggleButton.innerHTML = '<i class=\\\"bi bi-x\\\"></i> Ocultar Búsqueda';
    } else {
        searchSection.style.display = 'none';
        toggleButton.innerHTML = '<i class=\\\"bi bi-search\\\"></i> Buscar Clientes';
    }
}
";

$this->registerJs($js, \yii\web\View::POS_END);
?>