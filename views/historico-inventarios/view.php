<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\HistoricoInventarios $model */
/** @var app\models\Entradas[] $entradas */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = 'Cierre de Inventario #' . $model->id;
\yii\web\YiiAsset::register($this);

// Calcular duración del período
$fechaInicio = new DateTime($model->fecha_inicio);
$fechaCierre = new DateTime($model->fecha_cierre);
$duracion = $fechaInicio->diff($fechaCierre)->days;
?>

<style>
.inventario-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header - Información Principal */
.inventario-header {
    background: #1a3a52;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    padding: 40px;
    color: white;
    text-align: center;
}

.inventario-header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.inventario-header-subtitle {
    font-size: 1.2rem;
    opacity: 0.95;
    margin-bottom: 30px;
}

.inventario-header-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.inventario-header-stat {
    text-align: center;
}

.inventario-header-stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    display: block;
    margin-bottom: 8px;
}

.inventario-header-stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.inventario-header-stat-conversions {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.inventario-header-stat-conversions .conversion-line {
    font-size: 0.75rem;
    opacity: 0.85;
    display: block;
    margin-top: 3px;
}

.inventario-header-stat-conversions .conversion-line strong {
    font-weight: 700;
}

/* Body - Especificaciones */
.inventario-body {
    margin-bottom: 30px;
}

.especificaciones-titulo {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 25px;
    color: #1a3a52;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.especificaciones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 40px;
}

.especificacion-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 18px;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.especificacion-card:hover {
    border-color: #1a3a52;
    box-shadow: 0 2px 8px rgba(26, 58, 82, 0.08);
}

.especificacion-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #1a3a52;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.especificacion-icon i {
    font-size: 1.2rem;
    color: #ffffff;
}

.especificacion-content {
    flex: 1;
    min-width: 0;
}

.especificacion-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.especificacion-value {
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 600;
    word-wrap: break-word;
}

.especificacion-value.texto-largo {
    font-size: 0.9rem;
    line-height: 1.4;
}

.especificacion-conversions {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #e9ecef;
}

.especificacion-conversions .conversion-line {
    font-size: 0.7rem;
    color: #6c757d;
    display: block;
    margin-top: 3px;
}

.especificacion-conversions .conversion-line strong {
    color: #495057;
    font-weight: 600;
}

/* Lista de Entradas */
.entradas-section {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.entradas-titulo {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 25px;
    color: #1a3a52;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.entradas-titulo i {
    color: #1a3a52;
}

.entradas-lista {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 12px;
}

.entrada-item {
    padding: 0;
    border: none;
    display: block;
}

.entrada-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    text-decoration: none;
    transition: all 0.2s;
    gap: 15px;
}

.entrada-link:hover {
    border-color: #1a3a52;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(26, 58, 82, 0.1);
    transform: translateY(-1px);
}

.entrada-info {
    flex: 1;
    min-width: 0;
}

