<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HistoricoCobros $model */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

$this->title = Yii::t('app', 'Update Historico Cobros: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Historico Cobros'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="historico-cobros-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'precioOficial' => $precioOficial,
        'precioParalelo' => $precioParalelo,
    ]) ?>

</div>
