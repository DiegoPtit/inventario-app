<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Menú Principal';

// Registrar CSS personalizado para las tarjetas de menú
$this->registerCss('
.menu-container {
    padding: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.menu-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    padding: 15px 20px;
    min-height: 80px;
    border-left: 4px solid transparent;
}

.menu-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa;
}

.menu-card-icon {
    font-size: 1.8rem;
    margin-right: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background-color: rgba(0,0,0,0.03);
    transition: all 0.2s ease;
}

.menu-card:hover .menu-card-icon {
    background-color: rgba(0,0,0,0.08);
    transform: scale(1.05);
}

.menu-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
    text-align: left;
    line-height: 1.2;
}

/* Colores específicos para cada tarjeta */

.menu-card-historico-inventarios {
    border-left-color: #6c757d;
}
.menu-card-historico-inventarios .menu-card-icon {
    color: #6c757d;
}

.menu-card-entradas {
    border-left-color: #28a745;
}
.menu-card-entradas .menu-card-icon {
    color: #28a745;
}

.menu-card-salidas {
    border-left-color: #dc3545;
}
.menu-card-salidas .menu-card-icon {
    color: #dc3545;
}

.menu-card-clientes {
    border-left-color: #007bff;
}
.menu-card-clientes .menu-card-icon {
    color: #007bff;
}

.menu-card-facturas {
    border-left-color: #fd7e14;
}
.menu-card-facturas .menu-card-icon {
    color: #fd7e14;
}

.menu-card-lugares {
    border-left-color: #6f42c1;
}
.menu-card-lugares .menu-card-icon {
    color: #6f42c1;
}

.menu-card-proveedores {
    border-left-color: #17a2b8;
}
.menu-card-proveedores .menu-card-icon {
    color: #17a2b8;
}

.page-header {
    text-align: left;
    margin-bottom: 25px;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 15px;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #343a40;
    margin-bottom: 5px;
}

.page-header p {
    font-size: 1rem;
    color: #6c757d;
    margin: 0;
}
');

?>

<div class="menu-container">
    <div class="page-header">
        <h1>Menú Principal</h1>
        <p>Acceso rápido a los módulos del sistema</p>
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
            <h3 class="menu-card-title">Punto de Venta (POS)</h3>
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