.entrada-producto {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 6px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.entrada-producto i {
    color: #1a3a52;
    font-size: 1rem;
}

.entrada-ubicacion {
    font-size: 0.85rem;
    color: #495057;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.entrada-ubicacion i {
    color: #6c757d;
    font-size: 0.9rem;
}

.entrada-detalles {
    font-size: 0.8rem;
    color: #6c757d;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.entrada-detalle-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.entrada-cantidad {
    background: #1a3a52;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

.entrada-doc-badge {
    color: #0d6efd;
    margin-left: 8px;
    transition: color 0.2s;
}

.entrada-link:hover .entrada-doc-badge {
    color: #0a58ca;
}

.no-entradas {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-entradas i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

/* Footer - Botones */
.inventario-footer {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
}

.btn-action {
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border: 2px solid;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-editar {
    color: #0d6efd;
    border-color: #0d6efd;
    background: transparent;
}

.btn-editar:hover {
    background: #0d6efd;
    color: white;
}

.btn-borrar {
    color: #dc3545;
    border-color: #dc3545;
    background: transparent;
}

.btn-borrar:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .especificaciones-grid {
        grid-template-columns: 1fr;
    }
    
    .inventario-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }

    .inventario-header-stats {
        flex-direction: column;
        gap: 25px;
    }
    
    .inventario-header-title {
        font-size: 1.8rem;
    }
    
    .inventario-header-subtitle {
        font-size: 1rem;
    }
    
    .entrada-link {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .entrada-cantidad {
        align-self: flex-end;
    }

    .entradas-titulo {
        font-size: 1.2rem;
        flex-wrap: wrap;
    }

    .entradas-section {
        padding: 20px 15px;
    }
}

@media (max-width: 480px) {
    .inventario-header-stat-value {
        font-size: 2rem;
    }
    
    .entradas-titulo {
        font-size: 1.1rem;
    }

    .entrada-detalles {
        font-size: 0.75rem;
        line-height: 1.5;
        flex-direction: column;
        gap: 4px;
    }
}
</style>

<div class="inventario-view-container">

    <h2><?= Html::encode($this->title) ?></h2>

    <div class="text-start mb-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Ver todos', Url::to(['historico-inventarios/index']), [
            'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
            'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
        ]) ?>
    </div>
    
    <!-- HEADER - INFORMACIÓN PRINCIPAL -->
    <div class="inventario-header">
        <h1 class="inventario-header-title">
            <i class="bi bi-archive"></i> Cierre de Inventario
        </h1>
        <p class="inventario-header-subtitle">
            Período: <?= Yii::$app->formatter->asDate($model->fecha_inicio, 'dd/MM/yyyy') ?> 
            - <?= Yii::$app->formatter->asDate($model->fecha_cierre, 'dd/MM/yyyy') ?>
            (<?= $duracion ?> días)
        </p>
        
        <div class="inventario-header-stats">
            <div class="inventario-header-stat">
                <span class="inventario-header-stat-value">
                    <?= Html::encode(Yii::$app->formatter->asDecimal($model->cantidad_productos, 0)) ?>
                </span>
                <span class="inventario-header-stat-label">
                    <i class="bi bi-box-seam"></i> Productos
                </span>
            </div>
            
            <div class="inventario-header-stat">
                <span class="inventario-header-stat-value">
                    $<?= Html::encode(Yii::$app->formatter->asDecimal($model->valor, 2)) ?>
                </span>
                <span class="inventario-header-stat-label">
                    <i class="bi bi-currency-dollar"></i> Valor Total
                </span>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $valorVes = $model->valor * $precioParalelo->precio_ves;
                    $valorUsdOficial = $valorVes / $precioOficial->precio_ves;
                    ?>
                    <div class="inventario-header-stat-conversions">
                        <span class="conversion-line">Bs. <?= number_format($valorVes, 2, ',', '.') ?></span>
                        <span class="conversion-line"><strong>$<?= number_format($valorUsdOficial, 2) ?></strong> (BCV)</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="inventario-header-stat">
                <span class="inventario-header-stat-value">
                    <?= count($entradas) ?>
                </span>
                <span class="inventario-header-stat-label">
                    <i class="bi bi-list-ul"></i> Entradas
                </span>
            </div>
        </div>
    </div>

    <!-- BODY - ESPECIFICACIONES -->
    <div class="inventario-body">
        <h2 class="especificaciones-titulo">Detalles del Cierre de Inventario</h2>
        
        <div class="especificaciones-grid">
            <!-- ID -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-hash"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">ID del Cierre</div>
                    <div class="especificacion-value">#<?= Html::encode($model->id) ?></div>
                </div>
            </div>

            <!-- Fecha de Inicio -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Fecha de Inicio</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDate($model->fecha_inicio, 'long') ?></div>
                </div>
            </div>

            <!-- Fecha de Cierre -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Fecha de Cierre</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDate($model->fecha_cierre, 'long') ?></div>
                </div>
            </div>

            <!-- Duración -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Duración del Período</div>
                    <div class="especificacion-value"><?= $duracion ?> día<?= $duracion != 1 ? 's' : '' ?></div>
                </div>
            </div>

            <!-- Cantidad de Productos -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Cantidad de Productos</div>
                    <div class="especificacion-value"><?= Html::encode(Yii::$app->formatter->asDecimal($model->cantidad_productos, 0)) ?></div>
                </div>
            </div>

            <!-- Valor Total -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Valor Total del Inventario</div>
                    <div class="especificacion-value">$<?= Html::encode(Yii::$app->formatter->asDecimal($model->valor, 2)) ?></div>
                    
                    <?php if ($precioParalelo && $precioOficial): ?>
                        <?php 
                        $valorVes = $model->valor * $precioParalelo->precio_ves;
                        $valorUsdOficial = $valorVes / $precioOficial->precio_ves;
                        ?>
                        <div class="especificacion-conversions">
                            <span class="conversion-line">Bs. <?= number_format($valorVes, 2, ',', '.') ?></span>
                            <span class="conversion-line"><strong>$<?= number_format($valorUsdOficial, 2) ?></strong> (BCV)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Valor Promedio -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calculator"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Valor Promedio por Producto</div>
                    <div class="especificacion-value">
                        $<?= $model->cantidad_productos > 0 ? Html::encode(Yii::$app->formatter->asDecimal($model->valor / $model->cantidad_productos, 2)) : '0.00' ?>
                    </div>
                    
                    <?php if ($precioParalelo && $precioOficial && $model->cantidad_productos > 0): ?>
                        <?php 
                        $valorPromedio = $model->valor / $model->cantidad_productos;
                        $valorPromedioVes = $valorPromedio * $precioParalelo->precio_ves;
                        $valorPromedioUsdOficial = $valorPromedioVes / $precioOficial->precio_ves;
                        ?>
                        <div class="especificacion-conversions">
                            <span class="conversion-line">Bs. <?= number_format($valorPromedioVes, 2, ',', '.') ?></span>
                            <span class="conversion-line"><strong>$<?= number_format($valorPromedioUsdOficial, 2) ?></strong> (BCV)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fecha de Registro -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Fecha de Registro</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></div>
                </div>
            </div>

            <!-- Nota -->
            <?php if (!empty($model->nota)): ?>
            <div class="especificacion-card" style="grid-column: 1 / -1;">
                <div class="especificacion-icon">
                    <i class="bi bi-sticky"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Nota</div>
                    <div class="especificacion-value texto-largo"><?= Html::encode($model->nota) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- LISTA DE ENTRADAS DEL PERÍODO -->
        <div class="entradas-section">
            <h3 class="entradas-titulo">
                <i class="bi bi-list-ul"></i>
                Entradas del Período (<?= count($entradas) ?>)
            </h3>
            
            <?php if (!empty($entradas)): ?>
                <ul class="entradas-lista">
                    <?php foreach ($entradas as $entrada): ?>
                        <?php
                        // Crear nombre del producto
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
                        
                        // Obtener ubicación
                        $ubicacion = 'Sin ubicación';
                        if ($entrada->lugar) {
                            $ubicacion = $entrada->lugar->nombre;
                        }
                        ?>
                        <li class="entrada-item">
                            <?= Html::beginTag('a', [
                                'href' => Url::to(['entradas/view', 'id' => $entrada->id]),
                                'class' => 'entrada-link',
                                'title' => 'Ver detalles de la entrada'
                            ]) ?>
                                <div class="entrada-info">
                                    <div class="entrada-producto">
                                        <i class="bi bi-box"></i>
                                        <?= Html::encode($nombreProducto) ?>
                                        <?php if (!empty($entrada->ruta_documento_respaldo)): ?>
                                            <i class="bi bi-file-earmark-text entrada-doc-badge" title="Tiene documento"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="entrada-ubicacion">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <?= Html::encode($ubicacion) ?>
                                    </div>
                                    <div class="entrada-detalles">
                                        <?php if ($entrada->proveedor): ?>
                                            <span class="entrada-detalle-item">
                                                <i class="bi bi-building"></i>
                                                <?= Html::encode($entrada->proveedor->razon_social) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="entrada-detalle-item">
                                            <i class="bi bi-calendar3"></i>
                                            <?= Yii::$app->formatter->asDate($entrada->created_at, 'dd/MM/yyyy') ?>
                                        </span>
                                        <?php if ($entrada->nro_documento): ?>
                                            <span class="entrada-detalle-item">
                                                <i class="bi bi-file-text"></i>
                                                Doc: <?= Html::encode($entrada->nro_documento) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="entrada-cantidad">
                                    <?= $entrada->cantidad ?> unidad<?= $entrada->cantidad != 1 ? 'es' : '' ?>
                                </div>
                            <?= Html::endTag('a') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-entradas">
                    <i class="bi bi-inbox"></i>
                    <p>No hay entradas registradas en este período.</p>
                    <p>Período: <?= Yii::$app->formatter->asDate($model->fecha_inicio, 'dd/MM/yyyy') ?> - <?= Yii::$app->formatter->asDate($model->fecha_cierre, 'dd/MM/yyyy') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>


</div>
