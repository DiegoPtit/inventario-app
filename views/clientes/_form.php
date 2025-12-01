<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */
/** @var yii\widgets\ActiveForm $form */

// CSS personalizado para el formulario con pasos
$this->registerCss('
.clientes-form-container {
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
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
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
    color: #007bff;
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

/* Status Cards */
.status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.status-card {
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.status-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.status-card.selected {
    border-color: #007bff;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
}

.status-card.status-solvente.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
}

.status-card.status-moroso.selected {
    border-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
}

.status-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.status-card.status-solvente .status-icon {
    color: #28a745;
}

.status-card.status-moroso .status-icon {
    color: #dc3545;
}

.status-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
}

.status-description {
    font-size: 0.9rem;
    color: #6c757d;
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

/* Info Cards */
.info-card {
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
    border-radius: 15px;
    padding: 25px;
    border-left: 5px solid #007bff;
    margin-bottom: 20px;
}

.info-card i {
    color: #007bff;
    font-size: 1.5rem;
    margin-right: 10px;
}

.info-card-text {
    color: #495057;
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .clientes-form-container {
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

    .status-cards {
        grid-template-columns: 1fr;
    }
}
');
?>

<div class="clientes-form">
    <div class="clientes-form-container">
        
        <!-- Barra de Progreso -->
        <div class="progress-bar-container">
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" id="progress-bar-fill-clientes"></div>
            </div>
            <div class="progress-info">
                <span class="progress-step-text" id="progress-step-text-clientes">Paso 1 de 3: Información Personal</span>
                <span class="progress-percentage" id="progress-percentage-clientes">0%</span>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'clientes-form',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ],
        ]); ?>

        <!-- PASO 1: Información Personal -->
        <div class="step-content active" data-step="1">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-person-vcard"></i>
                    Información Personal
                </h3>
                <p class="section-description">
                    Comienza ingresando los datos personales del cliente.
                </p>
                
                <div class="info-card">
                    <i class="bi bi-info-circle"></i>
                    <span class="info-card-text">
                        El <strong>nombre</strong> es obligatorio. Los demás campos son opcionales pero recomendados para mantener un mejor registro.
                    </span>
                </div>

                <?= $form->field($model, 'nombre')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Nombre completo del cliente',
                    'autofocus' => true
                ]) ?>
                
                <div class="form-row">
                    <?= $form->field($model, 'documento_identidad')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Cédula, RNC, Pasaporte...'
                    ]) ?>

                    <?= $form->field($model, 'edad')->textInput([
                        'type' => 'number',
                        'min' => '0',
                        'max' => '150',
                        'placeholder' => 'Edad del cliente'
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- PASO 2: Información de Contacto -->
        <div class="step-content" data-step="2">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-telephone"></i>
                    Información de Contacto
                </h3>
                <p class="section-description">
                    Proporciona los datos de contacto y ubicación del cliente.
                </p>

                <div class="info-card">
                    <i class="bi bi-info-circle"></i>
                    <span class="info-card-text">
                        Esta información te permitirá comunicarte con el cliente y localizar su dirección.
                    </span>
                </div>
                
                <?= $form->field($model, 'telefono')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Número de teléfono',
                    'type' => 'tel'
                ]) ?>

                <?= $form->field($model, 'ubicacion')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Dirección completa del cliente'
                ]) ?>
            </div>
        </div>

        <!-- PASO 3: Estado del Cliente -->
        <div class="step-content" data-step="3">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-clipboard-check"></i>
                    Estado del Cliente
                </h3>
                <p class="section-description">
                    Define el estado de pago del cliente. Por defecto, todos los clientes se registran como "Solvente".
                </p>

                <div class="info-card">
                    <i class="bi bi-info-circle"></i>
                    <span class="info-card-text">
                        <strong>Solvente:</strong> Cliente al día con sus pagos.<br>
                        <strong>Moroso:</strong> Cliente con pagos pendientes o atrasados.
                    </span>
                </div>

                <?= $form->field($model, 'status')->hiddenInput(['id' => 'clientes-status-input'])->label(false) ?>

                <div class="status-cards">
                    <div class="status-card status-solvente" data-status="Solvente">
                        <div class="status-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="status-title">Solvente</div>
                        <div class="status-description">
                            Cliente al día con sus pagos
                        </div>
                    </div>

                    <div class="status-card status-moroso" data-status="Moroso">
                        <div class="status-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="status-title">Moroso</div>
                        <div class="status-description">
                            Cliente con pagos pendientes
                        </div>
                    </div>
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
                <?= $model->isNewRecord ? 'Registrar Cliente' : 'Actualizar Cliente' ?>
            </button>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
// JavaScript para manejo de pasos
$totalSteps = 3;
$defaultStatus = $model->isNewRecord ? 'Solvente' : $model->status;
$js = <<<JS
let currentStep = 1;
const totalSteps = {$totalSteps};

// Inicializar status por defecto
const defaultStatus = '{$defaultStatus}';
document.getElementById('clientes-status-input').value = defaultStatus;
document.querySelector('.status-card[data-status="' + defaultStatus + '"]').classList.add('selected');

// Función para actualizar la vista de pasos
function updateStep() {
    // Nombres de los pasos
    const stepNames = [
        'Información Personal',
        'Contacto',
        'Estado'
    ];
    
    // Actualizar contenido
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector('.step-content[data-step="' + currentStep + '"]').classList.add('active');
    
    // Actualizar barra de progreso
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    const progressBar = document.getElementById('progress-bar-fill-clientes');
    const progressText = document.getElementById('progress-step-text-clientes');
    const progressPercentage = document.getElementById('progress-percentage-clientes');
    
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
    document.querySelector('.clientes-form-container').scrollIntoView({ 
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

// Manejo de selección de status
document.querySelectorAll('.status-card').forEach(card => {
    card.addEventListener('click', function() {
        const status = this.getAttribute('data-status');
        
        // Remover selección de todas las tarjetas
        document.querySelectorAll('.status-card').forEach(c => c.classList.remove('selected'));
        
        // Seleccionar esta tarjeta
        this.classList.add('selected');
        
        // Actualizar el input hidden
        document.getElementById('clientes-status-input').value = status;
    });
});

// Inicializar
updateStep();
JS;
$this->registerJs($js);
?>