<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Clientes;
use app\models\Facturas;
use app\models\HistoricoCobros;

/** @var yii\web\View $this */
/** @var app\models\HistoricoCobros $model */
/** @var yii\widgets\ActiveForm $form */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

// Obtener información del cliente y factura si existen
$cliente = null;
$factura = null;

if (!$model->isNewRecord) {
    $cliente = $model->cliente;
    $factura = $model->factura;
} else {
    // Si es nuevo registro, intentar obtener de parámetros GET
    if (isset($_GET['id_cliente'])) {
        $model->id_cliente = $_GET['id_cliente'];
        $cliente = Clientes::findOne($model->id_cliente);
    }
    if (isset($_GET['id_factura'])) {
        $model->id_factura = $_GET['id_factura'];
        $factura = Facturas::findOne($model->id_factura);
    }
}

// Opciones para método de pago
$metodosPago = [
    'EFECTIVO' => 'EFECTIVO',
    'PAGO MOVIL' => 'PAGO MOVIL',
    'OTRA FORMA EQUIVALENTE' => 'OTRA FORMA EQUIVALENTE'
];

// CSS personalizado para el formulario con pasos
$this->registerCss('
.historico-cobros-form,
.historico-cobros-form *,
.historico-cobros-form *::before,
.historico-cobros-form *::after {
    box-sizing: border-box;
}

.historico-cobros-form {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Información del Cliente/Factura */
.info-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    border-left: 4px solid #6c757d;
}

.info-box h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box p {
    margin: 5px 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.info-box strong {
    color: #212529;
}

.info-box .conversion-detail {
    font-size: 0.8rem;
    color: #868e96;
    margin-left: 8px;
    font-weight: 400;
}

.info-box .conversion-detail strong {
    color: #495057;
    font-weight: 600;
}

/* Stepper - Indicador de Pasos */
.stepper-container {
    margin-bottom: 40px;
}

.stepper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 30px;
}

.stepper::before {
    content: "";
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: #e9ecef;
    z-index: 0;
}

.stepper-progress {
    position: absolute;
    top: 25px;
    left: 0;
    height: 3px;
    background: #6c757d;
    z-index: 1;
    transition: width 0.3s ease;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #adb5bd;
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.step.active .step-circle {
    background: #495057;
    border-color: #495057;
    color: white;
    box-shadow: 0 2px 8px rgba(73, 80, 87, 0.3);
    transform: scale(1.05);
}

.step.completed .step-circle {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.step.completed .step-circle i {
    display: block;
}

.step-circle i {
    display: none;
}

.step-label {
    font-size: 0.85rem;
    color: #868e96;
    font-weight: 500;
    text-align: center;
    max-width: 150px;
}

.step.active .step-label {
    color: #495057;
    font-weight: 600;
}

.step.completed .step-label {
    color: #6c757d;
}

/* Secciones de Pasos */
.step-content {
    display: none;
    animation: fadeIn 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.step-content.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section {
    margin-bottom: 30px;
    width: 100%;
    box-sizing: border-box;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #6c757d;
    font-size: 1.5rem;
}

.section-description {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
    width: 100%;
    box-sizing: border-box;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: block;
    font-size: 0.95rem;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
    width: 100%;
    box-sizing: border-box;
}

/* Campo de Monto Destacado */
.monto-destacado {
    background: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    border: 2px solid #e9ecef;
}

.monto-icono {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 15px;
}

.monto-texto {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 15px;
}

.monto-destacado .form-control {
    max-width: 400px;
    margin: 0 auto;
    font-size: 2rem;
    text-align: center;
    height: 80px;
    font-weight: 700;
    color: #28a745;
    border-color: #28a745;
}

.monto-destacado label {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: #333;
}

/* Conversiones de Moneda */
.conversion-info {
    margin-top: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 10px;
    border: 1px solid rgba(40, 167, 69, 0.3);
    display: none;
}

.conversion-info.visible {
    display: block;
}

.conversion-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(40, 167, 69, 0.2);
}

.conversion-row:last-child {
    border-bottom: none;
}

.conversion-label {
    font-size: 0.85rem;
    color: #495057;
    font-weight: 500;
}

.conversion-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #28a745;
}

/* Currency Carousel Styles */
.currency-carousel {
    position: relative;
    overflow: hidden;
    min-height: 250px;
    touch-action: pan-y;
    padding: 15px 0;
}

.carousel-page-cobro {
    display: none;
    animation: fadeInCarousel 0.3s ease-in-out;
}

.carousel-page-cobro.active {
    display: block;
}

.input-with-badge-cobro {
    position: relative;
    width: 100%;
    margin: 20px 0;
}

.currency-badge-cobro {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: -25px;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 6px 16px;
    border-radius: 20px;
    color: white;
    z-index: 10;
    pointer-events: none;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.usdt-badge-cobro {
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);
}

.bcv-badge-cobro {
    background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.4);
}

.ves-badge-cobro {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.4);
}

