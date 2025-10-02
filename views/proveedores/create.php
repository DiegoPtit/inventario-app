<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Proveedores $model */

$this->title = 'Registrar Nuevo Proveedor';

$this->registerCss('
.proveedores-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.proveedores-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.proveedores-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.proveedores-create-header i {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 15px;
}
');
?>
<div class="proveedores-create">
    
    <div class="proveedores-create-header">
        <i class="bi bi-truck"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar un nuevo proveedor</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
