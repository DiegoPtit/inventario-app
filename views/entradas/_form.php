<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Productos;
use app\models\Lugares;
use app\models\Proveedores;

/** @var yii\web\View $this */
/** @var app\models\Entradas $model */
/** @var yii\widgets\ActiveForm $form */

// Obtener listas para dropdowns
$productos = ArrayHelper::map(Productos::find()->all(), 'id', function($model) {
    return $model->marca . ' ' . $model->modelo;
});
$lugares = ArrayHelper::map(Lugares::find()->all(), 'id', 'nombre');
$proveedores = ArrayHelper::map(Proveedores::find()->all(), 'id', 'razon_social');

// CSS personalizado para el formulario con pasos
$this->registerCss('
.entradas-form-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 900px;
    margin: 0 auto;
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
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border-color: #17a2b8;
    color: white;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    transform: scale(1.1);
}

.step.completed .step-circle {
    background: #28a745;
    border-color: #28a745;
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
    color: #6c757d;
    font-weight: 500;
    text-align: center;
    max-width: 120px;
}

.step.active .step-label {
    color: #17a2b8;
    font-weight: 600;
}

.step.completed .step-label {
    color: #28a745;
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
    color: #17a2b8;
    font-size: 1.8rem;
}

.section-description {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 30px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 0;
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
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
}

/* Upload de Documento */
.file-upload-container {
    border: 3px dashed #dee2e6;
    border-radius: 15px;
    padding: 40px 30px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.file-upload-container:hover {
    border-color: #17a2b8;
    background: #e7f6f8;
}

.file-upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 15px;
}

.file-upload-text {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 10px;
}

.file-upload-hint {
    font-size: 0.9rem;
    color: #adb5bd;
}

.field-entradas-documentfile input[type="file"] {
    display: none;
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
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    margin-left: auto;
}

