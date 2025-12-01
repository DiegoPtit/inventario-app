<!-- Modal Nueva Categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-labelledby="modalNuevaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNuevaCategoriaLabel">
                    <i class="bi bi-tags-fill"></i>
                    Registrar Nueva Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                
                <div class="alert alert-info d-flex align-items-start gap-3 mb-4" role="alert">
                    <i class="bi bi-info-circle-fill fs-4"></i>
                    <div>
                        <strong>Categoría:</strong> Agrupa productos similares para facilitar su organización y búsqueda.
                    </div>
                </div>

                <form id="form-nueva-categoria">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoria-titulo" name="titulo" placeholder="Nombre de la categoría" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="categoria-descripcion" name="descripcion" rows="4" placeholder="Descripción opcional de la categoría..."></textarea>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-categoria-submit">
                    <i class="bi bi-check-circle"></i> Registrar Categoría
                </button>
            </div>
        </div>
    </div>
</div>