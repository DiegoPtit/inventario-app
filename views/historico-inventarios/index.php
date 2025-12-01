<?php

use app\models\HistoricoInventarios;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\HistoricoInventariosSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = Yii::t('app', 'Inventarios');

// Registrar CSS personalizado para las tarjetas de histórico de inventarios
$this->registerCss('
.inventario-card {
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition: box-shadow 0.2s ease;
    padding: 15px;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    height: 100%;
    cursor: pointer;
}

.inventario-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.inventario-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.inventario-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.inventario-card-id {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 4px;
}

.inventario-card-periodo {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px;
    background: white;
    border-radius: 6px;
}

.inventario-card-fecha {
    font-size: 0.85rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 6px;
}

.inventario-card-fecha i {
    color: #007bff;
    font-size: 0.9rem;
}

.inventario-card-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 8px;
}

.inventario-stat-box {
    background: white;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
}

.inventario-stat-label {
    font-size: 0.7rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 4px;
}

.inventario-stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
}

.inventario-stat-value.cantidad {
    color: #28a745;
}

.inventario-stat-value.valor {
    color: #007bff;
}

.inventario-stat-conversions {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #e9ecef;
}

.inventario-stat-conversions .conversion-line {
    font-size: 0.65rem;
    color: #6c757d;
    display: block;
    margin-top: 2px;
}

.inventario-stat-conversions .conversion-line strong {
    color: #495057;
    font-weight: 600;
}

.inventario-card-nota {
    font-size: 0.8rem;
    color: #6c757d;
    font-style: italic;
    padding: 8px;
    background: white;
    border-radius: 6px;
    margin-top: 5px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.inventarios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.no-inventarios {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-inventarios i {
    font-size: 4rem;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .inventarios-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .inventario-card-stats {
        grid-template-columns: 1fr;
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
<div class="historico-inventarios-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-4">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Registrar Cierre de Inventario'), '#', [
                    'class' => 'btn btn-outline-primary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f0f8ff; border-color: #b3d9ff; color: #004085; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#modalCierreInventario',
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
            $hayFiltros = !empty(Yii::$app->request->get('HistoricoInventariosSearch'));
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
                <div class="col-md-6">
                    <?= $form->field($searchModel, 'fecha_inicio')->input('date', [
                        'value' => Yii::$app->request->get('HistoricoInventariosSearch')['fecha_inicio'] ?? ''
                    ])->label('Fecha de Inicio') ?>
                </div>
                
                <div class="col-md-6">
                    <?= $form->field($searchModel, 'fecha_cierre')->input('date', [
                        'value' => Yii::$app->request->get('HistoricoInventariosSearch')['fecha_cierre'] ?? ''
                    ])->label('Fecha de Cierre') ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 d-flex align-items-end">
                    <div class="filter-buttons w-100">
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
        $inventarios = $dataProvider->getModels(); 
        $totalInventarios = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($inventarios) ?> de <?= $totalInventarios ?> registros de inventario
            </small>
        </div>

        <?php if (empty($inventarios)): ?>
            <div class="inventarios-grid">
                <div class="no-inventarios">
                    <i class="bi bi-archive"></i>
                    <h3>No hay cierres de inventario registrados</h3>
                    <p>¡Registra cierres de inventario para mostrarlos aquí!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="inventarios-grid">
                <?php foreach ($inventarios as $inventario): ?>
                    <div class="inventario-card" onclick="window.location.href='<?= Url::to(['view', 'id' => $inventario->id]) ?>'">
                        <div class="inventario-card-content">
                            <div class="inventario-card-header">
                                <span class="inventario-card-id">
                                    <i class="bi bi-hash"></i><?= Html::encode($inventario->id) ?>
                                </span>
                            </div>
                            
                            <div class="inventario-card-periodo">
                                <div class="inventario-card-fecha">
                                    <i class="bi bi-calendar-check"></i>
                                    <strong>Inicio:</strong> <?= Yii::$app->formatter->asDate($inventario->fecha_inicio, 'dd/MM/yyyy') ?>
                                </div>
                                <div class="inventario-card-fecha">
                                    <i class="bi bi-calendar-x"></i>
                                    <strong>Cierre:</strong> <?= Yii::$app->formatter->asDate($inventario->fecha_cierre, 'dd/MM/yyyy') ?>
                                </div>
                            </div>
                            
                            <div class="inventario-card-stats">
                                <div class="inventario-stat-box">
                                    <div class="inventario-stat-label">Productos</div>
                                    <div class="inventario-stat-value cantidad">
                                        <?= Html::encode(Yii::$app->formatter->asDecimal($inventario->cantidad_productos, 0)) ?>
                                    </div>
                                </div>
                                <div class="inventario-stat-box">
                                    <div class="inventario-stat-label">Valor</div>
                                    <div class="inventario-stat-value valor">
                                        $<?= Html::encode(Yii::$app->formatter->asDecimal($inventario->valor, 2)) ?>
                                    </div>
                                    
                                    <?php if ($precioParalelo && $precioOficial): ?>
                                        <?php 
                                        $valorVes = $inventario->valor * $precioParalelo->precio_ves;
                                        $valorUsdOficial = $valorVes / $precioOficial->precio_ves;
                                        ?>
                                        <div class="inventario-stat-conversions">
                                            <span class="conversion-line">Bs. <?= number_format($valorVes, 2, ',', '.') ?></span>
                                            <span class="conversion-line"><strong>$<?= number_format($valorUsdOficial, 2) ?></strong> (BCV)</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($inventario->nota)): ?>
                                <div class="inventario-card-nota">
                                    <i class="bi bi-sticky"></i> <?= Html::encode($inventario->nota) ?>
                                </div>
                            <?php endif; ?>
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
