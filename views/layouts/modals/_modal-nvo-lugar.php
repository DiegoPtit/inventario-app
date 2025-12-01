<!-- Modal Nuevo Lugar -->
<div class="modal fade" id="modalNuevoLugar" tabindex="-1" aria-labelledby="modalNuevoLugarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNuevoLugarLabel">
                    <i class="bi bi-geo-alt-fill"></i>
                    Registrar Nuevo Lugar/Almacén
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                
                <!-- Barra de Progreso -->
                <div class="progress-bar-container-lugar mb-4">
                    <div class="progress-bar-wrapper-lugar">
                        <div class="progress-bar-fill-lugar" id="progress-bar-fill-lugar"></div>
                    </div>
                    <div class="progress-info-lugar">
                        <span class="progress-step-text-lugar" id="progress-step-text-lugar">Paso 1 de 2: Información Básica</span>
                        <span class="progress-percentage-lugar" id="progress-percentage-lugar">0%</span>
                    </div>
                </div>

                <form id="form-nuevo-lugar">
                   <!-- PASO 1: Información Básica -->
                    <div class="step-content-lugar active" data-step="1">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-geo-alt text-primary"></i>
                            Información Básica
                        </h5>
                        <p class="text-muted mb-4">Comienza ingresando la información básica del lugar.</p>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nombre del Lugar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lugar-nombre" name="nombre" placeholder="Nombre del lugar" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" id="lugar-descripcion" name="descripcion" rows="4" placeholder="Descripción detallada del lugar, sus características, uso..."></textarea>
                        </div>
                    </div>

                    <!-- PASO 2: Ubicación -->
                    <div class="step-content-lugar" data-step="2">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-pin-map text-primary"></i>
                            Ubicación y Detalles
                        </h5>
                        <p class="text-muted mb-4">Proporciona la ubicación física del lugar.</p>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" class="form-control" id="lugar-ubicacion" name="ubicacion" placeholder="Dirección o ubicación física completa">
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" id="btn-lugar-previous" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btn-lugar-next">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="btn-lugar-submit" style="display: none;">
                    <i class="bi bi-check-circle"></i> Registrar Lugar
                </button>
            </div>
        </div>
    </div>
</div>