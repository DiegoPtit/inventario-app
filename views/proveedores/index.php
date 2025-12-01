<?php

use app\models\Proveedores;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProveedoresSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Proveedores');

// Registrar CSS personalizado para las tarjetas de proveedores
$this->registerCss('
.proveedor-card {
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

.proveedor-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.proveedor-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.proveedor-card-title {
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

.proveedor-card-info {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.4;
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin: 5px 0;
    min-height: 60px;
}

.proveedor-card-info-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.proveedor-card-ubicacion {
    font-size: 0.8rem;
    color: #007bff;
    display: flex;
    align-items: center;
    gap: 5px;
}

.proveedor-card-footer {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.proveedor-card-date {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.proveedor-card-actions {
    display: flex;
    gap: 8px;
}

.proveedor-action-btn {
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

.proveedor-action-btn:hover {
    text-decoration: none;
}

.proveedor-action-btn.view-btn:hover {
    background-color: rgba(0, 123, 255, 0.1);
    color: #0056b3;
}

.proveedor-action-btn.edit-btn:hover {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.proveedor-action-btn.delete-btn:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.proveedores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.no-proveedores {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-proveedores i {
    font-size: 4rem;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .proveedores-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .proveedor-card-footer {
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
<div class="proveedores-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-4">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Registrar Nuevo Proveedor'), ['create'], [
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
            $hayFiltros = !empty(Yii::$app->request->get('ProveedoresSearch'));
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
                    <?= $form->field($searchModel, 'razon_social')->textInput([
                        'placeholder' => 'Buscar por razón social...',
                        'value' => Yii::$app->request->get('ProveedoresSearch')['razon_social'] ?? ''
                    ])->label('Razón Social') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'ciudad')->textInput([
                        'placeholder' => 'Buscar por ciudad...',
                        'value' => Yii::$app->request->get('ProveedoresSearch')['ciudad'] ?? ''
                    ])->label('Ciudad') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'pais')->textInput([
                        'placeholder' => 'Buscar por país...',
                        'value' => Yii::$app->request->get('ProveedoresSearch')['pais'] ?? ''
                    ])->label('País') ?>
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
        $proveedores = $dataProvider->getModels(); 
        $totalProveedores = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($proveedores) ?> de <?= $totalProveedores ?> proveedores
            </small>
        </div>

        <?php if (empty($proveedores)): ?>
            <div class="proveedores-grid">
                <div class="no-proveedores">
                    <i class="bi bi-truck"></i>
                    <h3>No hay proveedores registrados</h3>
                    <p>¡Registra proveedores para mostrarlos aquí!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="proveedores-grid">
                <?php foreach ($proveedores as $proveedor): ?>
                    <div class="proveedor-card">
                        <div class="proveedor-card-content">
                            <h3 class="proveedor-card-title">
                                <?= Html::encode($proveedor->razon_social) ?>
                            </h3>
                            
                            <div class="proveedor-card-info">
                                <?php if (!empty($proveedor->documento_identificacion)): ?>
                                    <div class="proveedor-card-info-item">
                                        <i class="bi bi-card-text"></i>
                                        <span><?= Html::encode($proveedor->documento_identificacion) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($proveedor->telefono)): ?>
                                    <div class="proveedor-card-info-item">
                                        <i class="bi bi-telephone"></i>
                                        <span><?= Html::encode($proveedor->telefono) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($proveedor->ciudad) || !empty($proveedor->pais)): ?>
                                <div class="proveedor-card-ubicacion">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>
                                        <?= Html::encode($proveedor->ciudad ? $proveedor->ciudad . ', ' : '') ?>
                                        <?= Html::encode($proveedor->pais) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="proveedor-card-footer">
                            <span class="proveedor-card-date">
                                <?= Yii::$app->formatter->asDate($proveedor->created_at, 'dd/MM/yyyy') ?>
                            </span>
                            
                            <div class="proveedor-card-actions">
                                <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $proveedor->id], [
                                    'class' => 'proveedor-action-btn view-btn',
                                    'title' => 'Ver'
                                ]) ?>
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $proveedor->id], [
                                    'class' => 'proveedor-action-btn edit-btn',
                                    'title' => 'Editar'
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
