<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Lugares $model */

$this->title = $model->nombre;
\yii\web\YiiAsset::register($this);

// Obtener los productos con stock en este lugar
$stocks = $model->getStocks()
    ->with(['producto', 'producto.categoria'])
    ->where(['>', 'cantidad', 0])
    ->orderBy(['cantidad' => SORT_DESC])
    ->all();
?>

<style>
.lugar-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header */
.lugar-header {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.header-placeholder {
    width: 100%;
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
}

.header-placeholder i {
    font-size: 8rem;
    color: #adb5bd;
}

/* Cuerpo - Especificaciones */
.lugar-body {
    margin-bottom: 30px;
}

.especificaciones-titulo {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
}

.especificaciones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.especificacion-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.especificacion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.especificacion-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #546e7a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.especificacion-icon i {
    font-size: 1.5rem;
    color: #ffffff;
}

.especificacion-content {
    flex: 1;
    min-width: 0;
}

.especificacion-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.especificacion-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    word-wrap: break-word;
}

.especificacion-value.texto-largo {
    font-size: 0.95rem;
    line-height: 1.4;
}

/* Lista de Productos */
.productos-section {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.productos-titulo {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.productos-titulo i {
    color: #546e7a;
}

.productos-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.producto-item {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s;
}

.producto-item:last-child {
    border-bottom: none;
}

.producto-item:hover {
    background: #f8f9fa;
}

.producto-info {
    flex: 1;
}

.producto-nombre {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.producto-detalles {
    font-size: 0.9rem;
    color: #6c757d;
}

.producto-cantidad {
    background: #546e7a;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.producto-cantidad.bajo {
    background: #dc3545;
}

.producto-cantidad.medio {
    background: #ffc107;
}

.no-productos {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-productos i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

/* Footer - Botones */
.lugar-footer {
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
    
    .lugar-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }

    .producto-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding: 20px 15px;
    }

    .producto-cantidad {
        align-self: flex-end;
    }

    .productos-titulo {
        font-size: 1.2rem;
        flex-wrap: wrap;
    }

    .productos-section {
        padding: 20px 15px;
    }
}

@media (max-width: 480px) {
    .productos-titulo {
        font-size: 1.1rem;
    }

    .producto-detalles {
        font-size: 0.85rem;
        line-height: 1.5;
    }
}
</style>

<div class="lugar-view-container">

    <h2><?= Html::encode($this->title) ?></h2>

    <div class="text-start mb-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Ver todos', Url::to(['lugares/index']), [
            'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
            'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
        ]) ?>
    </div>
    
    <!-- HEADER -->
    <div class="lugar-header">
        <div class="header-placeholder">
            <i class="bi bi-geo-alt"></i>
        </div>
    </div>

    <!-- CUERPO - ESPECIFICACIONES -->
    <div class="lugar-body">
        <h2 class="especificaciones-titulo">Información del Lugar</h2>
        
        <div class="especificaciones-grid">
            <!-- Nombre -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Nombre</div>
                    <div class="especificacion-value"><?= Html::encode($model->nombre) ?></div>
                </div>
            </div>

            <!-- Descripción -->
            <?php if (!empty($model->descripcion)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-card-text"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Descripción</div>
                    <div class="especificacion-value texto-largo"><?= Html::encode($model->descripcion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ubicación -->
            <?php if (!empty($model->ubicacion)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-pin-map-fill"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Ubicación</div>
                    <div class="especificacion-value"><?= Html::encode($model->ubicacion) ?></div>
                </div>
            </div>
            <?php endif; ?>

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

            <!-- Última Actualización -->
            <div class="especificacion-card">
                <div class="especificacion-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Última Actualización</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></div>
                </div>
            </div>
        </div>

        <!-- LISTA DE PRODUCTOS -->
        <div class="productos-section">
            <h3 class="productos-titulo">
                <i class="bi bi-box-seam"></i>
                Productos en este lugar
            </h3>
            
            <?php if (!empty($stocks)): ?>
                <ul class="productos-lista">
                    <?php foreach ($stocks as $stock): ?>
                        <?php
                        // Determinar clase de color basado en cantidad
                        $colorClase = '';
                        if ($stock->cantidad == 0) {
                            $colorClase = 'bajo';
                        } elseif ($stock->cantidad <= 10) {
                            $colorClase = 'medio';
                        }

                        // Crear nombre del producto
                        $nombreProducto = '';
                        if ($stock->producto) {
                            $partes = array_filter([
                                $stock->producto->marca,
                                $stock->producto->modelo,
                                $stock->producto->descripcion
                            ]);
                            $nombreProducto = implode(' - ', $partes);
                            if (empty($nombreProducto)) {
                                $nombreProducto = 'Producto #' . $stock->producto->id;
                            }
                        } else {
                            $nombreProducto = 'Producto no disponible';
                        }
                        ?>
                        <li class="producto-item">
                            <div class="producto-info">
                                <div class="producto-nombre">
                                    <i class="bi bi-box"></i>
                                    <?= Html::encode($nombreProducto) ?>
                                </div>
                                <div class="producto-detalles">
                                    <?php if ($stock->producto && $stock->producto->categoria): ?>
                                        Categoría: <?= Html::encode($stock->producto->categoria->titulo) ?> | 
                                    <?php endif; ?>
                                    <?php if ($stock->producto && $stock->producto->precio_venta): ?>
                                        Precio: $<?= number_format($stock->producto->precio_venta, 2) ?>
                                    <?php endif; ?>
                                    <?php if ($stock->producto && $stock->producto->sku): ?>
                                        | SKU: <?= Html::encode($stock->producto->sku) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="producto-cantidad <?= $colorClase ?>">
                                <?= $stock->cantidad ?> unidad<?= $stock->cantidad != 1 ? 'es' : '' ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-productos">
                    <i class="bi bi-inbox"></i>
                    <p>No hay productos registrados en este lugar.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER - BOTONES DE ACCIÓN -->
    <div class="lugar-footer">
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-action btn-editar">
            <i class="bi bi-pencil-square"></i>
            Editar
        </a>
        
        <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display: inline;']) ?>
            <button type="submit" class="btn btn-action btn-borrar" 
                    onclick="return confirm('¿Está seguro de que desea eliminar este lugar?');">
                <i class="bi bi-trash"></i>
                Borrar
            </button>
        <?= Html::endForm() ?>
    </div>

</div>
