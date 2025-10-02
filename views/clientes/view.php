<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = $model->nombre;
\yii\web\YiiAsset::register($this);

// Obtener las facturas del cliente
$facturas = $model->getFacturas()
    ->with(['itemsFacturas', 'historicoCobros'])
    ->orderBy(['fecha' => SORT_DESC])
    ->all();

// Obtener el histórico de cobros
$historicoCobros = $model->getHistoricoCobros()
    ->with(['factura'])
    ->orderBy(['fecha' => SORT_DESC])
    ->limit(15)
    ->all();

// Obtener las salidas del cliente
$salidas = $model->getSalidas()
    ->with(['producto'])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(10)
    ->all();

// Calcular estadísticas
$totalFacturas = count($facturas);
$montoTotalFacturas = 0;
$facturasPendientes = 0;
$facturasCerradas = 0;

foreach ($facturas as $factura) {
    $montoTotalFacturas += $factura->monto_final;
    
    // Calcular total cobrado de esta factura
    $totalCobradoFactura = 0;
    foreach ($factura->historicoCobros as $cobro) {
        $totalCobradoFactura += $cobro->monto;
    }
    
    // Determinar si está cerrada
    if ($totalCobradoFactura >= $factura->monto_final) {
        $facturasCerradas++;
    } else {
        $facturasPendientes++;
    }
}

$totalCobros = 0;
foreach ($historicoCobros as $cobro) {
    $totalCobros += $cobro->monto;
}

$saldoPendiente = $montoTotalFacturas - $totalCobros;

// Registrar CSS personalizado
$this->registerCss('
.cliente-view-container { max-width: 1400px; margin: 0 auto; padding: 20px; }

.cliente-header { background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-bottom: 30px; padding: 40px; color: white; text-align: center; }
.cliente-avatar { width: 120px; height: 120px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 3rem; border: 4px solid rgba(255, 255, 255, 0.3); }
.cliente-nombre { font-size: 2rem; font-weight: 700; margin-bottom: 10px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); }
.cliente-status-badge { display: inline-block; padding: 8px 20px; border-radius: 25px; font-size: 0.9rem; font-weight: 600; margin-top: 10px; }
.status-solvente-badge { background: rgba(40, 167, 69, 0.9); border: 2px solid rgba(255, 255, 255, 0.5); }
.status-moroso-badge { background: rgba(220, 53, 69, 0.9); border: 2px solid rgba(255, 255, 255, 0.5); }

