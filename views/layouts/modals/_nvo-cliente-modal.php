
<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNuevoClienteLabel">
                    <i class="bi bi-person-plus-fill"></i>
                    Registrar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                
                <!-- Barra de Progreso -->
                <div class="progress-bar-container-modal mb-4">
                    <div class="progress-bar-wrapper-modal">
                        <div class="progress-bar-fill-modal" id="progress-bar-fill-modal"></div>
                    </div>
                    <div class="progress-info-modal">
                        <span class="progress-step-text-modal" id="progress-step-text-modal">Paso 1 de 3: Personal</span>
                        <span class="progress-percentage-modal" id="progress-percentage-modal">0%</span>
                    </div>
                </div>

                <form id="form-nuevo-cliente-modal">
                    <!-- PASO 1: Información Personal -->
                    <div class="step-content-modal active" data-step="1">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-person-vcard text-primary"></i>
                            Información Personal
                        </h5>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal-cliente-nombre" name="nombre" placeholder="Nombre completo del cliente" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Documento de Identidad</label>
                                <input type="text" class="form-control" id="modal-cliente-documento" name="documento_identidad" placeholder="Cédula, RNC, Pasaporte...">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Edad</label>
                                <input type="number" class="form-control" id="modal-cliente-edad" name="edad" min="0" max="150" placeholder="Edad del cliente">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: Información de Contacto -->
                    <div class="step-content-modal" data-step="2">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-telephone text-primary"></i>
                            Información de Contacto
                        </h5>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="tel" class="form-control" id="modal-cliente-telefono" name="telefono" placeholder="Número de teléfono">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" class="form-control" id="modal-cliente-ubicacion" name="ubicacion" placeholder="Dirección completa del cliente">
                        </div>
                    </div>

                    <!-- PASO 3: Estado del Cliente -->
                    <div class="step-content-modal" data-step="3">
                        <h5 class="section-title-modal mb-3">
                            <i class="bi bi-clipboard-check text-primary"></i>
                            Estado del Cliente
                        </h5>
                        
                        <input type="hidden" id="modal-cliente-status" name="status" value="Solvente">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="status-card-modal status-solvente selected" data-status="Solvente">
                                    <div class="status-icon-modal text-success">
                                        <i class="bi bi-check-circle-fill fs-1"></i>
                                    </div>
                                    <div class="status-title-modal fw-bold">Solvente</div>
                                    <small class="text-muted">Cliente al día con sus pagos</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="status-card-modal status-moroso" data-status="Moroso">
                                    <div class="status-icon-modal text-danger">
                                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                    </div>
                                    <div class="status-title-modal fw-bold">Moroso</div>
                                    <small class="text-muted">Cliente con pagos pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" id="btn-modal-previous" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btn-modal-next">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="btn-modal-submit" style="display: none;">
                    <i class="bi bi-check-circle"></i> Registrar Cliente
                </button>
            </div>
        </div>
    </div>
</div>