.currency-label-cobro {
    font-size: 0.9rem;
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 15px;
    text-align: center;
    letter-spacing: 0.5px;
}

.carousel-controls-cobro {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 25px;
}

.carousel-nav-cobro {
    background: white;
    border: 3px solid #28a745;
    color: #28a745;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.carousel-nav-cobro:hover:not(:disabled) {
    background: #28a745;
    color: white;
    transform: scale(1.15);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.carousel-nav-cobro:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    border-color: #dee2e6;
    color: #adb5bd;
}

@keyframes fadeInCarousel {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Navegación de Pasos */
.step-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 40px;
    gap: 15px;
}

.btn-step {
    padding: 12px 30px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-previous {
    background: #6c757d;
    color: white;
}

.btn-previous:hover {
    background: #5a6268;
    transform: translateX(-3px);
}

.btn-next {
    background: #495057;
    color: white;
    margin-left: auto;
}

.btn-next:hover {
    background: #343a40;
    transform: translateX(3px);
}

.btn-submit-final {
    background: #495057;
    color: white;
    padding: 15px 40px;
    margin-left: auto;
}

.btn-submit-final:hover {
    background: #343a40;
    transform: translateY(-2px);
}

.btn-step:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.help-block {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .historico-cobros-form-container {
        padding: 20px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .stepper {
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .step {
        flex: 0 0 calc(50% - 10px);
    }
    
    .step-label {
        font-size: 0.75rem;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 0.9rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
    
    .step-navigation {
        flex-direction: column;
    }
    
    .btn-step {
        width: 100%;
        justify-content: center;
    }
    
    .btn-next,
    .btn-submit-final {
        margin-left: 0;
    }
    
    .monto-destacado .form-control {
        font-size: 1.5rem;
        height: 60px;
    }
}

/* Estilos para el formulario ActiveForm */
#historico-cobros-form {
    width: 100%;
    overflow: hidden;
}

/* Estilos para los campos generados por Yii2 */
#historico-cobros-form .form-group,
#historico-cobros-form div[class^="field-"],
#historico-cobros-form div[class*=" field-"] {
    width: 100%;
    box-sizing: border-box;
    max-width: 100%;
}
');
?>

<div class="historico-cobros-form">
    
    <!-- Información del Cliente y Factura -->
        <?php if ($cliente || $factura): ?>
            <div class="info-box">
                <h4>
                    <i class="bi bi-info-circle-fill"></i>
                    Información del Cobro
                </h4>
                <?php if ($cliente): ?>
                    <p><strong>Cliente:</strong> <?= Html::encode($cliente->nombre) ?></p>
                <?php endif; ?>
                <?php if ($factura): 
                    // Función helper para convertir entre monedas
                    $convertCurrency = function($amount, $fromCurrency, $toCurrency) use ($precioParalelo, $precioOficial) {
                        if (!$precioParalelo || !$precioOficial) {
                            return $amount;
                        }
                        
                        $value = floatval($amount);
                        
                        // Si son la misma moneda, retornar sin conversión
                        if ($fromCurrency === $toCurrency) {
                            return $value;
                        }
                        
                        // Convertir de fromCurrency a VES (moneda base)
                        $amountInVES = 0;
                        if ($fromCurrency === 'USDT') {
                            $amountInVES = $value * $precioParalelo->precio_ves;
                        } elseif ($fromCurrency === 'BCV') {
                            $amountInVES = $value * $precioOficial->precio_ves;
                        } elseif ($fromCurrency === 'VES') {
                            $amountInVES = $value;
                        }
                        
                        // Convertir de VES a toCurrency
                        if ($toCurrency === 'USDT') {
                            return $amountInVES / $precioParalelo->precio_ves;
                        } elseif ($toCurrency === 'BCV') {
                            return $amountInVES / $precioOficial->precio_ves;
                        } elseif ($toCurrency === 'VES') {
                            return $amountInVES;
                        }
                        
                        return $value;
                    };
                    
                    // Función helper para formatear moneda
                    $formatCurrency = function($amount, $currency) {
                        if ($currency === 'VES') {
                            return 'Bs. ' . number_format($amount, 2, ',', '.');
                        } else {
                            return '$' . number_format($amount, 2);
                        }
                    };
                    
                    // Calcular cobros previos en la moneda de la factura
                    $cobrosPreviosData = HistoricoCobros::find()
                        ->where(['id_factura' => $factura->id])
                        ->andWhere($model->isNewRecord ? [] : ['!=', 'id', $model->id])
                        ->all();
                    
                    $cobrosPreviosEnMonedaFactura = 0;
                    foreach ($cobrosPreviosData as $cobro) {
                        // Convertir cada cobro a la moneda de la factura
                        $cobrosPreviosEnMonedaFactura += $convertCurrency($cobro->monto, $cobro->currency, $factura->currency);
                    }
                    
                    $cobrosPreviosEnMonedaFactura = round($cobrosPreviosEnMonedaFactura, 2);
                    $montoDisponible = round($factura->monto_final - $cobrosPreviosEnMonedaFactura, 2);
                ?>
                    <p><strong>Factura N°:</strong> <?= Html::encode($factura->codigo) ?></p>
                    <p style="margin-bottom: 8px;">
                        <strong>Mon

eda de Factura:</strong> 
                        <span style="background: #e3f2fd; padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                            <?= $factura->displayCurrency() ?>
                        </span>
                    </p>
                    <p style="margin-bottom: 15px;">
                        <strong>Monto Total Factura:</strong> 
                        <span style="color: #1976d2; font-weight: 700;">
                            <?= $formatCurrency($factura->monto_final, $factura->currency) ?>
                        </span>
                    </p>
                    <?php if ($cobrosPreviosEnMonedaFactura > 0): ?>
                        <p style="margin-bottom: 8px;">
                            <strong>Cobros Previos:</strong> 
                            <span style="color: #f57c00; font-weight: 600;">
                                <?= $formatCurrency($cobrosPreviosEnMonedaFactura, $factura->currency) ?>
                            </span>
                        </p>
                        <p style="margin-bottom: 0;">
                            <strong>Monto Disponible:</strong> 
                            <span style="color: #28a745; font-weight: 700; font-size: 1.1em;">
                                <?= $formatCurrency($montoDisponible, $factura->currency) ?>
                            </span>
                        </p>
                    <?php else: ?>
                        <p style="margin-bottom: 0;">
                            <strong>Monto Disponible:</strong> 
                            <span style="color: #28a745; font-weight: 700; font-size: 1.1em;">
                                <?= $formatCurrency($montoDisponible, $factura->currency) ?>
                            </span>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Indicador de Pasos -->
        <div class="stepper-container">
            <div class="stepper">
                <div class="stepper-progress" id="stepper-progress"></div>
                
                <div class="step active" data-step="1">
                    <div class="step-circle">
                        <span class="step-number">1</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Monto a Pagar</div>
                </div>
                
                <div class="step" data-step="2">
                    <div class="step-circle">
                        <span class="step-number">2</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Método de Pago y Notas</div>
                </div>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'historico-cobros-form',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ],
        ]); ?>

        <!-- Campos ocultos para id_cliente e id_factura -->
        <?= $form->field($model, 'id_cliente')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'id_factura')->hiddenInput()->label(false) ?>

        <!-- PASO 1: Monto a Pagar -->
        <div class="step-content active" data-step="1">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-currency-dollar"></i>
                    Monto del Cobro
                </h3>
                <p class="section-description">
                    Ingresa el monto exacto que se está cobrando al cliente en este registro.
                </p>
                
                <div class="monto-destacado">
                    <div class="monto-icono">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="monto-texto">
                        Especifica la cantidad de dinero cobrada
                    </div>
                    
                    <!-- Currency Carousel -->
                    <div class="currency-carousel">
                        <!-- Page 1: USDT Input -->
                        <div class="carousel-page-cobro active" data-currency="usdt">
                            <div class="currency-label-cobro">Monto en USDT</div>
                            <div class="input-with-badge-cobro">
                                <span class="currency-badge-cobro usdt-badge-cobro">USDT</span>
                                <input type="number" 
                                       id="monto-usdt-cobro" 
                                       class="form-control" 
                                       step="0.01" 
                                       min="0"
                                       value="0.00"
                                       placeholder="0.00"
                                       style="font-size: 2rem; text-align: center; height: 80px; font-weight: 700; color: #28a745; border: 2px solid #4caf50; border-radius: 15px;">
                            </div>
                            
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <div class="conversion-info visible" style="display: block;">
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Bolívares al cambio paralelo:</span>
                                        <span class="conversion-value" id="usdt-to-ves-cobro">Bs. 0,00</span>
                                    </div>
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Dólares a Tasa BCV:</span>
                                        <span class="conversion-value" id="usdt-to-bcv-cobro">$0.00</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Page 2: USD BCV Input -->
                        <div class="carousel-page-cobro" data-currency="usd-bcv">
                            <div class="currency-label-cobro">Monto en USD BCV</div>
                            <div class="input-with-badge-cobro">
                                <span class="currency-badge-cobro bcv-badge-cobro">USD BCV</span>
                                <input type="number" 
                                       id="monto-bcv-cobro" 
                                       class="form-control" 
                                       step="0.01" 
                                       min="0"
                                       value="0.00"
                                       placeholder="0.00"
                                       style="font-size: 2rem; text-align: center; height: 80px; font-weight: 700; color: #28a745; border: 2px solid #2196f3; border-radius: 15px;">
                            </div>
                            
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <div class="conversion-info visible" style="display: block;">
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Dólares USDT:</span>
                                        <span class="conversion-value" id="bcv-to-usdt-cobro">$0.00</span>
                                    </div>
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Bolívares al cambio paralelo:</span>
                                        <span class="conversion-value" id="bcv-to-ves-cobro">Bs. 0,00</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Page 3: Bolívares Input -->
                        <div class="carousel-page-cobro" data-currency="ves">
                            <div class="currency-label-cobro">Monto en Bolívares</div>
                            <div class="input-with-badge-cobro">
                                <span class="currency-badge-cobro ves-badge-cobro">Bs.</span>
                                <input type="number" 
                                       id="monto-ves-cobro" 
                                       class="form-control" 
                                       step="0.01" 
                                       min="0"
                                       value="0.00"
                                       placeholder="0,00"
                                       style="font-size: 2rem; text-align: center; height: 80px; font-weight: 700; color: #28a745; border: 2px solid #ff9800; border-radius: 15px;">
                            </div>
                            
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <div class="conversion-info visible" style="display: block;">
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Dólares USDT:</span>
                                        <span class="conversion-value" id="ves-to-usdt-cobro">$0.00</span>
                                    </div>
                                    <div class="conversion-row">
                                        <span class="conversion-label">En Dólares a Tasa BCV:</span>
                                        <span class="conversion-value" id="ves-to-bcv-cobro">$0.00</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Carousel Controls -->
                    <div class="carousel-controls-cobro">
                        <button type="button" class="carousel-nav-cobro" id="carousel-prev-cobro">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="carousel-nav-cobro" id="carousel-next-cobro">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- Hidden fields for form submission -->
                    <?= $form->field($model, 'monto')->textInput([
                        'type' => 'hidden',
                        'id' => 'monto-cobro-input'
                    ])->label(false) ?>
                    
                    <!-- Hidden field for currency -->
                    <input type="hidden" id="currency-cobro-selected" name="currency" value="USDT">
                    
                    <?php if ($factura): ?>
                        <div id="monto-restante-info" style="margin-top: 20px; display: none;">
                            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 15px; text-align: center;">
                                <p style="margin: 0; font-size: 0.9rem; color: #856404;">
                                    <strong id="monto-restante-label">Monto Restante a Pagar:</strong>
                                </p>
                                <p id="monto-restante-valor" style="margin: 10px 0 0 0; font-size: 1.3rem; font-weight: 700; color: #856404;">
                                    $0.00
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>

        <!-- PASO 2: Método de Pago y Notas -->
        <div class="step-content" data-step="2">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-credit-card"></i>
                    Método de Pago y Notas Adicionales
                </h3>
                <p class="section-description">
                    Selecciona cómo se realizó el pago y agrega cualquier observación relevante.
                </p>
                
                <?= $form->field($model, 'metodo_pago')->dropDownList($metodosPago, [
                    'prompt' => '-- Selecciona el método de pago --',
                    'class' => 'form-control'
                ])->hint('Forma en que se realizó el cobro') ?>

                <?= $form->field($model, 'nota')->textarea([
                    'rows' => 6,
                    'placeholder' => 'Ej: Cobro parcial, Cliente pagó en varias cuotas, Incluye recargo por mora, etc...'
                ])->hint('Información adicional sobre este cobro (opcional)') ?>

                <?= $form->field($model, 'fecha')->textInput([
                    'type' => 'date',
                    'value' => $model->isNewRecord ? date('Y-m-d') : $model->fecha
                ])->hint('Fecha en que se realizó el cobro') ?>
            </div>
        </div>

        <!-- Navegación entre pasos -->
        <div class="step-navigation">
            <button type="button" class="btn-step btn-previous" id="btn-previous" style="display: none;">
                <i class="bi bi-arrow-left"></i>
                Anterior
            </button>
            
            <button type="button" class="btn-step btn-next" id="btn-next">
                Siguiente
                <i class="bi bi-arrow-right"></i>
            </button>
            
            <button type="submit" class="btn-step btn-submit-final" id="btn-submit" style="display: none;">
                <i class="bi bi-check-circle me-2"></i>
                <?= $model->isNewRecord ? 'Registrar Cobro' : 'Actualizar Cobro' ?>
            </button>
        </div>

        <?php ActiveForm::end(); ?>

