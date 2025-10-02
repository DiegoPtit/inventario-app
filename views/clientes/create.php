<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$this->title = 'Registrar Nuevo Cliente';

$this->registerCss('
.clientes-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.clientes-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.clientes-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.clientes-create-header i {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 15px;
}
');
?>
<div class="clientes-create">
    
    <div class="clientes-create-header">
        <i class="bi bi-person-plus-fill"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar un nuevo cliente</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
