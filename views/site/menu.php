<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Menú Principal';

// Registrar CSS personalizado para las tarjetas de menú
$this->registerCss('
.menu-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .menu-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

.menu-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    min-height: 200px;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}

.menu-card-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    transition: transform 0.3s ease;
}

.menu-card:hover .menu-card-icon {
    transform: scale(1.1);
}

.menu-card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    text-align: center;
}

/* Colores específicos para cada tarjeta */

.menu-card-entradas {
    background: linear-gradient(135deg, #e8f5e9 0%, #ffffff 100%);
    border-left: 5px solid #28a745;
}

.menu-card-entradas .menu-card-icon {
    color: #28a745;
}

.menu-card-salidas {
    background: linear-gradient(135deg, #ffebee 0%, #ffffff 100%);
    border-left: 5px solid #dc3545;
}

.menu-card-salidas .menu-card-icon {
    color: #dc3545;
}

.menu-card-clientes {
    background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
    border-left: 5px solid #007bff;
}

.menu-card-clientes .menu-card-icon {
    color: #007bff;
}

.menu-card-facturas {
    background: linear-gradient(135deg, #fff3e0 0%, #ffffff 100%);
    border-left: 5px solid #fd7e14;
}

.menu-card-facturas .menu-card-icon {
    color: #fd7e14;
}

.menu-card-lugares {
    background: linear-gradient(135deg, #f3e5f5 0%, #ffffff 100%);
    border-left: 5px solid #6f42c1;
}

.menu-card-lugares .menu-card-icon {
    color: #6f42c1;
}

.menu-card-proveedores {
    background: linear-gradient(135deg, #e0f7fa 0%, #ffffff 100%);
    border-left: 5px solid #17a2b8;
}

.menu-card-proveedores .menu-card-icon {
    color: #17a2b8;
}

.page-header {
    text-align: center;
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.page-header p {
    font-size: 1.1rem;
    color: #6c757d;
}
');

?>

<div class="menu-container">
    <div class="page-header">
        <h1>Menú Principal</h1>
        <p>Selecciona una opción para continuar</p>
    </div>

    <div class="menu-grid">

        <!-- Tarjeta Historico Inventarios -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-archive"></i>
            </div>
            <h3 class="menu-card-title">Inventarios</h3>
        ', Url::to(['historico-inventarios/index']), [
            'class' => 'menu-card menu-card-historico-inventarios'
        ]) ?>

        <!-- Tarjeta Entradas -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <h3 class="menu-card-title">Entradas</h3>
        ', Url::to(['entradas/index']), [
            'class' => 'menu-card menu-card-entradas'
        ]) ?>

        <!-- Tarjeta Salidas -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-box-arrow-up"></i>
            </div>
            <h3 class="menu-card-title">Salidas</h3>
        ', Url::to(['salidas/index']), [
            'class' => 'menu-card menu-card-salidas'
        ]) ?>

        <!-- Tarjeta Clientes -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <h3 class="menu-card-title">Clientes</h3>
        ', Url::to(['clientes/index']), [
            'class' => 'menu-card menu-card-clientes'
        ]) ?>

        <!-- Tarjeta Facturas -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <h3 class="menu-card-title">PUNTO DE VENTA (POS)</h3>
        ', Url::to(['pos/index']), [
            'class' => 'menu-card menu-card-facturas'
        ]) ?>

        <!-- Tarjeta Lugares -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h3 class="menu-card-title">Lugares</h3>
        ', Url::to(['lugares/index']), [
            'class' => 'menu-card menu-card-lugares'
        ]) ?>

        <!-- Tarjeta Proveedores -->
        <?= Html::a('
            <div class="menu-card-icon">
                <i class="bi bi-truck"></i>
            </div>
            <h3 class="menu-card-title">Proveedores</h3>
        ', Url::to(['proveedores/index']), [
            'class' => 'menu-card menu-card-proveedores'
        ]) ?>
    </div>
</div>