</div>

<?php
// JavaScript para manejo de pasos
$totalSteps = 2;
$montoFactura = $factura ? $factura->monto_final : 0;

// Calcular la suma de cobros previos para esta factura (excluyendo el registro actual si es una edición)
$cobrosPreview = 0;
if ($factura) {
    $cobrosPreview = HistoricoCobros::find()
        ->where(['id_factura' => $factura->id])
        ->andWhere($model->isNewRecord ? [] : ['!=', 'id', $model->id])
        ->sum('monto') ?: 0;
}

$precioParaleloJs = $precioParalelo ? $precioParalelo->precio_ves : 'null';
$precioOficialJs = $precioOficial ? $precioOficial->precio_ves : 'null';
$facturaCurrencyJs = $factura ? "'" . $factura->currency . "'" : "'USDT'";
$montoDisponibleJs = isset($montoDisponible) ? $montoDisponible : 0;

$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};
const montoFactura = {$montoFactura};
const cobrosPreview = {$cobrosPreview};
const precioParalelo = {$precioParaleloJs};
const precioOficial = {$precioOficialJs};
const facturaCurrency = {$facturaCurrencyJs};
const montoDisponibleInicial = {$montoDisponibleJs};

// Currency carousel state
let currentCurrencyPageCobro = 0;
const currencyPagesCobro = ['usdt', 'usd-bcv', 'ves'];
let touchStartXCobro = 0;
let touchEndXCobro = 0;

