<?php
use yii\helpers\Html;
use yii\helpers\Url;
/** @var yii\web\View $this */
/** @var app\models\Proveedores $model */
$this->title = $model->razon_social;
\yii\web\YiiAsset::register($this);
$entradas = $model->getEntradas()->with(['producto', 'lugar'])->orderBy(['created_at' => SORT_DESC])->all();
?>
<style>
.proveedor-view-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.proveedor-header { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
.header-placeholder { width: 100%; height: 300px; display: flex; align-items: center; justify-content: center; background: #e9ecef; }
.header-placeholder i { font-size: 8rem; color: #adb5bd; }
.proveedor-body { margin-bottom: 30px; }
.especificaciones-titulo { font-size: 1.5rem; font-weight: 600; margin-bottom: 20px; color: #2c3e50; }
.especificaciones-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
.especificacion-card { background: #ffffff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 15px; transition: transform 0.2s, box-shadow 0.2s; }
.especificacion-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.especificacion-icon { width: 50px; height: 50px; border-radius: 50%; background: #546e7a; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.especificacion-icon i { font-size: 1.5rem; color: #ffffff; }
.especificacion-content { flex: 1; min-width: 0; }
.especificacion-label { font-size: 0.85rem; color: #6c757d; font-weight: 500; text-transform: uppercase; margin-bottom: 4px; }
.especificacion-value { font-size: 1.1rem; color: #2c3e50; font-weight: 600; word-wrap: break-word; }
.entradas-section { background: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
.entradas-titulo { font-size: 1.4rem; font-weight: 600; margin-bottom: 20px; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
.entradas-titulo i { color: #546e7a; }
.entradas-lista { list-style: none; padding: 0; margin: 0; }
.entrada-item { padding: 15px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; }
.entrada-item:last-child { border-bottom: none; }
.entrada-item:hover { background: #f8f9fa; }
.entrada-info { flex: 1; }
.entrada-nombre { font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
.entrada-detalles { font-size: 0.9rem; color: #6c757d; }
.entrada-cantidad { background: #546e7a; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
.no-entradas { text-align: center; padding: 40px; color: #6c757d; }
.no-entradas i { font-size: 3rem; margin-bottom: 15px; color: #adb5bd; }
.proveedor-footer { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; }
.btn-action { padding: 12px 30px; font-size: 1rem; font-weight: 600; border: 2px solid; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
.btn-editar { color: #0d6efd; border-color: #0d6efd; background: transparent; }
.btn-editar:hover { background: #0d6efd; color: white; }
.btn-borrar { color: #dc3545; border-color: #dc3545; background: transparent; }
.btn-borrar:hover { background: #dc3545; color: white; }
@media (max-width: 768px) { .especificaciones-grid { grid-template-columns: 1fr; } .proveedor-footer { flex-direction: column; } .btn-action { width: 100%; justify-content: center; } .entrada-item { flex-direction: column; align-items: flex-start; gap: 12px; padding: 20px 15px; } .entrada-cantidad { align-self: flex-end; } }
</style>
<div class="proveedor-view-container">
<h2><?= Html::encode($this->title) ?></h2>
<div class="text-start mb-3"><?= Html::a('<i class="bi bi-arrow-left"></i> Ver todos', Url::to(['proveedores/index']), ['class' => 'btn btn-outline-secondary btn-sm fw-bold w-100', 'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);']) ?></div>
<div class="proveedor-header"><div class="header-placeholder"><i class="bi bi-truck"></i></div></div>
<div class="proveedor-body">
<h2 class="especificaciones-titulo">Información del Proveedor</h2>
<div class="especificaciones-grid">
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-building"></i></div><div class="especificacion-content"><div class="especificacion-label">Razón Social</div><div class="especificacion-value"><?= Html::encode($model->razon_social) ?></div></div></div>
<?php if (!empty($model->documento_identificacion)): ?>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-card-text"></i></div><div class="especificacion-content"><div class="especificacion-label">Documento</div><div class="especificacion-value"><?= Html::encode($model->documento_identificacion) ?></div></div></div>
<?php endif; ?>
<?php if (!empty($model->ciudad)): ?>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-geo-alt-fill"></i></div><div class="especificacion-content"><div class="especificacion-label">Ciudad</div><div class="especificacion-value"><?= Html::encode($model->ciudad) ?></div></div></div>
<?php endif; ?>
<?php if (!empty($model->pais)): ?>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-flag-fill"></i></div><div class="especificacion-content"><div class="especificacion-label">País</div><div class="especificacion-value"><?= Html::encode($model->pais) ?></div></div></div>
<?php endif; ?>
<?php if (!empty($model->telefono)): ?>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-telephone-fill"></i></div><div class="especificacion-content"><div class="especificacion-label">Teléfono</div><div class="especificacion-value"><?= Html::encode($model->telefono) ?></div></div></div>
<?php endif; ?>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-calendar-plus"></i></div><div class="especificacion-content"><div class="especificacion-label">Fecha de Registro</div><div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></div></div></div>
<div class="especificacion-card"><div class="especificacion-icon"><i class="bi bi-calendar-check"></i></div><div class="especificacion-content"><div class="especificacion-label">Última Actualización</div><div class="especificacion-value"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></div></div></div>
</div>
<div class="entradas-section">
<h3 class="entradas-titulo"><i class="bi bi-box-arrow-in-down"></i> Entradas de este proveedor</h3>
<?php if (!empty($entradas)): ?>
<ul class="entradas-lista">
<?php foreach ($entradas as $entrada): ?>
<?php
$nombreProducto = '';
if ($entrada->producto) {
$partes = array_filter([$entrada->producto->marca, $entrada->producto->modelo, $entrada->producto->descripcion]);
$nombreProducto = implode(' - ', $partes);
if (empty($nombreProducto)) $nombreProducto = 'Producto #' . $entrada->producto->id;
} else {
$nombreProducto = 'Producto no disponible';
}
?>
<li class="entrada-item">
<div class="entrada-info">
<div class="entrada-nombre"><i class="bi bi-box"></i> <?= Html::encode($nombreProducto) ?></div>
<div class="entrada-detalles">
<?php if ($entrada->lugar): ?>Lugar: <?= Html::encode($entrada->lugar->nombre) ?> | <?php endif; ?>
Fecha: <?= Yii::$app->formatter->asDate($entrada->created_at, 'dd/MM/yyyy') ?>
</div>
</div>
<div class="entrada-cantidad"><?= $entrada->cantidad ?> unidad<?= $entrada->cantidad != 1 ? 'es' : '' ?></div>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<div class="no-entradas"><i class="bi bi-inbox"></i><p>No hay entradas registradas de este proveedor.</p></div>
<?php endif; ?>
</div>
</div>
<div class="proveedor-footer">
<a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-action btn-editar"><i class="bi bi-pencil-square"></i> Editar</a>
<?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display: inline;']) ?>
<button type="submit" class="btn btn-action btn-borrar" onclick="return confirm('¿Está seguro de que desea eliminar este proveedor?');"><i class="bi bi-trash"></i> Borrar</button>
<?= Html::endForm() ?>
</div>
</div>

