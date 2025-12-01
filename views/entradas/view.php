<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Entradas $model */

// Cargar relaciones necesarias
$producto = $model->producto;
$proveedor = $model->proveedor;
$lugar = $model->lugar;

// Crear título
$titulo = 'Entrada #' . $model->id;
if ($producto) {
    $nombreProducto = implode(' - ', array_filter([
        $producto->marca,
        $producto->modelo,
        $producto->color
    ]));
    if (empty($nombreProducto)) {
        $nombreProducto = 'Producto #' . $producto->id;
    }
    $titulo = 'Entrada - ' . Html::encode($nombreProducto);
}

$this->title = $titulo;
\yii\web\YiiAsset::register($this);

// Calcular valores financieros
$costoTotal = 0;
$valorVenta = 0;
if ($producto) {
    $costoTotal = $producto->costo * $model->cantidad;
    $valorVenta = $producto->precio_venta * $model->cantidad;
}
?>

<style>
.entrada-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header - Resumen de la Entrada */
.entrada-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 12px;
    padding: 30px;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 30px;
}

.entrada-header-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.entrada-header-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge-entrada {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Secciones */
.section-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.section-title i {
    color: #28a745;
}

/* Grid de información */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #28a745;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 1.5rem;
    color: #ffffff;
}

.info-content {
    flex: 1;
    min-width: 0;
}

.info-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.info-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    word-wrap: break-word;
}

/* Resumen Financiero */
.resumen-financiero {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    margin-top: 30px;
}

.resumen-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
    flex-wrap: wrap;
    gap: 8px;
}

.resumen-item:last-child {
    border-bottom: none;
    margin-top: 10px;
    padding-top: 15px;
    border-top: 2px solid #28a745;
}

.resumen-label {
    font-size: 1rem;
    color: #495057;
    font-weight: 500;
    flex: 1;
    min-width: 120px;
}

.resumen-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    text-align: right;
    flex-shrink: 0;
}

.resumen-item:last-child .resumen-label,
.resumen-item:last-child .resumen-value {
    font-size: 1.3rem;
    font-weight: 700;
}

/* Mobile-friendly resumen financiero */
@media (max-width: 768px) {
    .resumen-financiero {
        padding: 20px 15px;
        margin-top: 20px;
    }
    
    .resumen-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px 0;
        gap: 5px;
    }
    
    .resumen-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    
    .resumen-value {
        font-size: 1.2rem;
        color: #2c3e50;
        font-weight: 700;
        text-align: left;
        width: 100%;
        padding: 8px 12px;
        background: #ffffff;
        border-radius: 6px;
        border-left: 3px solid #28a745;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .resumen-item:last-child .resumen-label {
        font-size: 1rem;
        color: #28a745;
    }
    
    .resumen-item:last-child .resumen-value {
        font-size: 1.4rem;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-left: none;
        text-align: center;
        font-weight: 800;
    }
}

@media (max-width: 480px) {
    .resumen-financiero {
        padding: 15px 10px;
        border-radius: 8px;
    }
    
    .resumen-item {
        padding: 12px 0;
    }
    
    .resumen-label {
        font-size: 0.8rem;
    }
    
    .resumen-value {
        font-size: 1.1rem;
        padding: 6px 10px;
    }
    
    .resumen-item:last-child .resumen-value {
        font-size: 1.3rem;
        padding: 10px;
    }
}

/* Botones de acción */
.acciones-footer {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
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
    text-decoration: none;
}

.btn-volver {
    color: #28a745;
    border-color: #28a745;
    background: transparent;
}

.btn-volver:hover {
    background: #28a745;
    color: white;
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

/* Documento Section */
.documento-section {
    background: #e7f3ff;
    border-left: 4px solid #0d6efd;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.documento-titulo {
    font-weight: 600;
    color: #004085;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.documento-contenido {
    color: #004085;
    line-height: 1.6;
}

.documento-link {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
}

.documento-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .entrada-header-title {
        font-size: 1.5rem;
    }
    
    .acciones-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .entrada-view-container {
        padding: 15px;
    }
    
    .section-card {
        padding: 20px 15px;
    }
}
</style>