// Initialize currency carousel
initCurrencyCarouselCobro();

function initCurrencyCarouselCobro() {
    // Navigation buttons
    const btnPrev = document.getElementById('carousel-prev-cobro');
    const btnNext = document.getElementById('carousel-next-cobro');
    
    if (btnPrev) {
        btnPrev.addEventListener('click', () => navigateCarouselCobro(-1));
    }
    if (btnNext) {
        btnNext.addEventListener('click', () => navigateCarouselCobro(1));
    }
    
    // Input listeners for each currency
    const montoUsdt = document.getElementById('monto-usdt-cobro');
    const montoBcv = document.getElementById('monto-bcv-cobro');
    const montoVes = document.getElementById('monto-ves-cobro');
    
    if (montoUsdt) {
        montoUsdt.addEventListener('input', handleUsdtInputCobro);
    }
    if (montoBcv) {
        montoBcv.addEventListener('input', handleUsdBcvInputCobro);
    }
    if (montoVes) {
        montoVes.addEventListener('input', handleVesInputCobro);
    }
    
    // Touch events for mobile swipe
    const carousel = document.querySelector('.currency-carousel');
    if (carousel) {
        carousel.addEventListener('touchstart', handleTouchStartCobro, { passive: true });
        carousel.addEventListener('touchend', handleTouchEndCobro, { passive: true });
    }
}