.cliente-body { margin-bottom: 30px; }
.especificaciones-titulo { font-size: 1.5rem; font-weight: 600; margin-bottom: 20px; color: #2c3e50; }
.especificaciones-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
.especificacion-card { background: #ffffff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 15px; transition: transform 0.2s, box-shadow 0.2s; }
.especificacion-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.especificacion-icon { width: 50px; height: 50px; border-radius: 50%; background: #007bff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.especificacion-icon i { font-size: 1.5rem; color: #ffffff; }
.especificacion-content { flex: 1; min-width: 0; }
.especificacion-label { font-size: 0.85rem; color: #6c757d; font-weight: 500; text-transform: uppercase; margin-bottom: 4px; }
.especificacion-value { font-size: 1.1rem; color: #2c3e50; font-weight: 600; word-wrap: break-word; }
.especificacion-value.texto-largo { font-size: 0.95rem; line-height: 1.4; }

.estadisticas-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
.estadistica-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; transition: transform 0.2s, box-shadow 0.2s; }
.estadistica-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
.estadistica-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 1.8rem; }
.estadistica-icon.facturas { background: linear-gradient(135deg, #17a2b8, #138496); color: white; }
.estadistica-icon.cobros { background: linear-gradient(135deg, #28a745, #218838); color: white; }
.estadistica-icon.pendiente { background: linear-gradient(135deg, #ffc107, #e0a800); color: white; }
.estadistica-label { font-size: 0.9rem; color: #6c757d; margin-bottom: 8px; font-weight: 500; }
.estadistica-valor { font-size: 1.8rem; font-weight: 700; color: #2c3e50; }
.estadistica-conversions { margin-top: 8px; padding-top: 8px; border-top: 1px solid #e9ecef; }
.estadistica-conversions .conversion-line { font-size: 0.7rem; color: #6c757d; display: block; margin-top: 3px; }
.estadistica-conversions .conversion-line strong { color: #495057; font-weight: 600; }

.facturas-section, .cobros-section, .salidas-section { background: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
.facturas-titulo, .cobros-titulo, .salidas-titulo { font-size: 1.4rem; font-weight: 600; margin-bottom: 20px; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
.facturas-titulo i { color: #007bff; }
.cobros-titulo i { color: #28a745; }
.salidas-titulo i { color: #17a2b8; }

.facturas-lista, .cobros-lista, .salidas-lista { list-style: none; padding: 0; margin: 0; }
.factura-item, .cobro-item, .salida-item { 
    padding: 20px; 
    border-bottom: 1px solid #e9ecef; 
    transition: all 0.2s ease;
    border-radius: 8px;
    margin-bottom: 12px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.factura-item:last-child, .cobro-item:last-child, .salida-item:last-child { border-bottom: none; margin-bottom: 0; }
.factura-item:hover, .cobro-item:hover, .salida-item:hover { 
    background: #f8f9fa; 
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.factura-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.factura-info { flex: 1; }
.factura-codigo { font-weight: 700; color: #2c3e50; font-size: 1.1rem; margin-bottom: 4px; }
.factura-detalles { font-size: 0.9rem; color: #6c757d; line-height: 1.4; }
.factura-status-badge { 
    padding: 6px 14px; 
    border-radius: 20px; 
    font-size: 0.8rem; 
    font-weight: 600;
    white-space: nowrap;
}
.factura-status-cerrada { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.factura-status-abierta { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }

.factura-footer { display: flex; justify-content: space-between; align-items: center; }
.factura-monto-section { text-align: right; }
.factura-monto { 
    background: linear-gradient(135deg, #007bff, #0056b3); 
    color: white; 
    padding: 10px 18px; 
    border-radius: 25px; 
    font-size: 1rem; 
    font-weight: 700;
    display: inline-block;
    margin-bottom: 6px;
}
.factura-conversions { 
    font-size: 0.75rem; 
    color: #6c757d; 
    line-height: 1.3;
}

.cobro-monto-wrapper { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.cobro-conversions { font-size: 0.7rem; color: #6c757d; text-align: right; white-space: nowrap; }

.no-facturas, .no-cobros, .no-salidas { text-align: center; padding: 40px; color: #6c757d; }
.no-facturas i, .no-cobros i, .no-salidas i { font-size: 3rem; margin-bottom: 15px; color: #adb5bd; }

.cliente-footer { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; }
.btn-action { padding: 12px 30px; font-size: 1rem; font-weight: 600; border: 2px solid; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
.btn-editar { color: #0d6efd; border-color: #0d6efd; background: transparent; }
.btn-editar:hover { background: #0d6efd; color: white; }
.btn-borrar { color: #dc3545; border-color: #dc3545; background: transparent; }
.btn-borrar:hover { background: #dc3545; color: white; }

@media (max-width: 768px) {
    .especificaciones-grid, .estadisticas-grid { grid-template-columns: 1fr; }
    .cliente-footer { flex-direction: column; }
    .btn-action { width: 100%; justify-content: center; }
    .factura-item, .cobro-item, .salida-item { padding: 16px; margin-bottom: 10px; }
    .factura-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .factura-footer { flex-direction: column; align-items: flex-start; gap: 10px; }
    .factura-monto-section { text-align: left; width: 100%; }
}
');
?>

<div class="cliente-view-container">
    <h2><?= Html::encode($this->title) ?></h2>

    <div class="text-start mb-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Ver todos', Url::to(['clientes/index']), [
            'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
            'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
        ]) ?>
    </div>
    
    <!-- HEADER -->
    <div class="cliente-header">
        <div class="cliente-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <h1 class="cliente-nombre"><?= Html::encode($model->nombre) ?></h1>
        <div class="cliente-status-badge <?= $model->isStatusSolvente() ? 'status-solvente-badge' : 'status-moroso-badge' ?>">
            <?php if ($model->isStatusSolvente()): ?>
                <i class="bi bi-check-circle me-1"></i> Solvente
            <?php else: ?>
                <i class="bi bi-exclamation-triangle me-1"></i> Moroso
            <?php endif; ?>
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="estadisticas-grid">
        <div class="estadistica-card">
            <div class="estadistica-icon facturas">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="estadistica-label">Total Facturas</div>
            <div class="estadistica-valor"><?= $totalFacturas ?></div>
            <small class="text-muted">
                <?= $facturasCerradas ?> cerradas | <?= $facturasPendientes ?> abiertas
            </small>
        </div>

        <div class="estadistica-card">
            <div class="estadistica-icon cobros">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="estadistica-label">Total Cobrado</div>
            <div class="estadistica-valor">$<?= number_format($totalCobros, 2) ?></div>
            
            <?php if ($precioParalelo && $precioOficial): ?>
                <?php 
                $totalCobrosVes = $totalCobros * $precioParalelo->precio_ves;
                $totalCobrosUsdOficial = $totalCobrosVes / $precioOficial->precio_ves;
                ?>
                <div class="estadistica-conversions">
                    <span class="conversion-line">Bs. <?= number_format($totalCobrosVes, 2, ',', '.') ?></span>
                    <span class="conversion-line"><strong>$<?= number_format($totalCobrosUsdOficial, 2) ?></strong> (BCV)</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="estadistica-card">
            <div class="estadistica-icon pendiente">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="estadistica-label">Saldo Pendiente</div>
            <div class="estadistica-valor">$<?= number_format($saldoPendiente, 2) ?></div>
            
            <?php if ($precioParalelo && $precioOficial): ?>
                <?php 
                $saldoPendienteVes = $saldoPendiente * $precioParalelo->precio_ves;
                $saldoPendienteUsdOficial = $saldoPendienteVes / $precioOficial->precio_ves;
                ?>
                <div class="estadistica-conversions">
                    <span class="conversion-line">Bs. <?= number_format($saldoPendienteVes, 2, ',', '.') ?></span>
                    <span class="conversion-line"><strong>$<?= number_format($saldoPendienteUsdOficial, 2) ?></strong> (BCV)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ESPECIFICACIONES -->
    <div class="cliente-body">
        <h2 class="especificaciones-titulo">Información del Cliente</h2>
        
        <div class="especificaciones-grid">
            <?php if (!empty($model->documento_identidad)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-card-text"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Documento de Identidad</div>
                    <div class="especificacion-value"><?= Html::encode($model->documento_identidad) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($model->telefono)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-telephone-fill"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Teléfono</div>
                    <div class="especificacion-value"><?= Html::encode($model->telefono) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($model->ubicacion)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Ubicación</div>
                    <div class="especificacion-value texto-largo"><?= Html::encode($model->ubicacion) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($model->edad)): ?>
            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-person-badge"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Edad</div>
                    <div class="especificacion-value"><?= Html::encode($model->edad) ?> años</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-calendar-plus"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Fecha de Registro</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></div>
                </div>
            </div>

            <div class="especificacion-card">
                <div class="especificacion-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="especificacion-content">
                    <div class="especificacion-label">Última Actualización</div>
                    <div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></div>
                </div>
            </div>
        </div>

        <!-- FACTURAS -->
        <div class="facturas-section">
            <h3 class="facturas-titulo"><i class="bi bi-receipt"></i> Facturas del Cliente</h3>
            <?php if (!empty($facturas)): ?>
                <ul class="facturas-lista">
                    <?php foreach ($facturas as $factura): ?>
                        <?php
                        // Calcular total cobrado de esta factura
                        $totalCobradoFactura = 0;
                        foreach ($factura->historicoCobros as $cobro) {
                            $totalCobradoFactura += $cobro->monto;
                        }
                        $esCerrada = ($totalCobradoFactura >= $factura->monto_final);
                        ?>
                        <li class="factura-item" style="cursor: pointer;" onclick="window.location.href='<?= Url::to(['facturas/view', 'id' => $factura->id]) ?>';">
                            <!-- Header: Información y Estado -->
                            <div class="factura-header">
                                <div class="factura-info">
                                    <div class="factura-codigo">
                                        <i class="bi bi-file-text"></i> Factura: <?= Html::encode($factura->codigo) ?>
                                    </div>
                                    <div class="factura-detalles">
                                        Fecha: <?= Yii::$app->formatter->asDate($factura->fecha) ?> | Items: <?= count($factura->itemsFacturas) ?>
                                    </div>
                                </div>
                                <span class="factura-status-badge <?= $esCerrada ? 'factura-status-cerrada' : 'factura-status-abierta' ?>">
                                    <?= $esCerrada ? '<i class="bi bi-check-circle"></i> Cerrada' : '<i class="bi bi-clock"></i> Abierta' ?>
                                </span>
                            </div>
                            
                            <!-- Footer: Monto y conversiones -->
                            <div class="factura-footer">
                                <div></div> <!-- Espaciador -->
                                <div class="factura-monto-section">
                                    <div class="factura-monto">$<?= number_format($factura->monto_final, 2) ?></div>
                                    <?php if ($precioParalelo && $precioOficial): ?>
                                        <?php 
                                        $facturaVes = $factura->monto_final * $precioParalelo->precio_ves;
                                        $facturaUsdOficial = $facturaVes / $precioOficial->precio_ves;
                                        ?>
                                        <div class="factura-conversions">
                                            Bs. <?= number_format($facturaVes, 2, ',', '.') ?> | <strong>$<?= number_format($facturaUsdOficial, 2) ?></strong> (BCV)
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-facturas">
                    <i class="bi bi-inbox"></i>
                    <p>Este cliente no tiene facturas registradas aún.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- COBROS -->
        <div class="cobros-section">
            <h3 class="cobros-titulo"><i class="bi bi-cash-coin"></i> Histórico de Cobros</h3>
            <?php if (!empty($historicoCobros)): ?>
                <ul class="cobros-lista">
                    <?php foreach ($historicoCobros as $cobro): ?>
                        <li class="cobro-item">
                            <div class="cobro-info">
                                <div class="cobro-fecha">
                                    <i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDate($cobro->fecha) ?>
                                </div>
                                <div class="cobro-detalles">
                                    <?php if ($cobro->factura): ?>
                                        Factura: <?= Html::encode($cobro->factura->codigo) ?>
                                        <?php if ($cobro->metodo_pago): ?> | Método: <?= Html::encode($cobro->metodo_pago) ?><?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="cobro-monto-wrapper">
                                <span class="cobro-monto">$<?= number_format($cobro->monto, 2) ?></span>
                                <?php if ($precioParalelo && $precioOficial): ?>
                                    <?php 
                                    $cobroVes = $cobro->monto * $precioParalelo->precio_ves;
                                    $cobroUsdOficial = $cobroVes / $precioOficial->precio_ves;
                                    ?>
                                    <div class="cobro-conversions">
                                        Bs. <?= number_format($cobroVes, 2, ',', '.') ?> | <strong>$<?= number_format($cobroUsdOficial, 2) ?></strong> (BCV)
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-cobros">
                    <i class="bi bi-inbox"></i>
                    <p>No hay registros de cobros para este cliente.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- SALIDAS -->
        <div class="salidas-section">
            <h3 class="salidas-titulo"><i class="bi bi-box-arrow-right"></i> Salidas Registradas</h3>
            <?php if (!empty($salidas)): ?>
                <ul class="salidas-lista">
                    <?php foreach ($salidas as $salida): ?>
                        <li class="salida-item">
                            <div class="salida-info">
                                <div class="salida-fecha">
                                    <i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDatetime($salida->created_at) ?>
                                </div>
                                <div class="salida-detalles">
                                    <?php if ($salida->producto): ?>
                                        Producto: <?= Html::encode($salida->producto->marca . ' ' . $salida->producto->modelo) ?>
                                    <?php endif; ?>
                                    <?php if ($salida->is_movimiento): ?>
                                        <span class="badge bg-info text-white ms-2">Movimiento</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="salida-monto"><?= $salida->cantidad ?> unidades</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-salidas">
                    <i class="bi bi-inbox"></i>
                    <p>No hay salidas registradas para este cliente.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="cliente-footer">
        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-action btn-editar">
            <i class="bi bi-pencil-square"></i> Editar
        </a>
        <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display: inline;']) ?>
            <button type="submit" class="btn btn-action btn-borrar" onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">
                <i class="bi bi-trash"></i> Borrar
            </button>
        <?= Html::endForm() ?>
    </div>
</div>