.btn-next:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.btn-submit-final {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 15px 40px;
    margin-left: auto;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-submit-final:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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

/* Tipo de Entrada Cards */
.tipo-entrada-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.tipo-entrada-card {
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.tipo-entrada-card:hover {
    border-color: #17a2b8;
    background: #e7f6f8;
    transform: translateY(-5px);
}

.tipo-entrada-card.selected {
    border-color: #17a2b8;
    background: linear-gradient(135deg, #e7f6f8 0%, #d1ecf1 100%);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.tipo-entrada-card i {
    font-size: 3rem;
    color: #17a2b8;
    margin-bottom: 15px;
}

.tipo-entrada-card h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.tipo-entrada-card p {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 8px;
    margin-bottom: 0;
}

/* Campos ocultos por defecto */
.proveedor-fields {
    display: none;
    animation: fadeIn 0.3s ease;
}

.proveedor-fields.show {
    display: block;
}

/* Resumen de Stock */
.stock-resumen {
    background: linear-gradient(135deg, #e7f6f8 0%, #d1ecf1 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    border: 2px solid #17a2b8;
}

.stock-icono {
    font-size: 4rem;
    color: #17a2b8;
    margin-bottom: 20px;
}

.stock-texto {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .entradas-form-container {
        padding: 20px;
    }
    
    .stepper {
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .step {
        flex: 0 0 calc(33.333% - 10px);
    }
    
    .step-label {
        font-size: 0.75rem;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 0.9rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
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
    
    .tipo-entrada-options {
        grid-template-columns: 1fr;
    }
}
');
?>

<div class="entradas-form">
    <div class="entradas-form-container">
        
        <!-- Indicador de Pasos -->
        <div class="stepper-container">
            <div class="stepper">
                <div class="stepper-progress" id="stepper-progress"></div>
                
                <div class="step active" data-step="1">
                    <div class="step-circle">
                        <span class="step-number">1</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Tipo de Entrada</div>
                </div>
                
                <div class="step" data-step="2">
                    <div class="step-circle">
                        <span class="step-number">2</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Stock y Almacén</div>
                </div>
                
                <div class="step" data-step="3">
                    <div class="step-circle">
                        <span class="step-number">3</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Datos del Proveedor</div>
                </div>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'entradas-form',
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ],
        ]); ?>

        <!-- PASO 1: Selección del Tipo de Entrada -->
        <div class="step-content active" data-step="1">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-card-checklist"></i>
                    Selecciona el Tipo de Entrada
                </h3>
                <p class="section-description">
                    Elige el tipo de entrada que estás registrando en el inventario.
                </p>
                
                <div class="tipo-entrada-options">
                    <div class="tipo-entrada-card" data-tipo="Compra">
                        <i class="bi bi-cart-check"></i>
                        <h4>Compra</h4>
                        <p>Entrada por adquisición a proveedor</p>
                    </div>
                    
                    <div class="tipo-entrada-card" data-tipo="Donación">
                        <i class="bi bi-gift"></i>
                        <h4>Donación</h4>
                        <p>Entrada por donación recibida</p>
                    </div>
                    
                    <div class="tipo-entrada-card" data-tipo="Inventario Inicial">
                        <i class="bi bi-clipboard-data"></i>
                        <h4>Inventario Inicial</h4>
                        <p>Registro de stock inicial</p>
                    </div>
                </div>

                <?= $form->field($model, 'tipo_entrada')->hiddenInput(['id' => 'entradas-tipo_entrada'])->label(false) ?>
            </div>
        </div>

        <!-- PASO 2: Asignación de Stock a Producto -->
        <div class="step-content" data-step="2">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-box-seam"></i>
                    Asignación de Stock a Producto
                </h3>
                <p class="section-description">
                    Selecciona el producto, la cantidad a ingresar y el almacén de destino.
                </p>
                
                <div class="stock-resumen">
                    <div class="stock-icono">
                        <i class="bi bi-boxes"></i>
                    </div>
                    
                    <?= $form->field($model, 'id_producto')->dropDownList($productos, [
                        'prompt' => '-- Selecciona un producto --',
                        'class' => 'form-control'
                    ]) ?>

                    <div class="stock-texto">
                        Ingresa la cantidad de unidades que agregarás al inventario
                    </div>
                    
                    <?= $form->field($model, 'cantidad')->textInput([
                        'type' => 'number',
                        'min' => '1',
                        'placeholder' => '0',
                        'style' => 'max-width: 300px; margin: 0 auto 25px; font-size: 1.5rem; text-align: center; height: 60px;'
                    ])->label('Cantidad de Unidades', ['style' => 'font-size: 1.2rem; margin-bottom: 15px;']) ?>

                    <?= $form->field($model, 'id_lugar')->dropDownList($lugares, [
                        'prompt' => '-- Selecciona un almacén --',
                        'class' => 'form-control',
                        'style' => 'max-width: 400px; margin: 0 auto;'
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- PASO 3: Datos del Proveedor (Condicional) -->
        <div class="step-content" data-step="3">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-person-badge"></i>
                    Datos del Proveedor
                </h3>
                <p class="section-description" id="proveedor-description">
                    Completa la información del proveedor y el comprobante de compra.
                </p>
                
                <div class="proveedor-fields" id="proveedor-fields">
                    <?= $form->field($model, 'id_proveedor')->dropDownList($proveedores, [
                        'prompt' => '-- Selecciona un proveedor --',
                        'class' => 'form-control'
                    ]) ?>

                    <?= $form->field($model, 'nro_documento')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: FAC-001234'
                    ]) ?>

                    <div class="file-upload-container" onclick="document.getElementById('entradas-documentfile').click()">
                        <div class="file-upload-icon">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>
                        <div class="file-upload-text">
                            <strong>Haz clic para seleccionar documento de respaldo</strong>
                        </div>
                        <div class="file-upload-hint">
                            Formatos aceptados: PDF, JPG, JPEG, PNG<br>
                            Máximo 5MB
                        </div>
                    </div>

                    <?= $form->field($model, 'documentFile')->fileInput([
                        'accept' => '.pdf,.jpg,.jpeg,.png',
                        'id' => 'entradas-documentfile'
                    ])->label(false) ?>
                </div>
                
                <div class="alert alert-info" id="no-proveedor-message" style="display: none; border-radius: 15px; padding: 30px; text-align: center;">
                    <i class="bi bi-info-circle" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                    <h4>Datos de Proveedor No Requeridos</h4>
                    <p>Para este tipo de entrada no es necesario proporcionar información del proveedor.</p>
                </div>
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
                Registrar Entrada
            </button>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
// JavaScript para manejo de pasos
$totalSteps = 3;
$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};
let tipoEntradaSeleccionado = null;

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
    
    // Mostrar/ocultar campos de proveedor según tipo de entrada y paso actual
    if (currentStep === 3) {
        updateProveedorFields();
    }
    
    // Scroll suave hacia arriba
    document.querySelector('.entradas-form-container').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

// Función para actualizar campos de proveedor
function updateProveedorFields() {
    const proveedorFields = document.getElementById('proveedor-fields');
    const noProveedorMessage = document.getElementById('no-proveedor-message');
    const proveedorDescription = document.getElementById('proveedor-description');
    
    if (tipoEntradaSeleccionado === 'Compra') {
        proveedorFields.classList.add('show');
        noProveedorMessage.style.display = 'none';
        proveedorDescription.textContent = 'Completa la información del proveedor y el comprobante de compra.';
    } else {
        proveedorFields.classList.remove('show');
        noProveedorMessage.style.display = 'block';
        proveedorDescription.textContent = 'Esta sección no requiere datos para este tipo de entrada.';
    }
}

// Selección de tipo de entrada
document.querySelectorAll('.tipo-entrada-card').forEach(card => {
    card.addEventListener('click', function() {
        // Quitar selección de todas las cards
        document.querySelectorAll('.tipo-entrada-card').forEach(c => {
            c.classList.remove('selected');
        });
        
        // Seleccionar la card clickeada
        this.classList.add('selected');
        tipoEntradaSeleccionado = this.getAttribute('data-tipo');
        
        // Actualizar campo oculto
        document.getElementById('entradas-tipo_entrada').value = tipoEntradaSeleccionado;
    });
});

// Botón siguiente
document.getElementById('btn-next').addEventListener('click', function() {
    // Validar paso actual antes de continuar
    if (currentStep === 1 && !tipoEntradaSeleccionado) {
        alert('Por favor, selecciona un tipo de entrada');
        return;
    }
    
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
        // Solo permitir navegar a pasos previos o al siguiente inmediato
        const targetStep = index + 1;
        if (targetStep === 1 || (targetStep > 1 && tipoEntradaSeleccionado)) {
            currentStep = targetStep;
            updateStep();
        }
    });
});

// Preview de documento seleccionado
document.getElementById('entradas-documentfile').addEventListener('change', function(e) {
    const container = document.querySelector('.file-upload-container');
    const file = e.target.files[0];
    
    if (file) {
        container.querySelector('.file-upload-text').innerHTML = 
            '<strong>Archivo seleccionado: ' + file.name + '</strong>';
        container.querySelector('.file-upload-hint').innerHTML = 
            'Tamaño: ' + (file.size / 1024).toFixed(2) + ' KB';
    }
});

// Inicializar
updateStep();
JS;
$this->registerJs($js);
?>