// Touch handlers
function handleTouchStartCobro(e) {
    touchStartXCobro = e.changedTouches[0].screenX;
}

function handleTouchEndCobro(e) {
    touchEndXCobro = e.changedTouches[0].screenX;
    handleSwipeCobro();
}

function handleSwipeCobro() {
    const swipeThreshold = 50;
    const diff = touchStartXCobro - touchEndXCobro;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            navigateCarouselCobro(1);
        } else {
            navigateCarouselCobro(-1);
        }
    }
}

// Navigate carousel
function navigateCarouselCobro(direction) {
    const newIndex = currentCurrencyPageCobro + direction;
    if (newIndex >= 0 && newIndex < currencyPagesCobro.length) {
        goToCarouselPageCobro(newIndex);
    }
}

// Go to specific page
function goToCarouselPageCobro(index) {
    currentCurrencyPageCobro = index;
    
    // Update UI
    document.querySelectorAll('.carousel-page-cobro').forEach(page => {
        page.classList.remove('active');
    });
    
    const targetCurrency = currencyPagesCobro[index];
    const targetPage = document.querySelector('.carousel-page-cobro[data-currency="' + targetCurrency + '"]');
    if (targetPage) {
        targetPage.classList.add('active');
    }
    
    // Update currency hidden field
    let currencyValue = 'USDT';
    if (currencyPagesCobro[index] === 'usdt') {
        currencyValue = 'USDT';
    } else if (currencyPagesCobro[index] === 'usd-bcv') {
        currencyValue = 'BCV';
    } else if (currencyPagesCobro[index] === 'ves') {
        currencyValue = 'VES';
    }
    document.getElementById('currency-cobro-selected').value = currencyValue;
    
    // Update monto restante display in new currency
    actualizarMontoRestante();
}

