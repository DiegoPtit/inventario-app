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
.clients-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-top: 30px;
}

.client-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
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
    text-decoration: none;
    color: inherit;
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
    max-width: 180px;
}

.client-card-status {
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge-solvente {
    background-color: #28a745;
    color: white;
}

.badge-moroso {
    background-color: #dc3545;
    color: white;
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

@media (max-width: 768px) {
    .clients-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .client-card {
        height: auto;
        min-height: 260px;
    }
    
    .client-card-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .client-card-value {
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
            <div class="position-relative">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="search-input-modern" 
                    placeholder="🔍 Buscar clientes por nombre, documento, ubicación o teléfono..."
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
                $filtrosActivos[] = '<strong>Búsqueda:</strong> "' . Html::encode($searchTerm) . '"';
            }
            if (!empty($searchModel->status)) {
                $filtrosActivos[] = '<strong>Status:</strong> ' . Html::encode($searchModel->status);
            }
            if (!empty($searchModel->ubicacion)) {
                $filtrosActivos[] = '<strong>Ubicación:</strong> ' . Html::encode($searchModel->ubicacion);
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
                            <span class="badge bg-primary ms-2"><?= $totalClientes ?> resultado<?= $totalClientes !== 1 ? 's' : '' ?></span>
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
                            
                            <?php if (!empty($cliente->ubicacion)): ?>
                                <div class="client-card-row">
                                    <span class="client-card-label">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Ubicación:
                                    </span>
                                    <span class="client-card-value" title="<?= Html::encode($cliente->ubicacion) ?>">
                                        <?= Html::encode($cliente->ubicacion) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
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
// JavaScript para búsqueda en tiempo real y filtros
$js = "
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');

// Función para realizar la búsqueda
function performSearch(searchTerm) {
    const currentUrl = new URL(window.location.href);
    
    // Asegurar que siempre incluya el parámetro r
    currentUrl.searchParams.set('r', 'clientes/index');
    
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

function toggleSearch() {
    const searchSection = document.getElementById('searchSection');
    const toggleButton = document.getElementById('searchToggle');
    
    if (searchSection.style.display === 'none') {
        searchSection.style.display = 'block';
        toggleButton.innerHTML = '<i class=\"bi bi-x\"></i> Ocultar Búsqueda';
    } else {
        searchSection.style.display = 'none';
        toggleButton.innerHTML = '<i class=\"bi bi-search\"></i> Buscar Clientes';
    }
}
";

$this->registerJs($js, \yii\web\View::POS_END);
?>