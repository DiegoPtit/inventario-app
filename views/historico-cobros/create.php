<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HistoricoCobros $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = 'Registrar Cobro al Cliente';

$this->registerCss('
.historico-cobros-create-header {
    text-align: center;
    margin-bottom: 40px;
}

.historico-cobros-create-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.historico-cobros-create-header p {
    font-size: 1.1rem;
    color: #6c757d;
}

.historico-cobros-create-header i {
    font-size: 3rem;
    color: #28a745;
    margin-bottom: 15px;
}
');
?>
<div class="historico-cobros-create">
    
    <div class="historico-cobros-create-header">
        <i class="bi bi-cash-coin"></i>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Completa el formulario para registrar un nuevo cobro realizado</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'precioOficial' => $precioOficial,
        'precioParalelo' => $precioParalelo,
    ]) ?>

</div>
