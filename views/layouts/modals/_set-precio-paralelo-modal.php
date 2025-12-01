<!-- Modal Actualizar Precio Paralelo -->
<div class="modal fade" id="modalActualizarPrecioParalelo" tabindex="-1" aria-labelledby="modalActualizarPrecioParaleloLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px; border: 1px solid #dee2e6;">
            <div class="modal-header" style="border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">
                <h5 class="modal-title fw-semibold" id="modalActualizarPrecioParaleloLabel">
                    Actualizar Precio Paralelo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="alert alert-light border-start border-4 border-warning mb-4" role="alert" style="background-color: #fffbf0;">
                    <strong class="d-block mb-1">Importante</strong>
                    <small class="text-muted">Ingrese el precio actual del dólar paralelo obtenido de fuentes confiables como Binance P2P, AirTM, o casas de cambio locales.</small>
                </div>

                <form id="form-actualizar-precio-paralelo">
                    <div class="mb-3">
                        <label class="form-label" for="precio-paralelo-input">
                            Precio Paralelo (VES)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">VES</span>
                            <input type="number" step="0.01" class="form-control" id="precio-paralelo-input" name="precio_paralelo" placeholder="Ej: 179.50" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="observaciones-precio-paralelo">
                            Observaciones <small class="text-muted">(Opcional)</small>
                        </label>
                        <textarea class="form-control" id="observaciones-precio-paralelo" name="observaciones" rows="3" placeholder="Ej: Precio obtenido de Binance P2P"></textarea>
                    </div>

                    <div class="form-check p-3 mb-0" style="background-color: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                        <input class="form-check-input" type="checkbox" id="confirmar-actualizacion-precio" name="confirmar_actualizacion" value="1">
                        <label class="form-check-label" for="confirmar-actualizacion-precio">
                            Confirmo que el precio ingresado es correcto
                        </label>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; background-color: #f8f9fa;">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-actualizacion-precio">
                    Actualizar Precio
                </button>
            </div>
        </div>
    </div>
</div>