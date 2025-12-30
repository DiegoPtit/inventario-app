<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $datosReporte */
/** @var string $tipo */
/** @var string $fecha */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = 'Reporte de Inventario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .header .fecha {
            font-size: 11px;
            color: #666;
        }
        
        .seccion-lugar {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .seccion-lugar h2 {
            font-size: 16px;
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #333;
        }
        
        .productos-lista {
            margin-bottom: 20px;
        }
        
        .producto-item {
            padding: 10px;
            margin-bottom: 8px;
            border: 1px solid #ddd;
            background-color: #fafafa;
        }
        
        .producto-item:nth-child(odd) {
            background-color: #fff;
        }
        
        .producto-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
            color: #333;
        }
        
        .producto-detalles {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            font-size: 11px;
            color: #555;
        }
        
        .producto-detalles .detalle-item {
            padding: 3px 0;
        }
        
        .producto-detalles .detalle-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }
        
        .footer-seccion {
            background-color: #e8e8e8;
            padding: 12px;
            border-top: 2px solid #333;
            margin-top: 10px;
        }
        
        .footer-seccion h3 {
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .footer-totales {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .total-item {
            text-align: center;
        }
        
        .total-item .total-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .total-item .total-valor {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }
        
        .conversion-text {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
            line-height: 1.2;
        }
        
        .conversion-text .conversion-line {
            display: block;
        }
        
        .conversion-text strong {
            color: #333;
            font-weight: 600;
        }
        
        .sin-datos {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .seccion-lugar {
                page-break-inside: avoid;
            }
            
            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <!-- Membrete -->
    <div class="header">
        <h1>Hava Inventario</h1>
        <div class="fecha">
            <strong>
                <?php
                if ($tipo === 'pasivos') {
                    echo 'Listado de Pasivos';
                } elseif ($tipo === 'por-lugar') {
                    echo 'Reporte de Inventario por Almacén';
                } else {
                    echo 'Reporte de Inventario General';
                }
                ?>
            </strong><br>
            Fecha de generación: <?= Html::encode($fecha) ?>
        </div>
    </div>

    <!-- Cuerpo del reporte -->
    <?php if (empty($datosReporte)): ?>
        <div class="sin-datos">
            <p>No hay datos disponibles para generar el reporte.</p>
        </div>
    <?php else: ?>
        <?php if ($tipo === 'pasivos'): ?>
            <!-- Reporte de Pasivos -->
            <?php foreach ($datosReporte as $seccion): ?>
                <div class="seccion-lugar">
                    <!-- Header de la factura con cliente -->
                    <h2>👤 <?= Html::encode($seccion['cliente'] ? $seccion['cliente']->nombre : 'Cliente sin nombre') ?></h2>
                    
                    <!-- Info de la factura -->
                    <div style="background-color: #f8f9fa; padding: 10px; margin-bottom: 15px; border-left: 4px solid #dc3545;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 11px;">
                            <div>
                                <strong>Factura:</strong> <?= Html::encode($seccion['factura']->codigo) ?>
                            </div>
                            <div>
                                <strong>Fecha:</strong> <?= Html::encode(date('d/m/Y', strtotime($seccion['factura']->fecha))) ?>
                            </div>
                            <div>
                                <strong>Estado:</strong> 
                                <span style="color: #dc3545; font-weight: bold;">Pendiente</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de productos -->
                    <div class="productos-lista">
                        <?php foreach ($seccion['productos'] as $item): ?>
                            <?php $producto = $item['producto']; ?>
                            <div class="producto-item">
                                <div class="producto-header">
                                    <?php
                                    $nombreProducto = trim(implode(' - ', array_filter([
                                        $producto->marca,
                                        $producto->modelo,
                                        $producto->color
                                    ])));
                                    echo Html::encode($nombreProducto ?: 'Producto sin nombre');
                                    ?>
                                </div>
                                <div class="producto-detalles">
                                    <?php if ($producto->contenido_neto): ?>
                                        <div class="detalle-item">
                                            <span class="detalle-label">Contenido Neto:</span>
                                            <?= Html::encode(number_format($producto->contenido_neto, 2)) ?>
                                            <?= Html::encode($producto->unidad_medida ?: '') ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="detalle-item">
                                        <span class="detalle-label">Precio Unitario:</span>
                                        $<?= number_format($item['precio_unitario'], 2) ?>
                                    </div>
                                    
                                    <div class="detalle-item">
                                        <span class="detalle-label">Cantidad:</span>
                                        <?= Html::encode($item['cantidad']) ?> unidades
                                    </div>
                                    
                                    <div class="detalle-item">
                                        <span class="detalle-label">Subtotal:</span>
                                        $<?= number_format($item['subtotal'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Footer de la factura -->
                    <div class="footer-seccion">
                        <h3>Resumen de Factura</h3>
                        <div class="footer-totales">
                            <div class="total-item">
                                <div class="total-label">Monto Total</div>
                                <div class="total-valor">
                                    <?= Html::encode($seccion['currency']) ?> 
                                    <?= number_format($seccion['monto_factura'], 2) ?>
                                </div>
                            </div>
                            <div class="total-item">
                                <div class="total-label">Monto Cobrado</div>
                                <div class="total-valor" style="color: #28a745;">
                                    <?= Html::encode($seccion['currency']) ?> 
                                    <?= number_format($seccion['monto_cobrado'], 2) ?>
                                </div>
                            </div>
                            <div class="total-item">
                                <div class="total-label">Monto Pendiente</div>
                                <div class="total-valor" style="color: #dc3545;">
                                    <?= Html::encode($seccion['currency']) ?> 
                                    <?= number_format($seccion['monto_pendiente'], 2) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($datosReporte) > 1): ?>
                <!-- Totales generales para pasivos -->
                <div class="seccion-lugar">
                    <div class="footer-seccion" style="background-color: #d0d0d0;">
                        <h3>TOTALES GENERALES DE PASIVOS</h3>
                        <div style="text-align: center; margin-top: 10px;">
                            <?php
                            // Agrupar por moneda
                            $totalesPorMoneda = [];
                            foreach ($datosReporte as $seccion) {
                                $currency = $seccion['currency'];
                                if (!isset($totalesPorMoneda[$currency])) {
                                    $totalesPorMoneda[$currency] = [
                                        'total' => 0,
                                        'cobrado' => 0,
                                        'pendiente' => 0,
                                    ];
                                }
                                $totalesPorMoneda[$currency]['total'] += $seccion['monto_factura'];
                                $totalesPorMoneda[$currency]['cobrado'] += $seccion['monto_cobrado'];
                                $totalesPorMoneda[$currency]['pendiente'] += $seccion['monto_pendiente'];
                            }
                            ?>
                            <?php foreach ($totalesPorMoneda as $currency => $totales): ?>
                                <div style="margin-bottom: 15px;">
                                    <h4 style="margin-bottom: 8px; color: #333;"><?= Html::encode($currency) ?></h4>
                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                        <div>
                                            <small style="color: #666;">Total Facturado</small><br>
                                            <strong><?= number_format($totales['total'], 2) ?></strong>
                                        </div>
                                        <div>
                                            <small style="color: #666;">Total Cobrado</small><br>
                                            <strong style="color: #28a745;"><?= number_format($totales['cobrado'], 2) ?></strong>
                                        </div>
                                        <div>
                                            <small style="color: #666;">Total Pendiente</small><br>
                                            <strong style="color: #dc3545;"><?= number_format($totales['pendiente'], 2) ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Reporte de Inventario -->
            <?php foreach ($datosReporte as $seccion): ?>
            <div class="seccion-lugar">
                <!-- Header de la agrupación -->
                <h2>📍 <?= Html::encode($seccion['lugar']->nombre) ?></h2>
                
                <!-- Lista de productos -->
                <div class="productos-lista">
                    <?php foreach ($seccion['productos'] as $producto): ?>
                        <div class="producto-item">
                            <div class="producto-header">
                                <?php
                                $nombreProducto = trim(implode(' - ', array_filter([
                                    $producto['marca'],
                                    $producto['modelo'],
                                    $producto['color']
                                ])));
                                echo Html::encode($nombreProducto ?: 'Producto sin nombre');
                                ?>
                            </div>
                            <div class="producto-detalles">
                                <?php if ($producto['contenido_neto']): ?>
                                    <div class="detalle-item">
                                        <span class="detalle-label">Contenido Neto:</span>
                                        <?= Html::encode(number_format($producto['contenido_neto'], 2)) ?>
                                        <?= Html::encode($producto['unidad_medida'] ?: '') ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="detalle-item">
                                    <span class="detalle-label">Costo Unitario:</span>
                                    $<?= number_format($producto['costo'], 2) ?>
                                </div>
                                
                                <div class="detalle-item">
                                    <span class="detalle-label">Precio de Venta:</span>
                                    $<?= number_format($producto['precio_venta'], 2) ?>
                                </div>
                                
                                <div class="detalle-item">
                                    <span class="detalle-label">Cantidad en Stock:</span>
                                    <?= Html::encode($producto['cantidad']) ?> unidades
                                </div>
                                
                                <div class="detalle-item">
                                    <span class="detalle-label">Subtotal Costo:</span>
                                    $<?= number_format($producto['subtotal_costo'], 2) ?>
                                </div>
                                
                                <div class="detalle-item">
                                    <span class="detalle-label">Subtotal Venta:</span>
                                    $<?= number_format($producto['subtotal_venta'], 2) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Footer de la agrupación -->
                <div class="footer-seccion">
                    <h3><?= ($tipo === 'general') ? 'Totales Generales' : 'Totales del Almacén' ?></h3>
                    <div class="footer-totales">
                        <div class="total-item">
                            <div class="total-label">Total Costo</div>
                            <div class="total-valor">$<?= number_format($seccion['total_costo'], 2) ?></div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $totalCostoVes = $seccion['total_costo'] * $precioParalelo->precio_ves;
                                $totalCostoUsdOficial = $totalCostoVes / $precioOficial->precio_ves;
                                ?>
                                <div class="conversion-text">
                                    <span class="conversion-line">Bs. <?= number_format($totalCostoVes, 2, ',', '.') ?></span>
                                    <span class="conversion-line"><strong>$<?= number_format($totalCostoUsdOficial, 2) ?></strong> (BCV)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="total-item">
                            <div class="total-label">Total Precio de Venta</div>
                            <div class="total-valor">$<?= number_format($seccion['total_precio_venta'], 2) ?></div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $totalVentaVes = $seccion['total_precio_venta'] * $precioParalelo->precio_ves;
                                $totalVentaUsdOficial = $totalVentaVes / $precioOficial->precio_ves;
                                ?>
                                <div class="conversion-text">
                                    <span class="conversion-line">Bs. <?= number_format($totalVentaVes, 2, ',', '.') ?></span>
                                    <span class="conversion-line"><strong>$<?= number_format($totalVentaUsdOficial, 2) ?></strong> (BCV)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="total-item">
                            <div class="total-label">Cantidad Total en Stock</div>
                            <div class="total-valor"><?= Html::encode($seccion['total_cantidad']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if ($tipo === 'general' && count($datosReporte) > 1): ?>
            <!-- Totales generales si hay más de un almacén -->
            <div class="seccion-lugar">
                <div class="footer-seccion" style="background-color: #d0d0d0;">
                    <h3>TOTALES GENERALES</h3>
                    <div class="footer-totales">
                        <div class="total-item">
                            <div class="total-label">Total Costo General</div>
                            <div class="total-valor">
                                $<?= number_format(array_sum(array_column($datosReporte, 'total_costo')), 2) ?>
                            </div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $totalCostoGeneral = array_sum(array_column($datosReporte, 'total_costo'));
                                $totalCostoGeneralVes = $totalCostoGeneral * $precioParalelo->precio_ves;
                                $totalCostoGeneralUsdOficial = $totalCostoGeneralVes / $precioOficial->precio_ves;
                                ?>
                                <div class="conversion-text">
                                    <span class="conversion-line">Bs. <?= number_format($totalCostoGeneralVes, 2, ',', '.') ?></span>
                                    <span class="conversion-line"><strong>$<?= number_format($totalCostoGeneralUsdOficial, 2) ?></strong> (BCV)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="total-item">
                            <div class="total-label">Total Precio de Venta General</div>
                            <div class="total-valor">
                                $<?= number_format(array_sum(array_column($datosReporte, 'total_precio_venta')), 2) ?>
                            </div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $totalVentaGeneral = array_sum(array_column($datosReporte, 'total_precio_venta'));
                                $totalVentaGeneralVes = $totalVentaGeneral * $precioParalelo->precio_ves;
                                $totalVentaGeneralUsdOficial = $totalVentaGeneralVes / $precioOficial->precio_ves;
                                ?>
                                <div class="conversion-text">
                                    <span class="conversion-line">Bs. <?= number_format($totalVentaGeneralVes, 2, ',', '.') ?></span>
                                    <span class="conversion-line"><strong>$<?= number_format($totalVentaGeneralUsdOficial, 2) ?></strong> (BCV)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="total-item">
                            <div class="total-label">Cantidad Total General</div>
                            <div class="total-valor">
                                <?= Html::encode(array_sum(array_column($datosReporte, 'total_cantidad'))) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        // Abrir diálogo de impresión automáticamente
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
