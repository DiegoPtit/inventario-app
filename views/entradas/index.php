<?php

use app\models\Entradas;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\EntradasSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Entradas');

// Registrar CSS personalizado para las tarjetas de entradas
$this->registerCss('
.entrada-card {
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition: all 0.2s ease;
    padding: 15px;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    cursor: pointer;
}

.entrada-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.entrada-card-link {
    text-decoration: none;
    color: inherit;
}

.entrada-card-link::after {
    content: \'\';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.entrada-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.entrada-card-title {
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

.entrada-card-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin: 5px 0;
}

.entrada-card-cantidad {
    font-size: 1.1rem;
    font-weight: 700;
    color: #28a745;
}

.entrada-card-proveedor {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: right;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.entrada-card-footer {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.entrada-card-date {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.entrada-card-doc {
    font-size: 0.75rem;
}

.entrada-download-link {
    color: #007bff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
    position: relative;
    z-index: 2;
}

.entrada-download-link:hover {
    background-color: rgba(0, 123, 255, 0.1);
    color: #0056b3;
    text-decoration: none;
}

.entradas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.no-entradas {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.no-entradas i {
    font-size: 4rem;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .entradas-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .entrada-card-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .entrada-card-proveedor {
        text-align: left;
    }
    
    .entrada-card-footer {
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
<div class="entradas-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-4">
            <h1 class="text-start"><?= Html::encode($this->title) ?></h1>
            <div class="text-start mt-3">
                <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Registrar Entrada de Producto'), ['create'], [
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
            $hayFiltros = !empty(Yii::$app->request->get('EntradasSearch'));
            ?>
            <div id="filtrosCollapse" style="display: <?= $hayFiltros ? 'block' : 'none' ?>;">
            
            <?php
            use yii\widgets\ActiveForm;
            use yii\helpers\ArrayHelper;
            use app\models\Proveedores;
            
            // Obtener lista de proveedores para el dropdown
            $proveedores = ArrayHelper::map(Proveedores::find()->all(), 'id', 'razon_social');
            
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
                    <?= $form->field($searchModel, 'nombre_producto')->textInput([
                        'placeholder' => 'Buscar por marca o descripción...',
                        'value' => Yii::$app->request->get('EntradasSearch')['nombre_producto'] ?? ''
                    ])->label('Nombre de Producto') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'fecha_desde')->input('date', [
                        'value' => Yii::$app->request->get('EntradasSearch')['fecha_desde'] ?? ''
                    ])->label('Fecha Desde') ?>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'fecha_hasta')->input('date', [
                        'value' => Yii::$app->request->get('EntradasSearch')['fecha_hasta'] ?? ''
                    ])->label('Fecha Hasta') ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($searchModel, 'id_proveedor')->dropDownList(
                        ['' => 'Todos los proveedores'] + $proveedores,
                        ['value' => Yii::$app->request->get('EntradasSearch')['id_proveedor'] ?? '']
                    )->label('Proveedor') ?>
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
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
        $entradas = $dataProvider->getModels(); 
        $totalEntradas = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($entradas) ?> de <?= $totalEntradas ?> entradas
            </small>
        </div>

        <?php if (empty($entradas)): ?>
            <div class="entradas-grid">
                <div class="no-entradas">
                    <i class="bi bi-inbox"></i>
                    <h3>No hay entradas registradas</h3>
                    <p>¡Registra entradas de productos para mostrarlas aquí!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="entradas-grid">
                <?php foreach ($entradas as $entrada): ?>
                    <div class="entrada-card">
                        <?= Html::a('', ['view', 'id' => $entrada->id], [
                            'class' => 'entrada-card-link',
                            'title' => 'Ver detalles de la entrada'
                        ]) ?>
                        <div class="entrada-card-content">
                            <?php
                            // Crear nombre del producto concatenando marca y descripción
                            $nombreProducto = '';
                            if ($entrada->producto) {
                                $partes = array_filter([
                                    $entrada->producto->marca,
                                    $entrada->producto->descripcion
                                ]);
                                $nombreProducto = implode(' ', $partes);
                                if (empty($nombreProducto)) {
                                    $nombreProducto = 'Producto #' . $entrada->id_producto;
                                }
                            } else {
                                $nombreProducto = 'Producto no disponible';
                            }
                            ?>
                            
                            <h3 class="entrada-card-title">
                                <?= Html::encode($nombreProducto) ?>
                            </h3>
                            
                            <div class="entrada-card-info">
                                <span class="entrada-card-cantidad">
                                    <?= Html::encode($entrada->cantidad) ?> unidades
                                </span>
                                <span class="entrada-card-proveedor">
                                    <?= $entrada->proveedor ? Html::encode($entrada->proveedor->razon_social) : 'Sin proveedor' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="entrada-card-footer">
                            <span class="entrada-card-date">
                                <?= Yii::$app->formatter->asDate($entrada->created_at, 'dd/MM/yyyy') ?>
                            </span>
                            
                            <?php if (!empty($entrada->ruta_documento_respaldo)): ?>
                                <div class="entrada-card-doc">
                                    <?= Html::a(
                                        '<i class="bi bi-file-earmark-text"></i>',
                                        Yii::getAlias('@web') . '/' . $entrada->ruta_documento_respaldo,
                                        [
                                            'class' => 'entrada-download-link',
                                            'target' => '_blank',
                                            'download' => true,
                                            'title' => 'Descargar documento',
                                            'onclick' => 'event.stopPropagation();'
                                        ]
                                    ) ?>
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