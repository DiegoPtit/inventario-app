<?php

use app\models\Lugares;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\LugaresSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Lugares');

// Registrar CSS personalizado para las tarjetas de lugares
$this->registerCss('
.lugar-card {
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition: box-shadow 0.2s ease;
    padding: 15px;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.lugar-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.lugar-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.lugar-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.lugar-card-descripcion {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 5px 0;
    min-height: 60px;
}

.lugar-card-ubicacion {
    font-size: 0.8rem;
    color: #007bff;
    display: flex;
    align-items: center;
    gap: 5px;
}

.lugar-card-footer {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.lugar-card-date {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.lugar-card-actions {
    display: flex;
    gap: 8px;
}

.lugar-action-btn {
    color: #6c757d;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.lugar-action-btn:hover {
    text-decoration: none;
}

.lugar-action-btn.view-btn:hover {
    background-color: rgba(0, 123, 255, 0.1);
    color: #0056b3;
}

.lugar-action-btn.edit-btn:hover {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.lugar-action-btn.delete-btn:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.lugares-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.no-lugares {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-lugares i {
    font-size: 4rem;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .lugares-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .lugar-card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}

.search-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

.search-section h5 {
    color: #495057;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-form .form-group {
    margin-bottom: 15px;
}

.filter-form .form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.filter-form .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

.filter-form .form-label {
    font-weight: 500;
    color: #495057;
    font-size: 0.85rem;
    margin-bottom: 5px;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.filter-buttons .btn {
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 500;
}

.btn-filter {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-filter:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-clear {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-clear:hover {
    background-color: #545b62;
    border-color: #545b62;
}

@media (max-width: 768px) {
    .filter-buttons {
        flex-direction: column;
    }
    
    .filter-buttons .btn {
        width: 100%;
    }
}

.filtros-toggle-btn {
    color: #6c757d;
    transition: color 0.2s ease;
}

.filtros-toggle-btn:hover {
    color: #007bff;
}

.filtros-toggle-btn i {
    transition: transform 0.3s ease;
}

#filtrosCollapse {
    overflow: hidden;
}
');
?>
<div class="lugares-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-4">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Registrar Nuevo Lugar'), ['create'], [
                    'class' => 'btn btn-outline-success btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8fff8; border-color: #c3e6cb; color: #155724; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
        </div>

        <!-- Sección de filtros colapsable -->
        <div class="search-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
                <button class="btn btn-link btn-sm p-0 text-decoration-none filtros-toggle-btn" type="button" id="filtrosToggleBtn">
                    <i class="bi bi-chevron-down" id="filtrosChevron"></i>
                    <span class="ms-1 fw-medium filtros-toggle-text">Mostrar filtros</span>
                </button>
            </div>
            
            <?php
            // Verificar si hay filtros aplicados para mostrar la sección expandida
            $hayFiltros = !empty(Yii::$app->request->get('LugaresSearch'));
            ?>
            <div id="filtrosCollapse" style="display: <?= $hayFiltros ? 'block' : 'none' ?>;">
            
            <?php
            use yii\widgets\ActiveForm;
            
            $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'filter-form'],
                'fieldConfig' => [
                    'template' => "<div class='form-group'>{label}\n{input}\n{hint}\n{error}</div>",
                    'labelOptions' => ['class' => 'form-label'],
                    'inputOptions' => ['class' => 'form-control'],
                ]
            ]); ?>
            
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'nombre')->textInput([
                        'placeholder' => 'Buscar por nombre...',
                        'value' => Yii::$app->request->get('LugaresSearch')['nombre'] ?? ''
                    ])->label('Nombre del Lugar') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'ubicacion')->textInput([
                        'placeholder' => 'Buscar por ubicación...',
                        'value' => Yii::$app->request->get('LugaresSearch')['ubicacion'] ?? ''
                    ])->label('Ubicación') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'descripcion')->textInput([
                        'placeholder' => 'Buscar en descripción...',
                        'value' => Yii::$app->request->get('LugaresSearch')['descripcion'] ?? ''
                    ])->label('Descripción') ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="filter-buttons">
                        <?= Html::submitButton('<i class="bi bi-search"></i> Buscar', [
                            'class' => 'btn btn-filter flex-fill'
                        ]) ?>
                        <?= Html::a('<i class="bi bi-arrow-clockwise"></i> Limpiar', ['index'], [
                            'class' => 'btn btn-clear flex-fill'
                        ]) ?>
                    </div>
                </div>
            </div>
            
            <?php ActiveForm::end(); ?>
            </div>
        </div>

        <?php 
        $lugares = $dataProvider->getModels(); 
        $totalLugares = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($lugares) ?> de <?= $totalLugares ?> lugares
            </small>
        </div>

        <?php if (empty($lugares)): ?>
            <div class="lugares-grid">
                <div class="no-lugares">
                    <i class="bi bi-geo-alt"></i>
                    <h3>No hay lugares registrados</h3>
                    <p>¡Registra lugares para mostrarlos aquí!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="lugares-grid">
                <?php foreach ($lugares as $lugar): ?>
                    <div class="lugar-card">
                        <div class="lugar-card-content">
                            <h3 class="lugar-card-title">
                                <?= Html::encode($lugar->nombre) ?>
                            </h3>
                            
                            <div class="lugar-card-descripcion">
                                <?= $lugar->descripcion ? Html::encode($lugar->descripcion) : '<em class="text-muted">Sin descripción</em>' ?>
                            </div>
                            
                            <?php if (!empty($lugar->ubicacion)): ?>
                                <div class="lugar-card-ubicacion">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span><?= Html::encode($lugar->ubicacion) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="lugar-card-footer">
                            <span class="lugar-card-date">
                                <?= Yii::$app->formatter->asDate($lugar->created_at, 'dd/MM/yyyy') ?>
                            </span>
                            
                            <div class="lugar-card-actions">
                                
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['view', 'id' => $lugar->id], [
                                    'class' => 'lugar-action-btn edit-btn',
                                    'title' => 'Editar'
                                ]) ?>
                                <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $lugar->id], [
                                    'class' => 'lugar-action-btn view-btn',
                                    'title' => 'Ver'
                                ]) ?>
                            </div>
                        </div>
                    </div>
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
$this->registerJs("
$(document).ready(function() {
    // Verificar estado inicial
    var isVisible = " . ($hayFiltros ? 'true' : 'false') . ";
    
    // Configurar estado inicial del botón
    if (isVisible) {
        $('#filtrosChevron').css('transform', 'rotate(180deg)');
        $('.filtros-toggle-text').text('Ocultar filtros');
    }
    
    $('#filtrosToggleBtn').click(function() {
        var \$collapse = $('#filtrosCollapse');
        var \$chevron = $('#filtrosChevron');
        var \$text = $('.filtros-toggle-text');
        
        if (!isVisible) {
            // Mostrar filtros
            \$collapse.slideDown(300);
            \$chevron.css('transform', 'rotate(180deg)');
            \$text.text('Ocultar filtros');
            isVisible = true;
        } else {
            // Ocultar filtros
            \$collapse.slideUp(300);
            \$chevron.css('transform', 'rotate(0deg)');
            \$text.text('Mostrar filtros');
            isVisible = false;
        }
    });
});
");
?>
