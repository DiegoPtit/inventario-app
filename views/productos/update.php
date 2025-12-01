<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Productos $model */

$productName = trim(implode(' ', array_filter([
    $model->marca,
    $model->modelo,
    $model->color
]))) ?: 'Producto #' . $model->id;

$this->title = 'Actualizar: ' . $productName;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $productName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$this->registerCss('
.productos-update-header {
    text-align: center;
    margin-bottom: 40px;
}

.productos-update-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.productos-update-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.productos-update-header i {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 15px;
}
');
?>
<div class="productos-update">

    <div class="productos-update-header">
        <i class="bi bi-pencil-square"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Modifica los datos del producto</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
