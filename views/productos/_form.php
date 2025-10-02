<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Lugares;
use app\models\Categorias;

/** @var yii\web\View $this */
/** @var app\models\Productos $model */
/** @var yii\widgets\ActiveForm $form */

// Obtener listas para dropdowns
$lugares = ArrayHelper::map(Lugares::find()->all(), 'id', 'nombre');
$categorias = ArrayHelper::map(Categorias::find()->all(), 'id', 'titulo');

// CSS personalizado para el formulario con pasos
$this->registerCss('
.productos-form-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 1000px;
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
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
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
    max-width: 100px;
}

.step.active .step-label {
    color: #007bff;
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
    color: #007bff;
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
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* Upload de Imágenes */
.file-upload-container {
    border: 3px dashed #dee2e6;
    border-radius: 15px;
    padding: 50px 30px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.file-upload-container:hover {
    border-color: #007bff;
    background: #e7f3ff;
}

.file-upload-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 20px;
}

.file-upload-text {
    font-size: 1.2rem;
    color: #495057;
    margin-bottom: 15px;
}

.file-upload-hint {
    font-size: 0.9rem;
    color: #adb5bd;
}

.existing-images {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.existing-image-item {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    aspect-ratio: 1;
}

.existing-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.field-productos-imagefiles input[type="file"] {
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
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    margin-left: auto;
}

.btn-next:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
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

/* Resumen de Cantidad */
.cantidad-resumen {
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    border: 2px solid #007bff;
}

.cantidad-icono {
    font-size: 4rem;
    color: #007bff;
    margin-bottom: 20px;
}

.cantidad-texto {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .productos-form-container {
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
}
');
?>

<div class="productos-form">
    <div class="productos-form-container">
        
        <!-- Indicador de Pasos -->
        <div class="stepper-container">
            <div class="stepper">
                <div class="stepper-progress" id="stepper-progress"></div>
                
                <div class="step active" data-step="1">
                    <div class="step-circle">
                        <span class="step-number">1</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Imágenes</div>
                </div>
                
                <div class="step" data-step="2">
                    <div class="step-circle">
                        <span class="step-number">2</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Información</div>
                </div>
                
                <div class="step" data-step="3">
                    <div class="step-circle">
                        <span class="step-number">3</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Medidas</div>
                </div>
                
                <div class="step" data-step="4">
                    <div class="step-circle">
                        <span class="step-number">4</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Precios</div>
                </div>
                
                <div class="step" data-step="5">
                    <div class="step-circle">
                        <span class="step-number">5</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Ubicación</div>
                </div>
                
                <div class="step" data-step="6">
                    <div class="step-circle">
                        <span class="step-number">6</span>
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-label">Cantidad</div>
                </div>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'productos-form',
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ],
        ]); ?>

        <!-- PASO 1: Subida de Imágenes -->
        <div class="step-content active" data-step="1">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-image"></i>
                    Imágenes del Producto
                </h3>
                <p class="section-description">
                    Comienza subiendo las imágenes de tu producto. Puedes agregar hasta 10 imágenes.
                </p>
                
                <?php if (!$model->isNewRecord && !empty($model->fotos)): ?>
                    <div class="existing-images">
                        <?php 
                        $fotosArray = json_decode($model->fotos, true);
                        if (is_array($fotosArray)) {
                            foreach ($fotosArray as $foto): ?>
                                <div class="existing-image-item">
                                    <?= Html::img(Yii::getAlias('@web') . '/' . $foto, [
                                        'alt' => 'Imagen del producto'
                                    ]) ?>
                                </div>
                            <?php endforeach;
                        }
                        ?>
                    </div>
                    <p class="mt-3 text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Al subir nuevas imágenes, se agregarán a las existentes
                    </p>
                <?php endif; ?>

                <div class="file-upload-container" onclick="document.getElementById('productos-imagefiles').click()">
                    <div class="file-upload-icon">
                        <i class="bi bi-cloud-upload"></i>
                    </div>
                    <div class="file-upload-text">
                        <strong>Haz clic para seleccionar imágenes</strong>
                    </div>
                    <div class="file-upload-hint">
                        Formatos aceptados: PNG, JPG, JPEG, GIF, WEBP<br>
                        Máximo 10 archivos
                    </div>
                </div>

                <?= $form->field($model, 'imageFiles[]')->fileInput([
                    'multiple' => true,
                    'accept' => 'image/*',
                    'id' => 'productos-imagefiles'
                ])->label(false) ?>
            </div>
        </div>

        <!-- PASO 2: Información Básica -->
        <div class="step-content" data-step="2">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Información Básica del Producto
                </h3>
                <p class="section-description">
                    Proporciona la información general que identifica tu producto.
                </p>
                
                <div class="form-row">
                    <?= $form->field($model, 'marca')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: Samsung, Apple, Nike...'
                    ]) ?>

                    <?= $form->field($model, 'modelo')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: Galaxy S21, iPhone 13...'
                    ]) ?>
                </div>

                <div class="form-row">
                    <?= $form->field($model, 'color')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: Negro, Blanco, Azul...'
                    ]) ?>

                    <?= $form->field($model, 'sku')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Código SKU del producto'
                    ]) ?>
                </div>

                <?= $form->field($model, 'descripcion')->textarea([
                    'rows' => 4,
                    'placeholder' => 'Describe las características principales del producto...'
                ]) ?>
            </div>
        </div>

        <!-- PASO 3: Especificaciones y Medidas -->
        <div class="step-content" data-step="3">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-rulers"></i>
                    Especificaciones y Medidas
                </h3>
                <p class="section-description">
                    Define las características físicas y medidas del producto.
                </p>
                
                <div class="form-row">
                    <?= $form->field($model, 'contenido_neto')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'placeholder' => 'Ej: 500'
                    ]) ?>

                    <?= $form->field($model, 'unidad_medida')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: ml, gr, kg, unidades...'
                    ]) ?>
                </div>

                <?= $form->field($model, 'codigo_barra')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Código de barras del producto'
                ]) ?>
            </div>
        </div>

        <!-- PASO 4: Precios y Costos -->
        <div class="step-content" data-step="4">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-cash-stack"></i>
                    Precios y Costos
                </h3>
                <p class="section-description">
                    Establece el costo de adquisición y el precio de venta del producto.
                </p>
                
                <div class="form-row">
                    <?= $form->field($model, 'costo')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'placeholder' => '0.00'
                    ])->hint('Costo de adquisición del producto') ?>

                    <?= $form->field($model, 'precio_venta')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'placeholder' => '0.00'
                    ])->hint('Precio de venta al público') ?>
                </div>
            </div>
        </div>

        <!-- PASO 5: Ubicación y Categorización -->
        <div class="step-content" data-step="5">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-geo-alt"></i>
                    Ubicación y Categorización
                </h3>
                <p class="section-description">
                    Define dónde se almacenará el producto y a qué categoría pertenece.
                </p>
                
                <div class="form-row">
                    <?= $form->field($model, 'id_lugar')->dropDownList($lugares, [
                        'prompt' => '-- Selecciona un lugar --',
                        'class' => 'form-control'
                    ])->hint('Lugar donde se almacenará el producto') ?>

                    <?= $form->field($model, 'id_categoria')->dropDownList($categorias, [
                        'prompt' => '-- Selecciona una categoría --',
                        'class' => 'form-control'
                    ])->hint('Categoría a la que pertenece el producto') ?>
                </div>
            </div>
        </div>

        <!-- PASO 6: Cantidad Inicial -->
        <div class="step-content" data-step="6">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-box-seam"></i>
                    Cantidad Inicial en el Inventario
                </h3>
                <p class="section-description">
                    <?php if ($model->isNewRecord): ?>
                        Especifica la cantidad inicial del producto para crear el stock en el inventario.
                    <?php else: ?>
                        Este producto ya existe. Para modificar el stock, usa la sección de inventario.
                    <?php endif; ?>
                </p>
                
                <?php if ($model->isNewRecord): ?>
                    <div class="cantidad-resumen">
                        <div class="cantidad-icono">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div class="cantidad-texto">
                            Ingresa la cantidad inicial de unidades que agregarás al inventario
                        </div>
                        
                        <?= $form->field($model, 'cantidad')->textInput([
                            'type' => 'number',
                            'min' => '0',
                            'placeholder' => '0',
                            'style' => 'max-width: 300px; margin: 0 auto; font-size: 1.5rem; text-align: center; height: 60px;'
                        ])->label('Cantidad de Unidades', ['style' => 'font-size: 1.2rem; margin-bottom: 15px;']) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" style="border-radius: 15px; padding: 30px; text-align: center;">
                        <i class="bi bi-info-circle" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                        <h4>Producto Existente</h4>
                        <p>Para modificar el stock de este producto, dirígete a la sección de gestión de inventario.</p>
                    </div>
                <?php endif; ?>
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
                <?= $model->isNewRecord ? 'Crear Producto' : 'Actualizar Producto' ?>
            </button>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
// JavaScript para manejo de pasos
$totalSteps = 6;
$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};

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
    document.querySelector('.productos-form-container').scrollIntoView({ 
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

// Preview de imágenes seleccionadas
document.getElementById('productos-imagefiles').addEventListener('change', function(e) {
    const container = document.querySelector('.file-upload-container');
    const files = e.target.files;
    
    if (files.length > 0) {
        let fileNames = [];
        for (let i = 0; i < files.length; i++) {
            fileNames.push(files[i].name);
        }
        
        container.querySelector('.file-upload-text').innerHTML = 
            '<strong>' + files.length + ' archivo(s) seleccionado(s)</strong>';
        container.querySelector('.file-upload-hint').innerHTML = 
            fileNames.slice(0, 3).join(', ') + (files.length > 3 ? ' y ' + (files.length - 3) + ' más...' : '');
    }
});

// Inicializar
updateStep();
JS;
$this->registerJs($js);
?>