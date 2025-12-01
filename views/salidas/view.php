<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\HistoricoMovimientos;
use app\models\Stock;

/** @var yii\web\View $this */
/** @var app\models\Salidas $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$producto = $model->producto;
$nombreProducto = '';
if ($producto) {
    $partes = array_filter([$producto->marca, $producto->descripcion]);
    $nombreProducto = implode(' ', $partes) ?: 'Producto #' . $model->id_producto;
} else {
    $nombreProducto = 'Producto no disponible';
}

$this->title = 'Detalle de Salida #' . $model->id;

// Histórico de movimientos
$historicoMovimientos = HistoricoMovimientos::find()
    ->where(['id_producto' => $model->id_producto])
    ->with(['producto', 'lugarOrigen', 'lugarDestino'])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(10)
    ->all();

// Stock actual
$stocks = Stock::find()
    ->where(['id_producto' => $model->id_producto])
    ->with(['lugar'])
    ->orderBy(['cantidad' => SORT_DESC])
    ->all();

$stockTotal = 0;
$stockPorLugar = [];
foreach ($stocks as $stock) {
    $stockTotal += $stock->cantidad;
    if ($stock->lugar) {
        $stockPorLugar[] = [
            'nombre' => $stock->lugar->nombre,
            'cantidad' => $stock->cantidad
        ];
    }
}

// Cálculos financieros
$valorStockCosto = $producto ? ($stockTotal * $producto->costo) : 0;
$valorStockVenta = $producto ? ($stockTotal * $producto->precio_venta) : 0;
$gananciaPotencial = $valorStockVenta - $valorStockCosto;
$margenGanancia = $valorStockCosto > 0 ? (($gananciaPotencial / $valorStockCosto) * 100) : 0;
?>

<style>
.container-view { max-width: 1400px; margin: 0 auto; padding: 20px; }
.header-salida { background: #546e7a; border-radius: 12px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.header-content { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
.header-info h1 { margin: 0 0 10px 0; font-size: 2rem; font-weight: 700; }
.header-info p { margin: 5px 0; opacity: 0.9; font-size: 1.1rem; }
.header-badge { background: rgba(255, 255, 255, 0.2); padding: 10px 20px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
.info-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); transition: transform 0.2s, box-shadow 0.2s; }
.info-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); }
.info-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
.info-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; background: #546e7a; }
.info-title { font-size: 0.85rem; color: #6c757d; font-weight: 600; text-transform: uppercase; }
.info-value { font-size: 1.8rem; font-weight: 700; color: #2c3e50; margin: 10px 0; }
.info-subtitle { font-size: 0.9rem; color: #6c757d; }
.section { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); margin-bottom: 30px; }
.section-separator { padding: 40px 0; border-top: 2px solid #e9ecef; }
.section-title { font-size: 1.5rem; font-weight: 700; color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.section-title i { color: #546e7a; }
.finanzas-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
.finanzas-card { background: #f8f9fa; border-radius: 10px; padding: 20px; text-align: center; }
.finanzas-label { font-size: 0.8rem; color: #6c757d; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
.finanzas-value { font-size: 1.5rem; font-weight: 700; color: #2c3e50; }
.finanzas-value.positivo { color: #28a745; }
.finanzas-conversions { margin-top: 10px; padding-top: 10px; border-top: 1px solid #dee2e6; }
.finanzas-conversions .conversion-line { font-size: 0.7rem; color: #6c757d; display: block; margin-top: 3px; }
.finanzas-conversions .conversion-line strong { color: #495057; font-weight: 600; }
.chart-container { position: relative; height: 300px; display: flex; justify-content: center; align-items: center; }
.historico-lista { list-style: none; padding: 0; margin: 0; }
.historico-item { padding: 20px; border-left: 4px solid #e9ecef; margin-bottom: 15px; background: #f8f9fa; border-radius: 8px; transition: all 0.2s; }
.historico-item:hover { background: #e9ecef; border-left-color: #546e7a; transform: translateX(5px); }
.historico-item.entrada { border-left-color: #28a745; }
.historico-item.salida { border-left-color: #546e7a; }
.historico-item.venta { border-left-color: #007bff; }
.historico-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.historico-accion { font-weight: 700; font-size: 1.1rem; color: #2c3e50; }
.historico-cantidad { background: white; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
.historico-detalles { color: #6c757d; font-size: 0.9rem; line-height: 1.6; }
.historico-fecha { color: #adb5bd; font-size: 0.8rem; margin-top: 8px; }
.no-data { text-align: center; padding: 40px; color: #6c757d; }
.no-data i { font-size: 3rem; margin-bottom: 15px; color: #adb5bd; }
.actions-footer { display: flex; gap: 15px; justify-content: center; }
.btn-action { padding: 12px 30px; font-size: 1rem; font-weight: 600; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
@media (max-width: 768px) {
    .header-content { flex-direction: column; align-items: flex-start; }
    .info-grid, .finanzas-grid { grid-template-columns: 1fr; }
    .actions-footer { flex-direction: column; }
    .btn-action { width: 100%; justify-content: center; }
    .section-separator { padding: 30px 0; }
}
</style>

<div class="container-view">
    <div class="mb-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver a Salidas', ['index'], [
            'class' => 'btn btn-outline-secondary btn-sm fw-bold',
            'style' => 'border-radius: 2rem; padding: 8px 20px;'
        ]) ?>
    </div>

    <div class="header-salida">
        <div class="header-content">
            <div class="header-info">
                <h1><i class="bi bi-box-arrow-up"></i> Salida #<?= $model->id ?></h1>
                <p><strong>Producto:</strong> <?= Html::encode($nombreProducto) ?></p>
                <p><strong>Tipo:</strong> <?= $model->is_movimiento == 1 ? 'Traspaso entre ubicaciones' : 'Descarte por caducación/faltante' ?></p>
            </div>
            <div class="header-badge">
                <i class="bi bi-calendar3"></i> <?= Yii::$app->formatter->asDate($model->created_at, 'dd/MM/yyyy') ?>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-icon"><i class="bi bi-box-seam"></i></div>
                <div class="info-title">Cantidad</div>
            </div>
            <div class="info-value"><?= Html::encode($model->cantidad) ?></div>
            <div class="info-subtitle">Unidades</div>
        </div>

        <?php if ($model->is_movimiento == 1): ?>
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="info-title">Origen</div>
                </div>
                <div class="info-value" style="font-size: 1.3rem;">
                    <?= $model->lugarOrigen ? Html::encode($model->lugarOrigen->nombre) : 'No especificado' ?>
                </div>
                <div class="info-subtitle">Ubicación original</div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-icon"><i class="bi bi-geo-fill"></i></div>
                    <div class="info-title">Destino</div>
                </div>
                <div class="info-value" style="font-size: 1.3rem;">
                    <?= $model->lugarDestino ? Html::encode($model->lugarDestino->nombre) : 'No especificado' ?>
                </div>
                <div class="info-subtitle">Nueva ubicación</div>
            </div>
        <?php else: ?>
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-icon"><i class="bi bi-trash"></i></div>
                    <div class="info-title">Descartado desde</div>
                </div>
                <div class="info-value" style="font-size: 1.3rem;">
                    <?= $model->lugarOrigen ? Html::encode($model->lugarOrigen->nombre) : 'No especificado' ?>
                </div>
                <div class="info-subtitle">Ubicación de descarte</div>
            </div>
        <?php endif; ?>

        <div class="info-card">
            <div class="info-card-header">
                <div class="info-icon"><i class="bi bi-boxes"></i></div>
                <div class="info-title">Stock Actual Total</div>
            </div>
            <div class="info-value"><?= $stockTotal ?></div>
            <div class="info-subtitle">En todas las ubicaciones</div>
        </div>
    </div>

    <div class="section-separator">
        <h3 class="section-title"><i class="bi bi-cash-stack"></i> Estados Financieros del Producto</h3>
        <div class="finanzas-grid">
            <div class="finanzas-card">
                <div class="finanzas-label">Valor en Costo</div>
                <div class="finanzas-value">$<?= number_format($valorStockCosto, 2) ?></div>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $valorCostoVes = $valorStockCosto * $precioParalelo->precio_ves;
                    $valorCostoUsdOficial = $valorCostoVes / $precioOficial->precio_ves;
                    ?>
                    <div class="finanzas-conversions">
                        <span class="conversion-line">Bs. <?= number_format($valorCostoVes, 2, ',', '.') ?></span>
                        <span class="conversion-line"><strong>$<?= number_format($valorCostoUsdOficial, 2) ?></strong> (BCV)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="finanzas-card">
                <div class="finanzas-label">Valor en Venta</div>
                <div class="finanzas-value">$<?= number_format($valorStockVenta, 2) ?></div>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $valorVentaVes = $valorStockVenta * $precioParalelo->precio_ves;
                    $valorVentaUsdOficial = $valorVentaVes / $precioOficial->precio_ves;
                    ?>
                    <div class="finanzas-conversions">
                        <span class="conversion-line">Bs. <?= number_format($valorVentaVes, 2, ',', '.') ?></span>
                        <span class="conversion-line"><strong>$<?= number_format($valorVentaUsdOficial, 2) ?></strong> (BCV)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="finanzas-card">
                <div class="finanzas-label">Ganancia Potencial</div>
                <div class="finanzas-value <?= $gananciaPotencial >= 0 ? 'positivo' : '' ?>">
                    $<?= number_format($gananciaPotencial, 2) ?>
                </div>
                
                <?php if ($precioParalelo && $precioOficial): ?>
                    <?php 
                    $gananciaPotencialVes = $gananciaPotencial * $precioParalelo->precio_ves;
                    $gananciaPotencialUsdOficial = $gananciaPotencialVes / $precioOficial->precio_ves;
                    ?>
                    <div class="finanzas-conversions">
                        <span class="conversion-line">Bs. <?= number_format($gananciaPotencialVes, 2, ',', '.') ?></span>
                        <span class="conversion-line"><strong>$<?= number_format($gananciaPotencialUsdOficial, 2) ?></strong> (BCV)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="finanzas-card">
                <div class="finanzas-label">Margen de Ganancia</div>
                <div class="finanzas-value <?= $margenGanancia >= 0 ? 'positivo' : '' ?>">
                    <?= number_format($margenGanancia, 1) ?>%
                </div>
            </div>
        </div>
    </div>

    <div class="section-separator">
        <h3 class="section-title"><i class="bi bi-pie-chart-fill"></i> Distribución de Stock por Ubicación</h3>
        <?php if (!empty($stockPorLugar)): ?>
            <div class="chart-container">
                <canvas id="stockChart"></canvas>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="bi bi-inbox"></i>
                <p>No hay stock disponible para este producto</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-separator">
        <h3 class="section-title"><i class="bi bi-clock-history"></i> Histórico de Movimientos del Producto</h3>
        <?php if (!empty($historicoMovimientos)): ?>
            <ul class="historico-lista">
                <?php foreach ($historicoMovimientos as $movimiento): ?>
                    <?php
                    $accionClase = '';
                    $accionTexto = '';
                    $accionIcon = '';
                    
                    if ($movimiento->isAccionEntrada()) {
                        $accionClase = 'entrada';
                        $accionTexto = 'ENTRADA';
                        $accionIcon = 'bi-box-arrow-in-down';
                    } elseif ($movimiento->isAccionSalida()) {
                        $accionClase = 'salida';
                        $accionTexto = 'SALIDA';
                        $accionIcon = 'bi-box-arrow-up';
                    } elseif ($movimiento->isAccionVenta()) {
                        $accionClase = 'venta';
                        $accionTexto = 'VENTA';
                        $accionIcon = 'bi-cart-check';
                    }
                    ?>
                    <li class="historico-item <?= $accionClase ?>">
                        <div class="historico-header">
                            <div class="historico-accion">
                                <i class="bi <?= $accionIcon ?>"></i> <?= $accionTexto ?>
                            </div>
                            <div class="historico-cantidad"><?= $movimiento->cantidad ?> unidades</div>
                        </div>
                        <div class="historico-detalles">
                            <?php if ($movimiento->lugarOrigen && $movimiento->lugarDestino): ?>
                                <i class="bi bi-arrow-left-right"></i>
                                De: <strong><?= Html::encode($movimiento->lugarOrigen->nombre) ?></strong> 
                                → A: <strong><?= Html::encode($movimiento->lugarDestino->nombre) ?></strong>
                            <?php elseif ($movimiento->lugarOrigen): ?>
                                <i class="bi bi-geo-alt"></i>
                                Desde: <strong><?= Html::encode($movimiento->lugarOrigen->nombre) ?></strong>
                            <?php elseif ($movimiento->lugarDestino): ?>
                                <i class="bi bi-geo"></i>
                                Hacia: <strong><?= Html::encode($movimiento->lugarDestino->nombre) ?></strong>
                            <?php endif; ?>
                            <?php if ($movimiento->referencia_id): ?>
                                <br><i class="bi bi-link-45deg"></i> Referencia: #<?= $movimiento->referencia_id ?>
                            <?php endif; ?>
                        </div>
                        <div class="historico-fecha">
                            <i class="bi bi-clock"></i>
                            <?= Yii::$app->formatter->asDatetime($movimiento->created_at) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="no-data">
                <i class="bi bi-inbox"></i>
                <p>No hay movimientos registrados para este producto</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="actions-footer">
        <?= Html::a('<i class="bi bi-eye"></i> Ver Producto', 
            ['productos/view', 'id' => $model->id_producto], 
            ['class' => 'btn btn-primary btn-action']) ?>
        <?= Html::a('<i class="bi bi-arrow-repeat"></i> Nueva Salida', 
            ['create'], 
            ['class' => 'btn btn-success btn-action']) ?>
        <?= Html::a('<i class="bi bi-list-ul"></i> Todas las Salidas', 
            ['index'], 
            ['class' => 'btn btn-secondary btn-action']) ?>
    </div>
</div>

<?php
// Gráfico de Chart.js
if (!empty($stockPorLugar)) {
    $stockLabels = json_encode(array_column($stockPorLugar, 'nombre'));
    $stockData = json_encode(array_column($stockPorLugar, 'cantidad'));
    $colores = ['#546e7a', '#78909c', '#90a4ae', '#b0bec5', '#cfd8dc', '#607d8b', '#455a64', '#37474f'];
    $coloresJson = json_encode(array_slice($colores, 0, count($stockPorLugar)));

    $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
        'position' => \yii\web\View::POS_END
    ]);

    $js = <<<JS
(function() {
    function initChart() {
        const ctx = document.getElementById('stockChart');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: $stockLabels,
                    datasets: [{
                        label: 'Stock por Ubicación',
                        data: $stockData,
                        backgroundColor: $coloresJson,
                        hoverOffset: 10,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 15, font: { size: 12, weight: '600' } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    label += context.parsed + ' unidades';
                                    let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((context.parsed / sum) * 100).toFixed(1);
                                    label += ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else if (typeof Chart === 'undefined') {
            setTimeout(initChart, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
})();
JS;
    $this->registerJs($js, \yii\web\View::POS_END);
}
?>
