<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lugares $model */

$this->title = 'Registrar Nuevo Lugar';

$this->registerCss('
.lugares-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.lugares-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.lugares-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.lugares-create-header i {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 15px;
}
');
?>
<div class="lugares-create">
    
    <div class="lugares-create-header">
        <i class="bi bi-geo-alt-fill"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar un nuevo lugar</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
