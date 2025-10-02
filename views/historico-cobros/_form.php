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
.historico-cobros-form-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 900px;
    margin: 0 auto;
}

/* Información del Cliente/Factura */
.info-box {
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    border-left: 5px solid #007bff;
}

.info-box h4 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box p {
    margin: 5px 0;
    color: #495057;
    font-size: 1rem;
}

.info-box strong {
    color: #333;
}

.info-box .conversion-detail {
    font-size: 0.8rem;
    color: #6c757d;
    margin-left: 10px;
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transform: scale(1.1);
}

.step.completed .step-circle {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.step.completed .step-circle i {
    display: block;
}

.step-circle i {
    display: none;
}

.step-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
    max-width: 150px;
}

.step.active .step-label {
    color: #28a745;
    font-weight: 600;
}

.step.completed .step-label {
    color: #007bff;
}

/* Secciones de Pasos */
.step-content {
    display: none;
    animation: fadeIn 0.3s ease;
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
    color: #28a745;
    font-size: 1.8rem;
}

.section-description {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
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
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* Campo de Monto Destacado */
.monto-destacado {
    background: linear-gradient(135deg, #d4edda 0%, #f0f8ff 100%);
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    border: 2px solid #28a745;
}

.monto-icono {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 20px;
}

.monto-texto {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 20px;
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    margin-left: auto;
}

.btn-next:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-submit-final {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 15px 40px;
    margin-left: auto;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.btn-submit-final:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
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
');
?>

<div class="historico-cobros-form">
    <div class="historico-cobros-form-container">
        
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
                    // Calcular cobros previos para mostrar
                    $cobrosPrevios = HistoricoCobros::find()
                        ->where(['id_factura' => $factura->id])
                        ->andWhere($model->isNewRecord ? [] : ['!=', 'id', $model->id])
                        ->sum('monto') ?: 0;
                    $montoDisponible = $factura->monto_final - $cobrosPrevios;
                    
                    // Calcular conversiones si existen los precios
                    if ($precioParalelo && $precioOficial) {
                        $montoFacturaVes = $factura->monto_final * $precioParalelo->precio_ves;
                        $montoFacturaUsdOficial = $montoFacturaVes / $precioOficial->precio_ves;
                        
                        if ($cobrosPrevios > 0) {
                            $cobrosPreviosVes = $cobrosPrevios * $precioParalelo->precio_ves;
                            $cobrosPreviosUsdOficial = $cobrosPreviosVes / $precioOficial->precio_ves;
                            
                            $montoDisponibleVes = $montoDisponible * $precioParalelo->precio_ves;
                            $montoDisponibleUsdOficial = $montoDisponibleVes / $precioOficial->precio_ves;
                        }
                    }
                ?>
                    <p><strong>Factura N°:</strong> <?= Html::encode($factura->codigo) ?></p>
                    <p>
                        <strong>Monto Factura:</strong> $<?= number_format($factura->monto_final, 2) ?>
                        <?php if ($precioParalelo && $precioOficial): ?>
                            <br>
                            <span class="conversion-detail">
                                Bs. <?= number_format($montoFacturaVes, 2, ',', '.') ?> 
                                | <strong>$<?= number_format($montoFacturaUsdOficial, 2) ?> (BCV)</strong>
                            </span>
                        <?php endif; ?>
                    </p>
                    <?php if ($cobrosPrevios > 0): ?>
                        <p>
                            <strong>Cobros Previos:</strong> $<?= number_format($cobrosPrevios, 2) ?>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <br>
                                <span class="conversion-detail">
                                    Bs. <?= number_format($cobrosPreviosVes, 2, ',', '.') ?> 
                                    | <strong>$<?= number_format($cobrosPreviosUsdOficial, 2) ?> (BCV)</strong>
                                </span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong>Monto Disponible:</strong> <span style="color: #28a745; font-weight: 700;">$<?= number_format($montoDisponible, 2) ?></span>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <br>
                                <span class="conversion-detail">
                                    Bs. <?= number_format($montoDisponibleVes, 2, ',', '.') ?> 
                                    | <strong>$<?= number_format($montoDisponibleUsdOficial, 2) ?> (BCV)</strong>
                                </span>
                            <?php endif; ?>
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
                    
                    <?= $form->field($model, 'monto')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'placeholder' => '0.00',
                        'class' => 'form-control',
                        'id' => 'monto-cobro-input'
                    ])->label('Monto ($)') ?>
                    
                    <?php if ($precioParalelo && $precioOficial): ?>
                        <div id="conversion-info" class="conversion-info">
                            <div class="conversion-row">
                                <span class="conversion-label">En Bolívares al cambio paralelo:</span>
                                <span class="conversion-value" id="monto-ves">Bs. 0,00</span>
                            </div>
                            <div class="conversion-row">
                                <span class="conversion-label">En Dólares a Tasa BCV:</span>
                                <span class="conversion-value" id="monto-usd-oficial">$0.00</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($factura): ?>
                        <div id="monto-restante-info" style="margin-top: 20px; display: none;">
                            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 15px; text-align: center;">
                                <p style="margin: 0; font-size: 1rem; color: #856404;">
                                    <strong>Monto Restante a Pagar:</strong>
                                </p>
                                <p id="monto-restante-valor" style="margin: 10px 0 0 0; font-size: 1.5rem; font-weight: 700; color: #856404;">
                                    $0.00
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
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

$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};
const montoFactura = {$montoFactura};
const cobrosPreview = {$cobrosPreview};
const precioParalelo = {$precioParaleloJs};
const precioOficial = {$precioOficialJs};

