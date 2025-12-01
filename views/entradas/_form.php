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

/* Barra de Progreso Simplificada */
.progress-bar-container {
    margin-bottom: 40px;
}

.progress-bar-wrapper {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #17a2b8 0%, #138496 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    position: relative;
}

.progress-bar-fill::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-step-text {
    font-weight: 600;
    color: #17a2b8;
}

.progress-percentage {
    font-weight: 500;
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
    
    .progress-bar-container {
        margin-bottom: 30px;
    }
    
    .progress-info {
        font-size: 0.85rem;
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
        
        <!-- Barra de Progreso -->
        <div class="progress-bar-container">
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" id="progress-bar-fill-entradas"></div>
            </div>
            <div class="progress-info">
                <span class="progress-step-text" id="progress-step-text-entradas">Paso 1 de 3: Tipo de Entrada</span>
                <span class="progress-percentage" id="progress-percentage-entradas">0%</span>
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
                    
                    <div class="form-group">
                        <label class="form-label fw-bold">Producto <span class="text-danger">*</span></label>
                        <button type="button" class="form-control text-start" id="btn-seleccionar-producto-entrada" 
                                style="background: white; border: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                            <span id="producto-seleccionado-text" style="color: #6c757d;">-- Selecciona un producto --</span>
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <?= $form->field($model, 'id_producto')->hiddenInput(['id' => 'entradas-id_producto'])->label(false) ?>


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
    // Nombres de los pasos
    const stepNames = [
        'Tipo de Entrada',
        'Stock y Almacén',
        'Datos del Proveedor'
    ];
    
    // Actualizar contenido
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector('.step-content[data-step="' + currentStep + '"]').classList.add('active');
    
    // Actualizar barra de progreso
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    const progressBar = document.getElementById('progress-bar-fill-entradas');
    const progressText = document.getElementById('progress-step-text-entradas');
    const progressPercentage = document.getElementById('progress-percentage-entradas');
    
    if (progressBar) {
        progressBar.style.width = progress + '%';
    }
    
    if (progressText) {
        progressText.textContent = 'Paso ' + currentStep + ' de ' + totalSteps + ': ' + stepNames[currentStep - 1];
    }
    
    if (progressPercentage) {
        progressPercentage.textContent = Math.round(progress) + '%';
    }
    
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

// Botón para seleccionar producto mediante modal
const btnSeleccionarProducto = document.getElementById('btn-seleccionar-producto-entrada');
if (btnSeleccionarProducto) {
    btnSeleccionarProducto.addEventListener('click', function() {
        // Abrir modal en modo "entradas"
        if (typeof window.abrirModalSeleccionProducto === 'function') {
            window.abrirModalSeleccionProducto('entradas', function(producto) {
                // Callback cuando se selecciona un producto
                document.getElementById('entradas-id_producto').value = producto.id;
                document.getElementById('producto-seleccionado-text').textContent = 
                    (producto.marca || '') + ' ' + (producto.modelo || '');
                document.getElementById('producto-seleccionado-text').style.color = '#333';
            });
        }
    });
}

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