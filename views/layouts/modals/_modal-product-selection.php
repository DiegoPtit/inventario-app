<!-- Modal Selección de Producto -->
<div class="modal fade" id="modalSeleccionProducto" tabindex="-1" aria-labelledby="modalSeleccionProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border-radius: 20px 20px 0 0;">
                <div class="w-100">
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-3" id="modalSeleccionProductoLabel">
                        <i class="bi bi-box-seam"></i>
                        Seleccionar Producto
                    </h5>
                    <!-- Barra de búsqueda -->
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="busqueda-producto-modal" placeholder="Buscar por marca, modelo, descripción...">
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px; min-height: 400px;">
                
                <!-- Filtros de Lugares (solo para salidas) -->
                <div id="filtros-lugares-container" style="display: none;" class="mb-4">
                    <h6 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-funnel"></i> Filtrar por Ubicación
                    </h6>
                    <div class="d-flex flex-wrap gap-2" id="filtros-lugares">
                        <!-- Los filtros se cargarán dinámicamente -->
                    </div>
                </div>

                <!-- Loading -->
                <div id="loading-productos-modal" class="text-center py-5">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando productos...</p>
                </div>

                <!-- Contenedor de productos -->
                <div id="productos-grid-modal" class="row g-3" style="display: none;">
                    <!-- Las tarjetas de productos se cargarán aquí -->
                </div>

                <!-- Sin resultados -->
                <div id="sin-resultados-modal" style="display: none;" class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="mt-3 text-muted">No se encontraron productos</p>
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-cancelar-seleccion-producto">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-aceptar-seleccion-producto" disabled>
                    <i class="bi bi-check-circle"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>