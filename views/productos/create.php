<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Productos $model */

$this->title = 'Crear Nuevo Producto';

$this->registerCss('
.productos-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.productos-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.productos-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.productos-create-header i {
    font-size: 3rem;
    color: #28a745;
    margin-bottom: 15px;
}
');
?>
<div class="productos-create">
    
    <div class="productos-create-header">
        <i class="bi bi-box-seam"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para agregar un nuevo producto al inventario</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
