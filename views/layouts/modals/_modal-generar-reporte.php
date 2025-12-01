<!-- Modal Generar Reporte -->
<div class="modal fade" id="modalGenerarReporte" tabindex="-1" aria-labelledby="modalGenerarReporteLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #e9ecef; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalGenerarReporteLabel">
                    <i class="bi bi-file-earmark-text"></i>
                    Generar Reporte de Inventario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="alert alert-info d-flex align-items-start gap-3" role="alert">
                    <i class="bi bi-info-circle-fill fs-4"></i>
                    <div>
                        <strong>Seleccione el tipo de reporte:</strong><br>
                        Puede generar un reporte general de todos los productos o seleccionar un almacén específico.
                    </div>
                </div>

                <form id="form-generar-reporte" method="get" action="" target="_blank">
                    <input type="hidden" name="r" value="productos/reporte">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text text-primary"></i>
                            Tipo de Reporte
                        </label>
                        <select class="form-select" id="tipo-reporte" name="tipo" required>
                            <option value="general">Reporte General (Todos los almacenes)</option>
                            <option value="por-lugar">Reporte por Almacén Específico</option>
                            <option value="pasivos">Listado de Pasivos</option>
                        </select>
                    </div>

                    <div class="mb-4" id="selector-lugar" style="display: none;">
                        <label class="form-label fw-bold">
                            <i class="bi bi-geo-alt text-success"></i>
                            Seleccionar Almacén
                        </label>
                        <select class="form-select" id="id-lugar" name="id_lugar">
                            <option value="">Seleccione un almacén...</option>
                            <?php
                            $lugares = \app\models\Lugares::find()->orderBy(['nombre' => SORT_ASC])->all();
                            foreach ($lugares as $lugar) {
                                echo '<option value="' . $lugar->id . '">' . \yii\helpers\Html::encode($lugar->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="border-top: 2px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-info" id="btn-generar-reporte">
                    <i class="bi bi-file-earmark-text"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>