<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Productos;
use app\models\Lugares;
use app\models\Clientes;
use app\models\Stock;

/** @var yii\web\View $this */
/** @var app\models\Salidas $model */
/** @var yii\widgets\ActiveForm $form */

// Obtener listas para dropdowns
$lugares = ArrayHelper::map(Lugares::find()->all(), 'id', 'nombre');
$clientes = ArrayHelper::map(Clientes::find()->all(), 'id', 'nombre');

// CSS personalizado para el formulario con pasos
$this->registerCss('
.salidas-form-container {
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
    background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
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
    color: #dc3545;
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
    color: #dc3545;
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
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
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
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    margin-left: auto;
}

.btn-next:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
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

/* Tipo de Salida Cards */
.tipo-salida-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.tipo-salida-card {
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.tipo-salida-card:hover {
    border-color: #dc3545;
    background: #fdf2f2;
    transform: translateY(-5px);
}

.tipo-salida-card.selected {
    border-color: #dc3545;
    background: linear-gradient(135deg, #fdf2f2 0%, #f8d7da 100%);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.tipo-salida-card i {
    font-size: 3rem;
    color: #dc3545;
    margin-bottom: 15px;
}

.tipo-salida-card h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.tipo-salida-card p {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 8px;
    margin-bottom: 0;
}

/* Info de Stock */
.stock-info {
    background: linear-gradient(135deg, #fdf2f2 0%, #f8d7da 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    border: 2px solid #dc3545;
    margin-bottom: 20px;
}

.stock-icono {
    font-size: 4rem;
    color: #dc3545;
    margin-bottom: 20px;
}

.stock-numero {
    font-size: 3rem;
    font-weight: 700;
    color: #dc3545;
    margin-bottom: 10px;
}

.stock-texto {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 20px;
}

/* Campo readonly especial */
.form-control[readonly] {
    background-color: #f8f9fa;
    opacity: 1;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
    .salidas-form-container {
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
    
    .tipo-salida-options {
        grid-template-columns: 1fr;
    }
}
');
?>

<div class="salidas-form">
    <div class="salidas-form-container">
        
        <!-- Barra de Progreso -->
        <div class="progress-bar-container">
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" id="progress-bar-fill-salidas"></div>
            </div>
            <div class="progress-info">
                <span class="progress-step-text" id="progress-step-text-salidas">Paso 1 de 2: Tipo de Salida</span>
                <span class="progress-percentage" id="progress-percentage-salidas">0%</span>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'salidas-form',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
            ],
        ]); ?>

        <!-- PASO 1: Selección del Tipo de Salida -->
        <div class="step-content active" data-step="1">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-card-checklist"></i>
                    Selecciona el Tipo de Salida
                </h3>
                <p class="section-description">
                    Elige el tipo de salida que estás registrando en el inventario.
                </p>
                
                <div class="tipo-salida-options">
                    <div class="tipo-salida-card" data-tipo="Traspaso">
                        <i class="bi bi-arrow-left-right"></i>
                        <h4>Traspaso</h4>
                        <p>Movimiento de producto entre ubicaciones</p>
                    </div>
                    
                    <div class="tipo-salida-card" data-tipo="Descarte">
                        <i class="bi bi-trash"></i>
                        <h4>Por Caducación/No Existentes</h4>
                        <p>Eliminación de stock por caducidad o faltante</p>
                    </div>
                </div>

                <!-- Campo oculto para el tipo de salida -->
                <input type="hidden" id="tipo_salida" name="tipo_salida" value="">
            </div>
        </div>

        <!-- PASO 2A: Producto y Ubicación para Traspaso -->
        <div class="step-content" data-step="2" id="paso-traspaso">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-arrow-left-right"></i>
                    Traspaso de Producto
                </h3>
                <p class="section-description">
                    Selecciona el producto a traspasar, su ubicación actual y la nueva ubicación.
                </p>
                
                <div class="form-group">
                    <label class="control-label">Producto</label>
                    <button type="button" class="form-control text-start" id="btn-seleccionar-producto-traspaso" 
                            style="background: white; border: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                        <span id="producto-traspaso-text" style="color: #6c757d;">-- Selecciona un producto --</span>
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="control-label">Lugar Origen</label>
                        <select class="form-control" id="lugar-origen-display" disabled>
                            <option value="">-- Lugar origen --</option>
                            <?php foreach($lugares as $id => $nombre): ?>
                                <option value="<?= $id ?>"><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Lugar Destino</label>
                        <select class="form-control" id="lugar-destino">
                            <option value="">-- Selecciona destino --</option>
                            <?php foreach($lugares as $id => $nombre): ?>
                                <option value="<?= $id ?>"><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="stock-info" id="stock-info-traspaso" style="display: none;">
                    <div class="stock-icono">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div class="stock-numero" id="stock-disponible">0</div>
                    <div class="stock-texto">Unidades disponibles en ubicación actual</div>
                </div>

                <div class="form-group" style="text-align: center;">
                    <label class="control-label" style="font-size: 1.2rem; margin-bottom: 15px;">Cantidad a Traspasar</label>
                    <input type="number" class="form-control" id="cantidad-traspaso" 
                           min="1" placeholder="0" 
                           style="max-width: 300px; margin: 0 auto; font-size: 1.5rem; text-align: center; height: 60px;">
                </div>
            </div>
        </div>

        <!-- PASO 2B: Producto y Stock para Descarte -->
        <div class="step-content" data-step="2" id="paso-descarte">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-trash"></i>
                    Descarte por Caducación/No Existentes
                </h3>
                <p class="section-description">
                    Selecciona el producto, la ubicación específica y la cantidad a descartar.
                </p>
                
                <div class="form-group">
                    <label class="control-label">Producto</label>
                    <button type="button" class="form-control text-start" id="btn-seleccionar-producto-descarte" 
                            style="background: white; border: 2px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                        <span id="producto-descarte-text" style="color: #6c757d;">-- Selecciona un producto --</span>
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="form-group" id="ubicacion-descarte-group" style="display: none;">
                    <label class="control-label">Ubicación a Descartar</label>
                    <select class="form-control" id="ubicacion-descarte">
                        <option value="">-- Selecciona una ubicación --</option>
                    </select>
                </div>

                <div class="stock-info" id="stock-info-descarte" style="display: none;">
                    <div class="stock-icono">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div class="stock-numero" id="stock-disponible-descarte">0</div>
                    <div class="stock-texto">Stock disponible en esta ubicación</div>
                </div>

                <div class="form-group" style="text-align: center;">
                    <label class="control-label" style="font-size: 1.2rem; margin-bottom: 15px;">Cantidad a Descartar</label>
                    <input type="number" class="form-control" id="cantidad-descarte" 
                           min="1" placeholder="0" 
                           style="max-width: 300px; margin: 0 auto; font-size: 1.5rem; text-align: center; height: 60px;">
                </div>
            </div>
        </div>

        <!-- Campos ocultos para envío del formulario -->
        <input type="hidden" id="form-producto" name="Salidas[id_producto]" value="">
        <input type="hidden" id="form-cantidad" name="Salidas[cantidad]" value="">
        <input type="hidden" id="form-lugar-origen" name="Salidas[id_lugar_origen]" value="">
        <input type="hidden" id="form-lugar-destino" name="Salidas[id_lugar_destino]" value="">

        <!-- Campo oculto para is_movimiento -->
        <?= $form->field($model, 'is_movimiento')->hiddenInput(['id' => 'is_movimiento'])->label(false) ?>

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
            
            <button type="button" class="btn-step btn-submit-final" id="btn-submit" style="display: none;">
                <i class="bi bi-check-circle me-2"></i>
                Registrar Salida
            </button>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
// JavaScript para manejo de pasos
$js = <<<JS
let currentStep = 1;
const totalSteps = 2;
let tipoSalidaSeleccionado = null;

// URLs para AJAX
const stockInfoUrl = '?r=salidas/stock-info';
const productosUrl = '?r=salidas/productos-con-stock';

// Función para actualizar la vista de pasos
function updateStep() {
    // Nombres de los pasos
    const stepNames = [
        'Tipo de Salida',
        'Producto y Ubicación'
    ];
    
    // Ocultar todos los contenidos de pasos
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    if (currentStep === 1) {
        // Mostrar paso 1
        document.querySelector('.step-content[data-step="1"]').classList.add('active');
        document.querySelector('.step-content[data-step="1"]').style.display = 'block';
    } else if (currentStep === 2) {
        // Mostrar el paso correcto según el tipo de salida
        if (tipoSalidaSeleccionado === 'Traspaso') {
            document.getElementById('paso-traspaso').classList.add('active');
            document.getElementById('paso-traspaso').style.display = 'block';
        } else if (tipoSalidaSeleccionado === 'Descarte') {
            document.getElementById('paso-descarte').classList.add('active');
            document.getElementById('paso-descarte').style.display = 'block';
        }
    }
    
    // Actualizar barra de progreso
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    const progressBar = document.getElementById('progress-bar-fill-salidas');
    const progressText = document.getElementById('progress-step-text-salidas');
    const progressPercentage = document.getElementById('progress-percentage-salidas');
    
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
    document.querySelector('.salidas-form-container').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

// Función para cargar productos con stock
function cargarProductosConStock() {
    const selectTraspaso = document.getElementById('producto-traspaso');
    const selectDescarte = document.getElementById('producto-descarte');
    
    fetch(productosUrl)
        .then(response => response.json())
        .then(data => {
            // Limpiar opciones existentes
            if (selectTraspaso) {
                selectTraspaso.innerHTML = '<option value="">-- Selecciona un producto y ubicación --</option>';
            }
            if (selectDescarte) {
                selectDescarte.innerHTML = '<option value="">-- Selecciona un producto --</option>';
            }
            
            // Para traspaso: mostrar todas las combinaciones producto-ubicación
            if (selectTraspaso) {
                data.forEach(stock => {
                    const optionTraspaso = new Option(stock.texto, stock.id);
                    // Guardar datos adicionales en el option
                    optionTraspaso.dataset.idLugar = stock.id_lugar;
                    optionTraspaso.dataset.cantidad = stock.cantidad;
                    optionTraspaso.dataset.idStock = stock.id_stock;
                    selectTraspaso.add(optionTraspaso);
                });
            }
            
            // Para descarte: agrupar por producto (sin duplicados)
            if (selectDescarte) {
                const productosUnicos = new Map();
                data.forEach(stock => {
                    if (!productosUnicos.has(stock.id)) {
                        // Extraer nombre del producto sin la ubicación
                        const nombreProducto = stock.texto.split(' - ')[0];
                        productosUnicos.set(stock.id, nombreProducto);
                    }
                });
                
                productosUnicos.forEach((nombre, id) => {
                    const optionDescarte = new Option(nombre, id);
                    selectDescarte.add(optionDescarte);
                });
            }
        })
        .catch(error => {
            console.error('Error cargando productos:', error);
            alert('Error al cargar los productos. Por favor, recarga la página.');
        });
}

// Función para obtener información de stock
function obtenerInfoStock(productoId, callback) {
    if (!productoId) {
        callback(null);
        return;
    }
    
    fetch(stockInfoUrl + '&id=' + productoId)
        .then(response => response.json())
        .then(data => {
            callback(data);
        })
        .catch(error => {
            console.error('Error obteniendo stock:', error);
            callback(null);
        });
}

// Selección de tipo de salida
document.querySelectorAll('.tipo-salida-card').forEach(card => {
    card.addEventListener('click', function() {
        // Quitar selección de todas las cards
        document.querySelectorAll('.tipo-salida-card').forEach(c => {
            c.classList.remove('selected');
        });
        
        // Seleccionar la card clickeada
        this.classList.add('selected');
        tipoSalidaSeleccionado = this.getAttribute('data-tipo');
        document.getElementById('tipo_salida').value = tipoSalidaSeleccionado;
        
        // Configurar is_movimiento
        if (tipoSalidaSeleccionado === 'Traspaso') {
            document.getElementById('is_movimiento').value = '1';
        } else {
            document.getElementById('is_movimiento').value = '0';
        }
    });
});

// Función para configurar eventos de productos
function configurarEventosProductos() {
    // Evento para producto de traspaso
    const productoTraspaso = document.getElementById('producto-traspaso');
    if (productoTraspaso) {
        productoTraspaso.addEventListener('change', function() {
            const productoId = this.value;
            // Actualizar campo oculto
            document.getElementById('form-producto').value = productoId;
            
            if (productoId) {
                // Obtener datos desde el option seleccionado
                const selectedOption = this.options[this.selectedIndex];
                const idLugar = selectedOption.dataset.idLugar;
                const cantidad = selectedOption.dataset.cantidad;
                
                if (idLugar && cantidad) {
                    // Mostrar información del stock seleccionado
                    document.getElementById('stock-disponible').textContent = cantidad;
                    document.getElementById('lugar-origen-display').value = idLugar;
                    // Actualizar campo oculto
                    document.getElementById('form-lugar-origen').value = idLugar;
                    document.getElementById('stock-info-traspaso').style.display = 'block';
                    
                    // Configurar máximo en cantidad
                    document.getElementById('cantidad-traspaso').setAttribute('max', cantidad);
                    
                    // Limpiar cantidad anterior
                    document.getElementById('cantidad-traspaso').value = '';
                    document.getElementById('form-cantidad').value = '';
                }
            } else {
                document.getElementById('stock-info-traspaso').style.display = 'none';
                document.getElementById('form-lugar-origen').value = '';
                document.getElementById('cantidad-traspaso').value = '';
                document.getElementById('form-cantidad').value = '';
            }
        });
    }
    
    // Evento para lugar destino
    const lugarDestino = document.getElementById('lugar-destino');
    if (lugarDestino) {
        lugarDestino.addEventListener('change', function() {
            document.getElementById('form-lugar-destino').value = this.value;
        });
    }
    
    // Evento para cantidad traspaso
    const cantidadTraspaso = document.getElementById('cantidad-traspaso');
    if (cantidadTraspaso) {
        cantidadTraspaso.addEventListener('input', function() {
            document.getElementById('form-cantidad').value = this.value;
        });
    }
    
    // Evento para producto de descarte
    const productoDescarte = document.getElementById('producto-descarte');
    if (productoDescarte) {
        productoDescarte.addEventListener('change', function() {
            const productoId = this.value;
            // Actualizar campo oculto
            document.getElementById('form-producto').value = productoId;
            
            if (productoId) {
                // Obtener todas las ubicaciones con stock para este producto
                obtenerInfoStock(productoId, (data) => {
                    if (data && data.length > 0) {
                        // Mostrar dropdown de ubicaciones
                        const ubicacionSelect = document.getElementById('ubicacion-descarte');
                        ubicacionSelect.innerHTML = '<option value="">-- Selecciona una ubicación --</option>';
                        
                        data.forEach(stock => {
                            const option = new Option(
                                stock.lugar_nombre + ' (' + stock.cantidad + ' unidades)',
                                stock.id_lugar
                            );
                            option.dataset.cantidad = stock.cantidad;
                            ubicacionSelect.add(option);
                        });
                        
                        document.getElementById('ubicacion-descarte-group').style.display = 'block';
                    }
                });
            } else {
                document.getElementById('ubicacion-descarte-group').style.display = 'none';
                document.getElementById('stock-info-descarte').style.display = 'none';
            }
        });
    }
    
    // Evento para ubicación de descarte
    const ubicacionDescarte = document.getElementById('ubicacion-descarte');
    if (ubicacionDescarte) {
        ubicacionDescarte.addEventListener('change', function() {
            const ubicacionId = this.value;
            
            if (ubicacionId) {
                // Obtener cantidad disponible en esta ubicación
                const selectedOption = this.options[this.selectedIndex];
                const cantidadDisponible = selectedOption.dataset.cantidad;
                
                if (cantidadDisponible) {
                    document.getElementById('stock-disponible-descarte').textContent = cantidadDisponible;
                    document.getElementById('stock-info-descarte').style.display = 'block';
                    
                    // Configurar máximo en cantidad
                    document.getElementById('cantidad-descarte').setAttribute('max', cantidadDisponible);
                    
                    // Actualizar campo hidden para lugar origen (lugar del cual se descarta)
                    document.getElementById('form-lugar-origen').value = ubicacionId;
                }
            } else {
                document.getElementById('stock-info-descarte').style.display = 'none';
                document.getElementById('form-lugar-origen').value = '';
            }
        });
    }
    
    // Evento para cantidad descarte
    const cantidadDescarte = document.getElementById('cantidad-descarte');
    if (cantidadDescarte) {
        cantidadDescarte.addEventListener('input', function() {
            document.getElementById('form-cantidad').value = this.value;
        });
    }
}

// Botón siguiente
document.getElementById('btn-next').addEventListener('click', function() {
    // Validar paso actual antes de continuar
    if (currentStep === 1 && !tipoSalidaSeleccionado) {
        alert('Por favor, selecciona un tipo de salida');
        return;
    }
    
    if (currentStep < totalSteps) {
        currentStep++;
        updateStep();
        
        // Cargar productos al llegar al paso 2
        if (currentStep === 2) {
            cargarProductosConStock();
            configurarEventosProductos();
        }
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
        const targetStep = index + 1;
        if (targetStep === 1 || (targetStep > 1 && tipoSalidaSeleccionado)) {
            currentStep = targetStep;
            updateStep();
            
            // Cargar productos si es necesario
            if (currentStep === 2) {
                cargarProductosConStock();
                configurarEventosProductos();
            }
        }
    });
});

// Evento para el botón de envío
document.getElementById('btn-submit').addEventListener('click', function() {
    // Validar datos antes de enviar
    const producto = document.getElementById('form-producto').value;
    const cantidad = document.getElementById('form-cantidad').value;
    
    if (!producto) {
        alert('Por favor selecciona un producto');
        return;
    }
    
    if (!cantidad || cantidad <= 0) {
        alert('Por favor ingresa una cantidad válida');
        return;
    }
    
    if (tipoSalidaSeleccionado === 'Traspaso') {
        const lugarOrigen = document.getElementById('form-lugar-origen').value;
        const lugarDestino = document.getElementById('form-lugar-destino').value;
        
        if (!lugarOrigen) {
            alert('Error: No se pudo determinar el lugar origen');
            return;
        }
        
        if (!lugarDestino) {
            alert('Por favor selecciona el lugar destino');
            return;
        }
        
        if (lugarOrigen === lugarDestino) {
            alert('El lugar origen y destino no pueden ser el mismo');
            return;
        }
    } else if (tipoSalidaSeleccionado === 'Descarte') {
        const ubicacionDescarte = document.getElementById('ubicacion-descarte').value;
        
        if (!ubicacionDescarte) {
            alert('Por favor selecciona la ubicación de donde descartar');
            return;
        }
    }
    
    // Si llegamos aquí, todos los datos están válidos
    // Debug - ver qué datos se están enviando
    console.log('Datos a enviar:');
    console.log('- Producto:', document.getElementById('form-producto').value);
    console.log('- Cantidad:', document.getElementById('form-cantidad').value);
    console.log('- is_movimiento:', document.getElementById('is_movimiento').value);
    console.log('- Lugar origen:', document.getElementById('form-lugar-origen').value);
    console.log('- Lugar destino:', document.getElementById('form-lugar-destino').value);
    console.log('- Tipo salida:', tipoSalidaSeleccionado);
    
    // Enviar el formulario
    document.getElementById('salidas-form').submit();
});

// =============================================
// Botones para seleccionar producto mediante modal
// =============================================

// Traspaso
const btnSeleccionarProductoTraspaso = document.getElementById('btn-seleccionar-producto-traspaso');
if (btnSeleccionarProductoTraspaso) {
    btnSeleccionarProductoTraspaso.addEventListener('click', function() {
        if (typeof window.abrirModalSeleccionProducto === 'function') {
            window.abrirModalSeleccionProducto('salidas', function(producto) {
                // El modal en modo salidas devuelve el producto con stock_seleccionado
                const stock = producto.stock_seleccionado;
                
                // Actualizar campo de texto
                document.getElementById('producto-traspaso-text').textContent = 
                    (producto.marca || '') + ' ' + (producto.modelo || '') + ' - ' + (stock.lugar_nombre || '');
                document.getElementById('producto-traspaso-text').style.color = '#333';
                
                // Actualizar campos ocultos
                document.getElementById('form-producto').value = producto.id;
                document.getElementById('form-lugar-origen').value = stock.id_lugar;
                
                // Actualizar lugar origen display
                document.getElementById('lugar-origen-display').value = stock.id_lugar;
                
                // Mostrar información de stock
                document.getElementById('stock-disponible').textContent = stock.cantidad || 0;
                document.getElementById('stock-info-traspaso').style.display = 'block';
                
                // Configurar máximo en cantidad
                const cantidadInput = document.getElementById('cantidad-traspaso');
                cantidadInput.setAttribute('max', stock.cantidad);
                cantidadInput.value = '';
                document.getElementById('form-cantidad').value = '';
            });
        }
    });
}

// Descarte
const btnSeleccionarProductoDescarte = document.getElementById('btn-seleccionar-producto-descarte');
if (btnSeleccionarProductoDescarte) {
    btnSeleccionarProductoDescarte.addEventListener('click', function() {
        if (typeof window.abrirModalSeleccionProducto === 'function') {
            window.abrirModalSeleccionProducto('salidas', function(producto) {
                // El modal en modo salidas devuelve el producto con stock_seleccionado
                const stock = producto.stock_seleccionado;
                
                // Actualizar campo de texto
                document.getElementById('producto-descarte-text').textContent = 
                    (producto.marca || '') + ' ' + (producto.modelo || '') + ' - ' + (stock.lugar_nombre || '');
                document.getElementById('producto-descarte-text').style.color = '#333';
                
                // Actualizar campos ocultos
                document.getElementById('form-producto').value = producto.id;
                document.getElementById('form-lugar-origen').value = stock.id_lugar;
                
                // Mostrar información de stock
                document.getElementById('stock-disponible-descarte').textContent = stock.cantidad || 0;
                document.getElementById('stock-info-descarte').style.display = 'block';
                
                // Configurar máximo en cantidad
                const cantidadInput = document.getElementById('cantidad-descarte');
                cantidadInput.setAttribute('max', stock.cantidad);
                cantidadInput.value = '';
            });
        }
    });
}

// Inicializar
updateStep();
JS;
$this->registerJs($js);
?>