// Handle USDT input (Page 1)
function handleUsdtInputCobro() {
    const usdt = parseFloat(this.value) || 0;
    
    if (precioParalelo && precioOficial) {
        // USDT → VES (Paralelo)
        const ves = usdt * precioParalelo;
        // USDT → USD BCV
        const usdBcv = ves / precioOficial;
        
        // Update display
        const usdtToVes = document.getElementById('usdt-to-ves-cobro');
        const usdtToBcv = document.getElementById('usdt-to-bcv-cobro');
        
        if (usdtToVes) usdtToVes.textContent = 'Bs. ' + ves.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (usdtToBcv) usdtToBcv.textContent = '$' + usdBcv.toFixed(2);
        
        // Sync other inputs (without triggering events)
        const montoBcv = document.getElementById('monto-bcv-cobro');
        const montoVes = document.getElementById('monto-ves-cobro');
        
        if (montoBcv) {
            montoBcv.removeEventListener('input', handleUsdBcvInputCobro);
            montoBcv.value = usdBcv.toFixed(2);
            montoBcv.addEventListener('input', handleUsdBcvInputCobro);
        }
        if (montoVes) {
            montoVes.removeEventListener('input', handleVesInputCobro);
            montoVes.value = ves.toFixed(2);
            montoVes.addEventListener('input', handleVesInputCobro);
        }
    }
    
    // Update hidden field - save in USDT (selected currency)
    const montoInput = document.getElementById('monto-cobro-input');
    if (montoInput) {
        montoInput.value = usdt.toFixed(2);
        actualizarMontoRestante();
    }
}

// Handle USD BCV input (Page 2)
function handleUsdBcvInputCobro() {
    const usdBcv = parseFloat(this.value) || 0;
    
    if (precioParalelo && precioOficial) {
        // USD BCV → VES (Oficial)
        const ves = usdBcv * precioOficial;
        // VES → USDT
        const usdt = ves / precioParalelo;
        
        // Update display
        const bcvToUsdt = document.getElementById('bcv-to-usdt-cobro');
        const bcvToVes = document.getElementById('bcv-to-ves-cobro');
        
        if (bcvToUsdt) bcvToUsdt.textContent = '$' + usdt.toFixed(2);
        if (bcvToVes) bcvToVes.textContent = 'Bs. ' + ves.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Sync other inputs
        const montoUsdt = document.getElementById('monto-usdt-cobro');
        const montoVes = document.getElementById('monto-ves-cobro');
        
        if (montoUsdt) {
            montoUsdt.removeEventListener('input', handleUsdtInputCobro);
            montoUsdt.value = usdt.toFixed(2);
            montoUsdt.addEventListener('input', handleUsdtInputCobro);
        }
        if (montoVes) {
            montoVes.removeEventListener('input', handleVesInputCobro);
            montoVes.value = ves.toFixed(2);
            montoVes.addEventListener('input', handleVesInputCobro);
        }
    }
    
    // Update hidden field - save in BCV (selected currency)
    const montoInput = document.getElementById('monto-cobro-input');
    if (montoInput) {
        montoInput.value = usdBcv.toFixed(2);
        actualizarMontoRestante();
    }
}

