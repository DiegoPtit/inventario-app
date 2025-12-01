<!-- Modal Cierre de Inventario -->
<div class="modal fade" id="modalCierreInventario" tabindex="-1" aria-labelledby="modalCierreInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
            <!-- Header -->
            <div class="modal-header" style="border-bottom: 1px solid #e9ecef; padding: 24px; background-color: #f8f9fa;">
                <div>
                    <h5 class="modal-title mb-1" id="modalCierreInventarioLabel" style="font-weight: 600; color: #212529;">
                        Cierre de Inventario
                    </h5>
                    <small class="text-muted">Registrar resumen del período</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Body -->
            <div class="modal-body" style="padding: 24px; background-color: #ffffff;">
                <!-- Loading State -->
                <div id="loading-cierre" class="text-center py-5">
                    <div class="spinner-border" style="color: #6c757d; width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted mb-0">Calculando datos del inventario...</p>
                </div>
                
                <!-- Form -->
                <form id="form-cierre-inventario" style="display: none;">
                    <!-- Info Alert -->
                    <div class="mb-4 p-3" style="background-color: #f4f6d9ff; border-left: 3px solid #817b3eff; border-radius: 6px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle" style="color: #6c757d; font-size: 1.1rem; margin-top: 2px;"></i>
                            <small class="text-muted mb-0" style="line-height: 1.6;">
                                Este proceso cerrará el período de inventario actual y registrará un resumen de las entradas realizadas en el rango de fechas especificado.
                            </small>
                        </div>
                    </div>

                    <!-- Período Card -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px;">
                            Período
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label mb-2" style="font-size: 0.875rem; color: #495057;">
                                    Fecha Inicio
                                </label>
                                <input type="datetime-local" class="form-control" id="cierre-fecha-inicio" name="fecha_inicio" readonly 
                                       style="background-color: #f8f9fa; border-color: #dee2e6; font-size: 0.875rem;">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label mb-2" style="font-size: 0.875rem; color: #495057;">
                                    Fecha Cierre
                                </label>
                                <input type="datetime-local" class="form-control" id="cierre-fecha-cierre" name="fecha_cierre" readonly
                                       style="background-color: #f8f9fa; border-color: #dee2e6; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Separador -->
                    <hr style="margin: 24px 0; border-color: #e9ecef;">

                    <!-- Resumen Card -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px;">
                            Resumen
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <small class="text-muted" style="font-size: 0.813rem;">Cantidad de Productos</small>
                                        <i class="bi bi-box-seam" style="color: #6c757d;"></i>
                                    </div>
                                    <input type="number" class="form-control border-0 p-0" id="cierre-cantidad" name="cantidad_productos" readonly
                                           style="background-color: transparent; font-size: 1.5rem; font-weight: 600; color: #212529;">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="p-3" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <small class="text-muted" style="font-size: 0.813rem;">Valor Total</small>
                                        <i class="bi bi-cash-stack" style="color: #6c757d;"></i>
                                    </div>
                                    <div class="input-group border-0 p-0" style="background-color: transparent;">
                                        <span class="input-group-text border-0 p-0 pe-1" style="background-color: transparent; font-size: 1.5rem; font-weight: 600; color: #212529;">$</span>
                                        <input type="number" step="0.01" class="form-control border-0 p-0" id="cierre-valor" name="valor" readonly
                                               style="background-color: transparent; font-size: 1.5rem; font-weight: 600; color: #212529;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador -->
                    <hr style="margin: 24px 0; border-color: #e9ecef;">

                    <!-- Observaciones -->
                    <div class="mb-4">
                        <label class="form-label mb-2" style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px;">
                            Observaciones
                        </label>
                        <textarea class="form-control" id="cierre-nota" name="nota" rows="3" 
                                  placeholder="Ingrese cualquier observación relevante..."
                                  style="resize: none; border-color: #dee2e6; font-size: 0.875rem;"></textarea>
                    </div>

                    <!-- Confirmación -->
                    <div class="p-3" style="background-color: #fffbf0; border: 1px solid #ffc107; border-radius: 8px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cierre-confirmar" name="confirmar_cierre" value="1"
                                   style="border-color: #ffc107; margin-top: 0.35rem;">
                            <label class="form-check-label" for="cierre-confirmar" style="font-size: 0.875rem; color: #212529; line-height: 1.6;">
                                <strong>Confirmo que deseo cerrar el inventario</strong>
                                <small class="d-block text-muted mt-1" style="font-size: 0.813rem;">
                                    Esta acción creará un registro permanente del cierre de inventario.
                                </small>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 16px 24px; background-color: #f8f9fa;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="font-size: 0.875rem; padding: 8px 20px;">
                    Cancelar
                </button>
                <button type="button" class="btn btn-dark" id="btn-confirmar-cierre" disabled style="font-size: 0.875rem; padding: 8px 20px;">
                    Registrar Cierre
                </button>
            </div>
        </div>
    </div>
</div>