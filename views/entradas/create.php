<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Entradas $model */

$this->title = 'Registrar Nueva Entrada de Inventario';

$this->registerCss('
.entradas-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.entradas-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.entradas-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.entradas-create-header i {
    font-size: 3rem;
    color: #17a2b8;
    margin-bottom: 15px;
}
');
?>
<div class="entradas-create">
    
    <div class="entradas-create-header">
        <i class="bi bi-box-arrow-in-down"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar una nueva entrada al inventario</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
