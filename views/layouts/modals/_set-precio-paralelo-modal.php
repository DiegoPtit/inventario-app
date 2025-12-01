<!-- Modal Actualizar Precio Paralelo -->
<div class="modal fade" id="modalActualizarPrecioParalelo" tabindex="-1" aria-labelledby="modalActualizarPrecioParaleloLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalActualizarPrecioParaleloLabel">
                    <i class="bi bi-currency-exchange"></i>
                    Actualizar Precio Paralelo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="alert alert-warning d-flex align-items-start gap-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <div>
                        <strong>Importante:</strong> Ingrese el precio actual del dólar paralelo obtenido de fuentes confiables como Binance P2P, AirTM, o casas de cambio locales.
                    </div>
                </div>

                <form id="form-actualizar-precio-paralelo">
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-currency-dollar text-success"></i>
                            Precio Paralelo (VES)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">VES</span>
                            <input type="number" step="0.01" class="form-control" id="precio-paralelo-input" name="precio_paralelo" placeholder="Ej: 179.50" required>
                        </div>
                        <small class="text-muted">Ingrese el precio actual del dólar paralelo</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-pencil-square text-info"></i>
                            Observaciones (Opcional)
                        </label>
                        <textarea class="form-control" id="observaciones-precio-paralelo" name="observaciones" rows="3" placeholder="Fuente del precio, notas adicionales..."></textarea>
                        <small class="text-muted">Ej: "Precio obtenido de Binance P2P", "Casa de cambio local"</small>
                    </div>

                    <div class="form-check mb-3 p-3" style="background: #fff3cd; border-radius: 10px; border: 2px solid #ffc107;">
                        <input class="form-check-input" type="checkbox" id="confirmar-actualizacion-precio" name="confirmar_actualizacion" value="1">
                        <label class="form-check-label fw-bold" for="confirmar-actualizacion-precio">
                            <i class="bi bi-check-circle text-warning"></i>
                            Confirmo que el precio ingresado es correcto y actualizado
                        </label>
                        <small class="d-block mt-2 text-muted">
                            Esta acción actualizará el precio paralelo en el sistema.
                        </small>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btn-confirmar-actualizacion-precio" disabled>
                    <i class="bi bi-check-circle"></i> Actualizar Precio
                </button>
            </div>
        </div>
    </div>
</div>