// Handle VES input (Page 3)
function handleVesInputCobro() {
    const ves = parseFloat(this.value) || 0;
    
    if (precioParalelo && precioOficial) {
        // VES → USDT
        const usdt = ves / precioParalelo;
        // VES → USD BCV
        const usdBcv = ves / precioOficial;
        
        // Update display
        const vesToUsdt = document.getElementById('ves-to-usdt-cobro');
        const vesToBcv = document.getElementById('ves-to-bcv-cobro');
        
        if (vesToUsdt) vesToUsdt.textContent = '$' + usdt.toFixed(2);
        if (vesToBcv) vesToBcv.textContent = '$' + usdBcv.toFixed(2);
        
        // Sync other inputs
        const montoUsdt = document.getElementById('monto-usdt-cobro');
        const montoBcv = document.getElementById('monto-bcv-cobro');
        
        if (montoUsdt) {
            montoUsdt.removeEventListener('input', handleUsdtInputCobro);
            montoUsdt.value = usdt.toFixed(2);
            montoUsdt.addEventListener('input', handleUsdtInputCobro);
        }
        if (montoBcv) {
            montoBcv.removeEventListener('input', handleUsdBcvInputCobro);
            montoBcv.value = usdBcv.toFixed(2);
            montoBcv.addEventListener('input', handleUsdBcvInputCobro);
        }
    }
    
    // Update hidden field - save in VES (selected currency)
    const montoInput = document.getElementById('monto-cobro-input');
    if (montoInput) {
        montoInput.value = ves.toFixed(2);
        actualizarMontoRestante();
    }
}

// Función para actualizar conversiones de moneda (legacy - ya no se usa)
function actualizarConversiones(monto) {
    // Esta función ahora está manejada por el carrusel
    return;
}

// Función helper para convertir entre monedas en JavaScript
function convertCurrencyJS(amount, fromCurrency, toCurrency) {
    if (!precioParalelo || !precioOficial) {
        return amount;
    }
    
    const value = parseFloat(amount) || 0;
    
    // Si son la misma moneda, retornar sin conversión
    if (fromCurrency === toCurrency) {
        return value;
    }
    
    // Convertir de fromCurrency a VES (moneda base)
    let amountInVES = 0;
    if (fromCurrency === 'USDT') {
        amountInVES = value * precioParalelo;
    } else if (fromCurrency === 'BCV') {
        amountInVES = value * precioOficial;
    } else if (fromCurrency === 'VES') {
        amountInVES = value;
    }
    
    // Convertir de VES a toCurrency
    let converted = 0;
    if (toCurrency === 'USDT') {
        converted = amountInVES / precioParalelo;
    } else if (toCurrency === 'BCV') {
        converted = amountInVES / precioOficial;
    } else if (toCurrency === 'VES') {
        converted = amountInVES;
    }
    
    // IMPORTANTE: usar round para evitar errores de precisión (.99)
    return parseFloat(converted.toFixed(2));
}

