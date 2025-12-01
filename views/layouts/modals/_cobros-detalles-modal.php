<!-- Modal Detalles de Cobros -->
<div class="modal fade" id="modalDetallesCobros" tabindex="-1" aria-labelledby="modalDetallesCobrosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalDetallesCobrosLabel">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span id="modal-titulo-factura">Detalles de Pagos</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <!-- Resumen de la factura -->
                <div class="invoice-summary-modal mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="summary-item-modal">
                                <span class="summary-label-modal"><i class="bi bi-person"></i> Cliente:</span>
                                <span class="summary-value-modal" id="modal-cliente-nombre">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item-modal">
                                <span class="summary-label-modal"><i class="bi bi-calendar3"></i> Fecha:</span>
                                <span class="summary-value-modal" id="modal-factura-fecha">-</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="summary-item-modal">
                                <span class="summary-label-modal"><i class="bi bi-cash-stack"></i> Total Cobrado:</span>
                                <div>
                                    <span class="summary-value-modal text-success fw-bold" id="modal-total-cobrado">$0.00 <small class="text-muted" style="font-size: 0.65em;">(USDT)</small></span>
                                    <div id="modal-total-conversiones" style="font-size: 0.8rem; color: #6c757d; margin-top: 4px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Tarjetas de pagos -->
                <h6 class="mb-3 fw-bold text-secondary">
                    <i class="bi bi-list-check"></i> Operaciones de Pago
                </h6>
                <div id="modal-cobros-container">
                    <!-- Las tarjetas de cobros se cargarán aquí dinámicamente -->
                </div>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <a href="#" id="btn-ver-factura" class="btn btn-primary">
                    <i class="bi bi-eye"></i> Ver Factura
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>