// Función para actualizar conversiones de moneda
function actualizarConversiones(monto) {
    if (!precioParalelo || !precioOficial) {
        return;
    }
    
    const conversionInfo = document.getElementById('conversion-info');
    const montoVes = document.getElementById('monto-ves');
    const montoUsdOficial = document.getElementById('monto-usd-oficial');
    
    if (!conversionInfo || !montoVes || !montoUsdOficial) {
        return;
    }
    
    if (monto > 0) {
        // USDT → VES
        const ves = monto * precioParalelo;
        // VES → USD oficial
        const usdOficial = ves / precioOficial;
        
        montoVes.textContent = 'Bs. ' + ves.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        montoUsdOficial.textContent = '$' + usdOficial.toFixed(2);
        
        conversionInfo.classList.add('visible');
    } else {
        conversionInfo.classList.remove('visible');
    }
}

// Función para actualizar el monto restante
function actualizarMontoRestante() {
    const montoInput = document.getElementById('monto-cobro-input');
    const montoRestanteInfo = document.getElementById('monto-restante-info');
    const montoRestanteValor = document.getElementById('monto-restante-valor');
    
    if (!montoInput || !montoRestanteInfo || !montoRestanteValor) {
        return; // Si no hay factura, no hacemos nada
    }
    
    const montoCobrado = parseFloat(montoInput.value) || 0;
    
    // Actualizar conversiones de moneda
    actualizarConversiones(montoCobrado);
    
    if (montoCobrado > 0) {
        // Calcular el monto disponible para cobrar (considerando cobros previos)
        const montoDisponible = montoFactura - cobrosPreview;
        
        // Verificar si el monto excede el monto disponible
        if (montoCobrado > montoDisponible) {
            alert('⚠️ Advertencia: El monto ingresado (' + montoCobrado.toFixed(2) + ') es mayor al monto disponible para cobrar (' + montoDisponible.toFixed(2) + '). Ya se han cobrado $' + cobrosPreview.toFixed(2) + ' previamente.');
            montoInput.value = '';
            montoRestanteInfo.style.display = 'none';
            return;
        }
        
        // Calcular monto restante (considerando cobros previos + monto actual)
        const montoRestante = montoFactura - cobrosPreview - montoCobrado;
        montoRestanteValor.textContent = '$' + montoRestante.toFixed(2);
        montoRestanteInfo.style.display = 'block';
        
        // Cambiar color si está completamente pagado
        if (montoRestante === 0) {
            montoRestanteInfo.querySelector('div').style.background = '#d4edda';
            montoRestanteInfo.querySelector('div').style.borderColor = '#28a745';
            montoRestanteValor.style.color = '#155724';
            montoRestanteInfo.querySelector('p').style.color = '#155724';
        } else {
            montoRestanteInfo.querySelector('div').style.background = '#fff3cd';
            montoRestanteInfo.querySelector('div').style.borderColor = '#ffc107';
            montoRestanteValor.style.color = '#856404';
            montoRestanteInfo.querySelector('p').style.color = '#856404';
        }
    } else {
        montoRestanteInfo.style.display = 'none';
    }
}

// Agregar evento al input de monto
const montoInput = document.getElementById('monto-cobro-input');
if (montoInput) {
    montoInput.addEventListener('input', function() {
        actualizarMontoRestante();
        // Si no hay factura, solo actualizar conversiones
        if (!montoFactura) {
            const montoCobrado = parseFloat(this.value) || 0;
            actualizarConversiones(montoCobrado);
        }
    });
    montoInput.addEventListener('change', function() {
        actualizarMontoRestante();
        // Si no hay factura, solo actualizar conversiones
        if (!montoFactura) {
            const montoCobrado = parseFloat(this.value) || 0;
            actualizarConversiones(montoCobrado);
        }
    });
}

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
