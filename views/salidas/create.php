<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Salidas $model */

$this->title = 'Registrar Nueva Salida de Inventario';

$this->registerCss('
.salidas-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.salidas-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.salidas-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.salidas-create-header i {
    font-size: 3rem;
    color: #dc3545;
    margin-bottom: 15px;
}
');
?>
<div class="salidas-create">
    
    <div class="salidas-create-header">
        <i class="bi bi-box-arrow-up"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar una nueva salida del inventario</p>
    </div>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $messages): ?>
        <?php foreach ((array) $messages as $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