// Función para actualizar el monto restante
function actualizarMontoRestante() {
    const montoInput = document.getElementById('monto-cobro-input');
    const montoRestanteInfo = document.getElementById('monto-restante-info');
    const montoRestanteValor = document.getElementById('monto-restante-valor');
    const montoRestanteLabel = document.getElementById('monto-restante-label');
    
    if (!montoInput || !montoRestanteInfo || !montoRestanteValor) {
        return; // Si no hay factura, no hacemos nada
    }
    
    const montoCobrado = parseFloat(montoInput.value) || 0;
    
    if (montoCobrado > 0) {
        // Obtener la moneda seleccionada
        const selectedCurrency = document.getElementById('currency-cobro-selected').value;
        
        // Convertir el monto cobrado a la moneda de la factura
        const montoCobradoEnMonedaFactura = convertCurrencyJS(montoCobrado, selectedCurrency, facturaCurrency);
        
        // El monto disponible ya está en la moneda de la factura (calculado en PHP)
        const montoDisponible = montoDisponibleInicial;
        
        // Verificar si el monto excede el monto disponible
        if (montoCobradoEnMonedaFactura > montoDisponible) {
            // Convertir montoDisponible a la moneda seleccionada para el mensaje
            const montoDisponibleEnMonedaSeleccionada = convertCurrencyJS(montoDisponible, facturaCurrency, selectedCurrency);
            
            alert('⚠️ Advertencia: El monto ingresado (' + montoCobrado.toFixed(2) + ') excede el monto disponible (' + montoDisponibleEnMonedaSeleccionada.toFixed(2) + ' en ' + selectedCurrency + ').');
            
            // Resetear todos los campos
            montoInput.value = '';
            const montoUsdt = document.getElementById('monto-usdt-cobro');
            const montoBcv = document.getElementById('monto-bcv-cobro');
            const montoVes = document.getElementById('monto-ves-cobro');
            if (montoUsdt) montoUsdt.value = '0.00';
            if (montoBcv) montoBcv.value = '0.00';
            if (montoVes) montoVes.value = '0.00';
            
            montoRestanteInfo.style.display = 'none';
            return;
        }
        
        // Calcular monto restante en la moneda de la factura
        const montoRestanteEnMonedaFactura = parseFloat((montoDisponible - montoCobradoEnMonedaFactura).toFixed(2));
        
        // Mostrar el restante en la moneda actual del carrusel
        let displayValue = '';
        let labelText = '';
        let displayCurrency = '';
        
        if (precioParalelo && precioOficial) {
            const currentCurrency = currencyPagesCobro[currentCurrencyPageCobro];
            let currentCurrencyEnum = 'USDT';
            
            if (currentCurrency === 'usdt') {
                currentCurrencyEnum = 'USDT';
                displayCurrency = 'USDT';
            } else if (currentCurrency === 'usd-bcv') {
                currentCurrencyEnum = 'BCV';
                displayCurrency = 'USD BCV';
            } else if (currentCurrency === 'ves') {
                currentCurrencyEnum = 'VES';
                displayCurrency = 'Bolívares';
            }
            
            // Convertir el restante a la moneda actual del carrusel
            const restanteConvertido = convertCurrencyJS(montoRestanteEnMonedaFactura, facturaCurrency, currentCurrencyEnum);
            
            if (currentCurrencyEnum === 'VES') {
                displayValue = 'Bs. ' + restanteConvertido.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                displayValue = '$' + restanteConvertido.toFixed(2);
            }
            
            labelText = 'Restante en ' + displayCurrency + ':';
        } else {
            displayValue = '$' + montoRestanteEnMonedaFactura.toFixed(2);
            labelText = 'Monto Restante a Pagar:';
        }
        
        if (montoRestanteLabel) montoRestanteLabel.textContent = labelText;
        montoRestanteValor.textContent = displayValue;
        montoRestanteInfo.style.display = 'block';
        
        // Cambiar color si está completamente pagado
        if (montoRestanteEnMonedaFactura <= 0.01) { // Tolerancia de 1 centavo
            montoRestanteInfo.querySelector('div').style.background = '#d4edda';
            montoRestanteInfo.querySelector('div').style.borderColor = '#28a745';
            montoRestanteValor.style.color = '#155724';
            if (montoRestanteLabel) montoRestanteLabel.parentElement.style.color = '#155724';
        } else {
            montoRestanteInfo.querySelector('div').style.background = '#fff3cd';
            montoRestanteInfo.querySelector('div').style.borderColor = '#ffc107';
            montoRestanteValor.style.color = '#856404';
            if (montoRestanteLabel) montoRestanteLabel.parentElement.style.color = '#856404';
        }
    } else {
        montoRestanteInfo.style.display = 'none';
    }
}

// Note: Event listeners for monto-cobro-input are now handled by the carousel


// Función para actualizar la vista de pasos
function updateStep() {
    // Actualizar contenido
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector('.step-content[data-step="' + currentStep + '"]').classList.add('active');
    
    // Actualizar indicadores
    document.querySelectorAll('.step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNum < currentStep) {
            step.classList.add('completed');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
        }
    });
    
    // Actualizar barra de progreso
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('stepper-progress').style.width = progress + '%';
    
    // Actualizar botones de navegación
    const btnPrevious = document.getElementById('btn-previous');
    const btnNext = document.getElementById('btn-next');
    const btnSubmit = document.getElementById('btn-submit');
    
    if (currentStep === 1) {
        btnPrevious.style.display = 'none';
    } else {
        btnPrevious.style.display = 'flex';
    }
    
    if (currentStep === totalSteps) {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'flex';
    } else {
        btnNext.style.display = 'flex';
        btnSubmit.style.display = 'none';
    }
    
    // Scroll suave hacia arriba
    document.querySelector('.historico-cobros-form-container').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

// Botón siguiente
document.getElementById('btn-next').addEventListener('click', function() {
    if (currentStep < totalSteps) {
        currentStep++;
        updateStep();
    }
});

// Botón anterior
document.getElementById('btn-previous').addEventListener('click', function() {
    if (currentStep > 1) {
        currentStep--;
        updateStep();
    }
});

// Hacer clic en los pasos del stepper
document.querySelectorAll('.step').forEach((step, index) => {
    step.addEventListener('click', function() {
        currentStep = index + 1;
        updateStep();
    });
});

// Inicializar
updateStep();
JS;
$this->registerJs($js);
?>
