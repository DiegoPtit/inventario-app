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
$this->registerCss(<<<CSS
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
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    position: relative;
}

.progress-bar-fill::after {
    content: '';
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
    color: #007bff;
}

.progress-percentage {
    font-weight: 500;
}

/* Input Groups con botones */
.input-group .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.input-group .btn {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    border-left: none !important;
    padding: 12px 20px;
}

.input-group:focus-within .btn {
    border-color: #007bff;
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
    .progress-bar-container {
        margin-bottom: 30px;
    }
    
    .progress-info {
        font-size: 0.85rem;
    }
    
    /* Ajustes generales del formulario */
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .section-title {
        font-size: 1.25rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .section-title i {
        font-size: 1.5rem;
    }
    
    .section-description {
        font-size: 0.9rem;
    }

    .file-upload-container {
        padding: 30px 15px;
        min-height: 200px;
    }

    .file-upload-icon {
        font-size: 3rem;
    }

    /* Navegación fija en la parte inferior para móvil (opcional, pero mejora UX) */
    .step-navigation {
        flex-direction: column-reverse; /* Botón principal arriba */
        gap: 10px;
        position: sticky;
        bottom: 0;
        background: white;
        padding: 15px 0;
        margin-top: 20px;
        border-top: 1px solid #f0f0f0;
        z-index: 100;
    }
    
    .btn-step {
        width: 100%;
        justify-content: center;
        padding: 14px;
    }
    
    .btn-next,
    .btn-submit-final {
        margin-left: 0;
    }
}
CSS
);
?>

<div class="productos-form">
    <!-- Barra de Progreso -->
    <div class="progress-bar-container">
        <div class="progress-bar-wrapper">
            <div class="progress-bar-fill" id="progress-bar-fill"></div>
        </div>
        <div class="progress-info">
            <span class="progress-step-text" id="progress-step-text">Paso 1 de 6: Imágenes</span>
            <span class="progress-percentage" id="progress-percentage">0%</span>
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

                <?= $form->field($model, 'codigo_barra')->hiddenInput(['id' => 'productos-codigo_barra'])->label(false) ?>
                
                <div class="form-group field-productos-codigo_barra">
                    <label class="control-label" for="productos-codigo_barra">Código de Barras</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="codigo_barra_display" placeholder="Código de barras del producto" readonly style="background-color: #f8f9fa;">
                        <button type="button" class="btn btn-primary" id="btn-leer-codigo" data-bs-toggle="modal" data-bs-target="#modalLectorCodigoBarras">
                            <i class="bi bi-upc-scan"></i> Leer Código
                        </button>
                    </div>
                    <div class="hint-block">Use el botón para escanear el código de barras con la cámara</div>
                </div>
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
                        'placeholder' => '0.00',
                        'id' => 'input-costo'
                    ])->hint('Costo de adquisición del producto') ?>

                    <?= $form->field($model, 'precio_venta')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'placeholder' => '0.00',
                        'id' => 'input-precio-venta',
                        'readonly' => true,
                        'style' => 'background-color: #f8f9fa;'
                    ])->hint('Se calcula automáticamente (doble del costo)') ?>
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
                    <div class="form-group field-productos-id_lugar">
                        <label class="control-label" for="productos-id_lugar">Id Lugar</label>
                        <div class="input-group">
                            <?= Html::activeDropDownList($model, 'id_lugar', $lugares, [
                                'prompt' => '-- Selecciona un lugar --',
                                'class' => 'form-control',
                                'id' => 'productos-id_lugar'
                            ]) ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoLugar">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <div class="hint-block">Lugar donde se almacenará el producto</div>
                    </div>

                    <div class="form-group field-productos-id_categoria">
                        <label class="control-label" for="productos-id_categoria">Id Categoria</label>
                        <div class="input-group">
                            <?= Html::activeDropDownList($model, 'id_categoria', $categorias, [
                                'prompt' => '-- Selecciona una categoría --',
                                'class' => 'form-control',
                                'id' => 'productos-id_categoria'
                            ]) ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <div class="hint-block">Categoría a la que pertenece el producto</div>
                    </div>
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

<?php
// JavaScript para manejo de pasos
$totalSteps = 6;
$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};

// Función para actualizar la vista de pasos
function updateStep() {
    // Nombres de los pasos
    const stepNames = [
        'Imágenes',
        'Información',
        'Medidas',
        'Precios',
        'Ubicación',
        'Cantidad'
    ];
    
    // Actualizar contenido
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector('.step-content[data-step="' + currentStep + '"]').classList.add('active');
    
    // Actualizar barra de progreso
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    const progressBar = document.getElementById('progress-bar-fill');
    const progressText = document.getElementById('progress-step-text');
    const progressPercentage = document.getElementById('progress-percentage');
    
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
    
    // Scroll suave hacia arriba
    const formContainer = document.querySelector('.productos-form');
    if (formContainer) {
        formContainer.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }
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

// Calcular precio de venta automáticamente (doble del costo)
const inputCosto = document.getElementById('input-costo');
const inputPrecioVenta = document.getElementById('input-precio-venta');

if (inputCosto && inputPrecioVenta) {
    inputCosto.addEventListener('input', function() {
        const costo = parseFloat(this.value) || 0;
        const precioVenta = costo * 2;
        inputPrecioVenta.value = precioVenta.toFixed(2);
    });
    
    // Calcular al cargar si ya hay un valor
    if (inputCosto.value) {
        const costo = parseFloat(inputCosto.value) || 0;
        const precioVenta = costo * 2;
        inputPrecioVenta.value = precioVenta.toFixed(2);
    }
}

// Inicializar
updateStep();

// Sincronizar código de barras display con el campo oculto al cargar
const codigoBarraHidden = document.getElementById('productos-codigo_barra');
const codigoBarraDisplay = document.getElementById('codigo_barra_display');

if (codigoBarraHidden && codigoBarraDisplay && codigoBarraHidden.value) {
    codigoBarraDisplay.value = codigoBarraHidden.value;
}

JS;
$this->registerJs($js);
?>