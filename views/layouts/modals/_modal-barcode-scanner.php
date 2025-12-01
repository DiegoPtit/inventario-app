<!-- Modal Lector de Código de Barras -->
<div class="modal fade" id="modalLectorCodigoBarras" tabindex="-1" aria-labelledby="modalLectorCodigoBarrasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalLectorCodigoBarrasLabel">
                    <i class="bi bi-upc-scan"></i>
                    Lector de Código de Barras
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="alert alert-info d-flex align-items-start gap-3 mb-4" role="alert">
                    <i class="bi bi-info-circle-fill fs-4"></i>
                    <div>
                        <strong>Instrucciones:</strong><br>
                        Coloque el código de barras frente a la cámara. El sistema detectará automáticamente el código.
                    </div>
                </div>

                <!-- Canvas para la cámara -->
                <div id="barcode-scanner-container" style="position: relative; width: 100%; height: 400px; background: #000; border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
                    <div id="interactive" class="viewport" style="width: 100%; height: 100%;"></div>
                    <div id="scanner-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%; height: 60%; border: 2px solid #00ff00; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5); pointer-events: none;"></div>
                </div>

                <!-- Campo de texto con el código detectado -->
                <div id="barcode-result-container" style="display: none;">
                    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>¡Código detectado!</strong>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-bold">
                            <i class="bi bi-upc text-success"></i>
                            Código de Barras Detectado
                        </label>
                        <input type="text" class="form-control form-control-lg" id="barcode-result-input" readonly style="font-size: 1.25rem; font-weight: 600; text-align: center; background-color: #d4edda; border-color: #28a745;">
                    </div>
                </div>

                <!-- Estado del escáner -->
                <div id="scanner-status" class="text-center text-muted mt-3">
                    <i class="bi bi-camera-video"></i> Iniciando cámara...
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-usar-codigo" style="display: none;">
                    <i class="bi bi-check-circle"></i> Usar este Código
                </button>
            </div>
        </div>
    </div>
</div>