<div class="entrada-view-container">

    <!-- HEADER -->
    <div class="entrada-header">
        <div class="entrada-header-title">
            <i class="bi bi-box-arrow-in-down"></i>
            <?= Html::encode($titulo) ?>
        </div>
        <div class="entrada-header-subtitle">
            <i class="bi bi-calendar-event"></i>
            Entrada registrada el <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?>
            <?php if ($model->nro_documento): ?>
                <span class="badge-entrada">
                    <i class="bi bi-file-text"></i>
                    Doc: <?= Html::encode($model->nro_documento) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- INFORMACIÓN DEL PRODUCTO -->
    <?php if ($producto): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-box-seam"></i>
            Información del Producto
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Producto</div>
                    <div class="info-value">
                        <?php
                        $nombreProducto = implode(' - ', array_filter([
                            $producto->marca,
                            $producto->modelo,
                            $producto->color
                        ]));
                        if (empty($nombreProducto)) {
                            $nombreProducto = 'Producto #' . $producto->id;
                        }
                        ?>
                        <?= Html::encode($nombreProducto) ?>
                    </div>
                </div>
            </div>

            <?php if ($producto->descripcion): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-chat-left-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Descripción</div>
                    <div class="info-value"><?= Html::encode($producto->descripcion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($producto->sku): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-upc-scan"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">SKU</div>
                    <div class="info-value"><?= Html::encode($producto->sku) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($producto->codigo_barra): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-upc"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Código de Barra</div>
                    <div class="info-value"><?= Html::encode($producto->codigo_barra) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($producto->contenido_neto && $producto->unidad_medida): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-rulers"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Contenido Neto</div>
                    <div class="info-value"><?= $producto->contenido_neto ?> <?= Html::encode($producto->unidad_medida) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($producto->categoria): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Categoría</div>
                    <div class="info-value"><?= Html::encode($producto->categoria->titulo) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- DETALLES DE LA ENTRADA -->
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-receipt"></i>
            Detalles de la Entrada
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-hash"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">ID Entrada</div>
                    <div class="info-value">#<?= $model->id ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Fecha de Entrada</div>
                    <div class="info-value"><?= Yii::$app->formatter->asDate($model->created_at) ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Cantidad</div>
                    <div class="info-value"><?= number_format($model->cantidad) ?> unidades</div>
                </div>
            </div>

            <?php if ($model->nro_documento): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Número de Comprobante</div>
                    <div class="info-value"><?= Html::encode($model->nro_documento) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($model->ruta_documento_respaldo): ?>
        <div class="documento-section">
            <div class="documento-titulo">
                <i class="bi bi-paperclip"></i>
                Documento de Respaldo
            </div>
            <div class="documento-contenido">
                <a href="<?= Url::to(['@web/' . $model->ruta_documento_respaldo]) ?>" target="_blank" class="documento-link">
                    <i class="bi bi-download"></i>
                    Ver documento
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- INFORMACIÓN DEL PROVEEDOR -->
    <?php if ($proveedor): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-building"></i>
            Información del Proveedor
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Razón Social</div>
                    <div class="info-value"><?= Html::encode($proveedor->razon_social) ?></div>
                </div>
            </div>

            <?php if ($proveedor->documento_identificacion): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-card-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Documento</div>
                    <div class="info-value"><?= Html::encode($proveedor->documento_identificacion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($proveedor->telefono): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-telephone"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value"><?= Html::encode($proveedor->telefono) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($proveedor->ciudad || $proveedor->pais): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Ubicación</div>
                    <div class="info-value">
                        <?= Html::encode(implode(', ', array_filter([$proveedor->ciudad, $proveedor->pais]))) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- INFORMACIÓN DEL LUGAR/ALMACÉN -->
    <?php if ($lugar): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-geo-alt"></i>
            Almacén Destino
        </h3>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-house-door"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Nombre del Almacén</div>
                    <div class="info-value"><?= Html::encode($lugar->nombre) ?></div>
                </div>
            </div>

            <?php if ($lugar->descripcion): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-chat-left-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Descripción</div>
                    <div class="info-value"><?= Html::encode($lugar->descripcion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($lugar->ubicacion): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Ubicación</div>
                    <div class="info-value"><?= Html::encode($lugar->ubicacion) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- RESUMEN FINANCIERO -->
    <?php if ($producto): ?>
    <div class="section-card">
        <h3 class="section-title">
            <i class="bi bi-calculator"></i>
            Resumen Financiero
        </h3>
        
        <div class="resumen-financiero">
            <div class="resumen-item">
                <span class="resumen-label">Costo Unitario:</span>
                <span class="resumen-value">$<?= number_format($producto->costo, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Precio de Venta Unitario:</span>
                <span class="resumen-value">$<?= number_format($producto->precio_venta, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Cantidad Ingresada:</span>
                <span class="resumen-value"><?= number_format($model->cantidad) ?> unidades</span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Costo Total de la Entrada:</span>
                <span class="resumen-value">$<?= number_format($costoTotal, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Valor Total de Venta:</span>
                <span class="resumen-value">$<?= number_format($valorVenta, 2) ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-label">Margen de Ganancia:</span>
                <span class="resumen-value">$<?= number_format($valorVenta - $costoTotal, 2) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- BOTONES DE ACCIÓN -->
    <div class="acciones-footer">
        <a href="<?= Url::to(['index']) ?>" class="btn-action btn-volver">
            <i class="bi bi-arrow-left"></i>
            Volver al Listado
        </a>
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn-action btn-editar">
            <i class="bi bi-pencil"></i>
            Editar Entrada
        </a>
        <a href="<?= Url::to(['delete', 'id' => $model->id]) ?>" class="btn-action btn-borrar" 
           data-confirm="¿Está seguro que desea eliminar esta entrada?" data-method="post">
            <i class="bi bi-trash"></i>
            Eliminar Entrada
        </a>
    </div>

</div>
