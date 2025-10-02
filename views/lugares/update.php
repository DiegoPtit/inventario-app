<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lugares $model */

$this->title = 'Actualizar Lugar: ' . $model->nombre;

$this->registerCss('
.lugares-update-header {
    text-align: center;
    margin-bottom: 40px;
}

.lugares-update-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.lugares-update-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.lugares-update-header i {
    font-size: 3rem;
    color: #28a745;
    margin-bottom: 15px;
}
');
?>
<div class="lugares-update">
    
    <div class="lugares-update-header">
        <i class="bi bi-pencil-square"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Modifica la información del lugar</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
