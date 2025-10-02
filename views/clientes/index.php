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

        <!-- Sección de búsqueda colapsable -->
        <div class="search-section" id="searchSection" style="display: none;">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <?php 
        $clientes = $dataProvider->getModels(); 
        $totalClientes = $dataProvider->getTotalCount();
        ?>
        
        <div class="mb-3">
            <small class="text-muted">
                Mostrando <?= count($clientes) ?> de <?= $totalClientes ?> clientes
            </small>
        </div>

        <?php if (empty($clientes)): ?>
            <div class="clients-grid">
                <div class="no-clients">
                    <i class="bi bi-person-x"></i>
                    <h3>No hay clientes registrados</h3>
                    <p>¡Registra clientes para mostrarlos aquí!</p>
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
// JavaScript para toggle de búsqueda
$js = "
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