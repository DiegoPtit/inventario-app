<!-- Modal Confirmación Rápida (POS Quick Mode) -->
<div class="modal fade" id="modalConfirmacionRapida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0 py-3">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-upc-scan me-2"></i>Producto Detectado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row g-3 g-md-4">
                    <!-- Columna Izquierda: Foto -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="bg-light rounded-3 p-2 p-md-3" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img id="modal-rapido-producto-foto" 
                                 src="<?= Yii::getAlias('@web') ?>/images/no-image.png" 
                                 alt="Producto" 
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 200px; max-width: 100%; object-fit: contain;"
                                 onerror="this.src='<?= Yii::getAlias('@web') ?>/images/no-image.png'">
                        </div>
                    </div>
                    
                    <!-- Columna Derecha: Detalles -->
                    <div class="col-12 col-md-8">
                        <h3 id="modal-rapido-producto-nombre" class="fw-bold mb-1 text-dark fs-5 fs-md-3"></h3>
                        <p id="modal-rapido-producto-descripcion" class="text-muted mb-3 mb-md-4 small"></p>
                        
                        <div class="row mb-3 mb-md-4">
                            <div class="col-6">
                                <label class="text-uppercase text-muted small fw-bold mb-1">Precio</label>
                                <div class="fs-5 fs-md-4 fw-bold text-dark" id="modal-rapido-producto-precio"></div>
                            </div>
                            <div class="col-6">
                                <label class="text-uppercase text-muted small fw-bold mb-1">Stock Total</label>
                                <div class="fs-5 fs-md-4 fw-bold text-dark" id="modal-rapido-producto-stock-total"></div>
                            </div>
                        </div>
                        
                        <div class="card bg-light border-0 mb-3 mb-md-4">
                            <div class="card-body p-3">
                                <!-- Selector de Ubicación -->
                                <div class="mb-3" id="modal-rapido-ubicacion-container">
                                    <label class="form-label fw-bold text-dark small">Ubicación</label>
                                    <select id="modal-rapido-ubicacion" class="form-select form-select-lg border-0 shadow-sm">
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <div class="mt-2 text-end">
                                        <small class="text-muted">Disponible: <span id="modal-rapido-stock-ubicacion" class="fw-bold text-dark">0</span></small>
                                    </div>
                                </div>
                                
                                <!-- Cantidad -->
                                <div class="row g-2 align-items-center">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-bold text-dark mb-2 mb-md-0 small">Cantidad a agregar</label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="input-group input-group-lg shadow-sm">
                                            <button class="btn btn-white border" type="button" onclick="document.getElementById('modal-rapido-cantidad').stepDown()">-</button>
                                            <input type="number" 
                                                   id="modal-rapido-cantidad" 
                                                   class="form-control text-center border-start-0 border-end-0" 
                                                   value="1" 
                                                   min="1">
                                            <button class="btn btn-white border" type="button" onclick="document.getElementById('modal-rapido-cantidad').stepUp()">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-3 pb-md-4 px-3 px-md-4">
                <div class="row w-100 g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-light btn-lg w-100" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-lg w-100" id="btn-confirmar-rapido">
                            <i class="bi bi-check-lg me-1 d-none d-md-inline"></i>Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>