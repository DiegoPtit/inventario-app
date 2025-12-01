<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Productos $model */

$this->title = 'Crear Nuevo Producto';

$this->registerCss('
.productos-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.productos-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.productos-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.productos-create-header i {
    font-size: 3rem;
    color: #28a745;
    margin-bottom: 15px;
}
');
?>
<div class="productos-create">
    
    <div class="productos-create-header">
        <i class="bi bi-box-seam"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Gestiona la creación de productos en el inventario</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mass-upload-tab" data-bs-toggle="tab" data-bs-target="#mass-upload" type="button" role="tab" aria-controls="mass-upload" aria-selected="true">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cargar productos en masa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="new-product-tab" data-bs-toggle="tab" data-bs-target="#new-product" type="button" role="tab" aria-controls="new-product" aria-selected="false">
                        <i class="bi bi-plus-circle me-2"></i>Cargar Nuevo Producto
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="productTabsContent">
                <!-- Tab: Cargar productos en masa -->
                <div class="tab-pane fade show active" id="mass-upload" role="tabpanel" aria-labelledby="mass-upload-tab">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="mb-3">Carga Masiva de Productos</h3>
                        <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
                            Selecciona un archivo para importar múltiples productos a la vez. 
                            Formatos aceptados: <strong>.csv, .txt</strong>
                        </p>
                        
                        <!-- Alert container for messages -->
                        <div id="upload-alert" style="max-width: 600px; margin: 0 auto 20px; display: none;"></div>
                        
                        <form id="mass-upload-form" class="d-inline-block text-start" style="max-width: 500px; width: 100%;" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="csvFile" class="form-label fw-bold">Archivo de datos</label>
                                <input class="form-control form-control-lg" type="file" id="csvFile" name="csvFile" accept=".csv, .txt" required>
                                <div class="form-text">Asegúrate de que el archivo siga la estructura requerida: marca,modelo,descripcion,cont_neto,mg,mijagua,milagro,dsamuel,costo</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="upload-btn" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="bi bi-upload me-2"></i>Cargar Archivo
                                </button>
                                <div id="upload-spinner" class="text-center mt-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Procesando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Procesando archivo, por favor espera...</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab: Cargar Nuevo Producto -->
                <div class="tab-pane fade" id="new-product" role="tabpanel" aria-labelledby="new-product-tab">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$this->registerCss('
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 15px 25px;
    border-radius: 10px 10px 0 0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-tabs .nav-link:hover {
    color: #007bff;
    background: rgba(0, 123, 255, 0.05);
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background: #fff;
    border-bottom: 3px solid #007bff;
}

.nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

/* Ajustes para móvil */
@media (max-width: 768px) {
    .card-header {
        padding: 15px !important;
    }

    .nav-tabs {
        display: flex;
        flex-direction: column;
        border-bottom: none;
        gap: 10px;
    }

    .nav-tabs .nav-item {
        width: 100%;
    }

    .nav-tabs .nav-link {
        width: 100%;
        border-radius: 10px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 15px;
        justify-content: flex-start;
    }

    .nav-tabs .nav-link.active {
        background-color: #e7f3ff;
        border-color: #007bff;
        border-bottom: 1px solid #007bff;
    }
    
    .productos-create-header h1 {
        font-size: 1.8rem;
    }
    
    .productos-create-header p {
        font-size: 0.95rem;
    }
}
');

// JavaScript for mass upload form
$this->registerJs("
$(document).ready(function() {
    $('#mass-upload-form').on('submit', function(e) {
        e.preventDefault();
        
        // Get the file input
        var fileInput = $('#csvFile')[0];
        if (!fileInput.files || !fileInput.files[0]) {
            showAlert('Por favor selecciona un archivo.', 'warning');
            return;
        }
        
        // Create FormData
        var formData = new FormData();
        formData.append('csvFile', fileInput.files[0]);
        
        // Show spinner, hide button
        $('#upload-btn').prop('disabled', true);
        $('#upload-spinner').show();
        $('#upload-alert').hide();
        
        // Send AJAX request
        $.ajax({
            url: '" . \yii\helpers\Url::to(['productos/process']) . "',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#upload-spinner').hide();
                $('#upload-btn').prop('disabled', false);
                
                if (response.success) {
                    showAlert(
                        '<strong>¡Éxito!</strong> ' + response.message + 
                        '<br><strong>Productos creados:</strong> ' + response.productosCreados + 
                        '<br><strong>Productos actualizados:</strong> ' + response.productosActualizados,
                        'success'
                    );
                    // Reset form
                    $('#mass-upload-form')[0].reset();
                } else {
                    var errorMsg = '<strong>Error:</strong> ' + response.message;
                    if (response.errores && response.errores.length > 0) {
                        errorMsg += '<br><br><strong>Detalles:</strong><ul>';
                        response.errores.forEach(function(error) {
                            errorMsg += '<li>' + error + '</li>';
                        });
                        errorMsg += '</ul>';
                    }
                    showAlert(errorMsg, 'danger');
                }
            },
            error: function(xhr, status, error) {
                $('#upload-spinner').hide();
                $('#upload-btn').prop('disabled', false);
                showAlert('<strong>Error de conexión:</strong> ' + error, 'danger');
            }
        });
    });
    
    function showAlert(message, type) {
        var alertHtml = '<div class=\"alert alert-' + type + ' alert-dismissible fade show\" role=\"alert\">' +
            message +
            '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>' +
            '</div>';
        $('#upload-alert').html(alertHtml).show();
    }
});
", \yii\web\View::POS_END);
?>
