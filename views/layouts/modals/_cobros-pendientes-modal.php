<!-- Modal Recordatorio de Cobros Pendientes -->
<div class="modal fade" id="modalRecordatorioCobros" tabindex="-1" aria-labelledby="modalRecordatorioCobrosLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalRecordatorioCobrosLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Recordatorio: Facturas Pendientes de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div id="loading-cobros-pendientes" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando clientes con facturas pendientes...</p>
                </div>
                
                <div id="contenido-cobros-pendientes" style="display: none;">
                    <div class="alert alert-warning d-flex align-items-start gap-3 mb-4" role="alert">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                        <div>
                            <strong>Recordatorio de Cobros</strong><br>
                            Los siguientes clientes tienen facturas pendientes de pago. Haz clic en cada cliente para ver sus facturas y gestionar los cobros.
                        </div>
                    </div>
                    
                    <div class="accordion" id="accordionClientesPendientes">
                        <!-- Los clientes se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>