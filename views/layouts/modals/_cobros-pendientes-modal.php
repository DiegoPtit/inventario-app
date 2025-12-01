<!-- Modal Recordatorio de Cobros Pendientes -->
<div class="modal fade" id="modalRecordatorioCobros" tabindex="-1" aria-labelledby="modalRecordatorioCobrosLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #dee2e6; background: #dc3545; color: white; border-radius: 12px 12px 0 0; padding: 14px 20px;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalRecordatorioCobrosLabel" style="font-size: 1rem; font-weight: 600;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Recordatorio: Facturas Pendientes de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="loading-cobros-pendientes" class="text-center py-4">
                    <div class="spinner-border text-danger" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted" style="font-size: 0.9rem;">Cargando clientes con facturas pendientes...</p>
                </div>
                
                <div id="contenido-cobros-pendientes" style="display: none;">
                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert" style="padding: 12px 14px; border-left: 3px solid #ffc107; border-radius: 6px; background: #fff9e6; border: 1px solid #ffe69c;">
                        <i class="bi bi-info-circle-fill" style="font-size: 1.1rem;"></i>
                        <div style="font-size: 0.85rem;">
                            <strong style="font-size: 0.9rem;">Recordatorio de Cobros</strong><br>
                            Los siguientes clientes tienen facturas pendientes de pago. Haz clic en cada cliente para ver sus facturas.
                        </div>
                    </div>
                    
                    <div class="accordion" id="accordionClientesPendientes">
                        <!-- Los clientes se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 12px 20px;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="padding: 6px 16px; font-size: 0.85rem;">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>