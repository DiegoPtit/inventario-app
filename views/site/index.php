<?php

/** @var yii\web\View $this */
/** @var app\models\Productos[] $productos */
/** @var app\models\Clientes[] $clientes */
/** @var float $cobrosCerradas */
/** @var float $cobrosAbiertas */
/** @var array $cobrosParaMostrar */
/** @var float $valorInventario */
/** @var float $valorRecaudado */
/** @var float $proporcionDeuda */
/** @var float $proporcionRecaudado */
/** @var int $clientesSolventes */
/** @var int $clientesMorosos */
/** @var app\models\HistoricoPreciosDolar $precioOficial */
/** @var app\models\HistoricoPreciosDolar $precioParalelo */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Desglose principal';


// Registrar CSS personalizado para las tarjetas de productos
$this->registerCss('
/* Product Carousel Wrapper */
.product-carousel-wrapper {
    position: relative;
    width: 100%;
}

/* Navigation Buttons (Desktop only) */
.carousel-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.carousel-nav-btn:hover:not(:disabled) {
    background: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.carousel-nav-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.carousel-nav-btn i {
    font-size: 1.2rem;
}

.carousel-prev {
    left: -55px;
}

.carousel-next {
    right: -55px;
}

/* Product Carousel Scroll Container */
.product-carousel-scroll {
    display: flex;
    gap: 15px;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x mandatory;
    padding: 10px 0;
}

/* Hide scrollbar but keep functionality */
.product-carousel-scroll::-webkit-scrollbar {
    display: none;
}

.product-carousel-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 350px;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    scroll-snap-align: start;
    flex-shrink: 0;
    /* Mobile: full width minus gap */
    width: calc(100% - 15px);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.product-card-header {
    height: 200px;
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
}

.product-card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.product-card-body {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px 0;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    min-height: 2.8rem;
}

.product-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-card-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #28a745;
}

.product-card-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
}

.product-card-conversions .conversion-line {
    display: block;
}

.product-card-conversions strong {
    color: #495057;
    font-weight: 600;
}

.product-card-category {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6c757d;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 12px;
}

/* Responsive Styles */
@media (min-width: 768px) {
    .product-carousel-wrapper {
        padding: 0 70px;
    }
    
    .carousel-nav-btn {
        display: flex;
    }
    
    .product-carousel-scroll {
        scroll-snap-type: x mandatory;
        display: flex;
        gap: 20px;
        overflow-x: auto;
        overflow-y: hidden;
    }
    
    .product-card {
        /* Calcular para mostrar exactamente 4 cards:
           100% / 4 = 25% por card
           Restar el espacio de los gaps: (3 gaps * 20px) / 4 = 15px por card
        */
        width: calc(25% - 15px);
        scroll-snap-align: start;
    }
}

/* Estilos para conversiones en otras secciones */
.conversion-subtexts {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
}

.conversion-subtexts .conversion-line {
    display: block;
}

.conversion-subtexts strong {
    color: #495057;
    font-weight: 600;
}

.analytics-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 8px;
    line-height: 1.3;
    text-align: center;
}

.cobro-conversions {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 4px;
    line-height: 1.3;
    text-align: right;
}

.product-card-category {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-products i {
    font-size: 4rem;
    margin-bottom: 20px;
}

.carousel-scroll {
    display: flex;
    gap: 20px;
    padding: 0 20px 20px 20px;
}

.clients-table {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.clients-table table {
    min-width: 600px;
}

.badge-solvente {
    background-color: #28a745;
    color: white;
}

.badge-moroso {
    background-color: #dc3545;
    color: white;
}

.table-responsive-custom {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table-responsive-custom table {
    margin-bottom: 0;
}

.clients-carousel {
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.clients-carousel::-webkit-scrollbar {
    height: 8px;
}

.clients-carousel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.clients-carousel::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.clients-carousel::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.clients-scroll {
    display: flex;
    gap: 20px;
    padding: 0 20px 20px 20px;
}

.client-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
    min-width: 300px;
    max-width: 300px;
    height: 280px;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.client-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.client-card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    padding: 15px;
    color: white;
    text-align: center;
    flex-shrink: 0;
}

.client-card-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.3rem;
}

.client-card-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.client-card-body {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.client-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e9ecef;
}

.client-card-row:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.client-card-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
}

.client-card-value {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 120px;
}

.client-card-status {
    display: flex;
    align-items: center;
    justify-content: center;
}

.analytics-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 30px;
}

.analytics-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.analytics-card-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.analytics-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    text-align: center;
}

.analytics-card-body {
    padding: 25px;
}

.chart-container {
    display: flex;
    justify-content: center;
    margin-bottom: 25px;
    height: 300px;
    position: relative;
}

.chart-container canvas {
    max-width: 100%;
    max-height: 100%;
}

.analytics-summary {
    display: flex;
    justify-content: space-around;
    gap: 20px;
    margin-top: 20px;
}

.summary-item {
    text-align: center;
    flex: 1;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.summary-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
}

.summary-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.summary-indicator-closed {
    background-color: #28a745;
}

.summary-indicator-open {
    background-color: #ffc107;
}

.summary-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #333;
}

.amount-positive {
    font-weight: 600;
    color: #28a745;
}

.amount-pending {
    font-weight: 600;
    color: #ffc107;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.status-closed {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-open {
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

/* Estilos para lista simple de cobros */
.cobros-lista-simple {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.cobro-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
}

.cobro-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.cobro-item.expandido {
    border-color: #007bff;
}

.cobro-resumen {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    gap: 15px;
}

.cobro-info-principal {
    display: flex;
    flex-direction: column;
    gap: 5px;
    flex: 1;
    min-width: 0;
}

.cobro-factura {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.cobro-cliente {
    color: #6c757d;
    font-size: 0.85rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cobro-info-secundaria {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.cobro-monto {
    font-weight: 700;
    color: #28a745;
    font-size: 1rem;
}

.cobro-estado {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.cobro-estado.cerrada {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.cobro-estado.abierta {
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.cobro-toggle-icon {
    color: #6c757d;
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.cobro-detalles {
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
    padding: 15px 20px;
}

.cobro-detalles-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 0.85rem;
    color: #6c757d;
    flex-wrap: wrap;
    gap: 10px;
}

.cobro-detalles-header i {
    margin-right: 5px;
}

.monto-restante {
    color: #dc3545;
    font-weight: 600;
}

.historico-cobros-tabla {
    overflow-x: auto;
}

.historico-cobros-tabla table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 6px;
    overflow: hidden;
    font-size: 0.85rem;
}

.historico-cobros-tabla thead {
    background: #f8f9fa;
}

.historico-cobros-tabla th {
    padding: 10px 12px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.historico-cobros-tabla td {
    padding: 10px 12px;
    border-bottom: 1px solid #f1f1f1;
    color: #333;
}

.historico-cobros-tabla tbody tr:last-child td {
    border-bottom: none;
}

.historico-cobros-tabla tbody tr:hover {
    background: #f8f9fa;
}

.monto-cell {
    font-weight: 600;
    color: #28a745;
}

.conversion-text {
    display: block;
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 2px;
    font-weight: 400;
}

.nota-cell {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #6c757d;
    font-style: italic;
}

@media (max-width: 768px) {
    .cobro-resumen {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .cobro-info-secundaria {
        width: 100%;
        justify-content: space-between;
    }
    
    .cobro-detalles-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .historico-cobros-tabla {
        font-size: 0.75rem;
    }
    
    .historico-cobros-tabla th,
    .historico-cobros-tabla td {
        padding: 8px;
    }
    
    .nota-cell {
        max-width: 100px;
    }
}

/* Botón de ver detalles */
.btn-ver-detalles {
    background: none;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
    color: #007bff;
    font-size: 1rem;
    transition: all 0.2s ease;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-ver-detalles:hover {
    background: rgba(0, 123, 255, 0.1);
    color: #0056b3;
    transform: scale(1.1);
}

.btn-ver-detalles:active {
    transform: scale(0.95);
}

.btn-ver-detalles i {
    pointer-events: none;
}

/* Analytics Card Simple */
.analytics-card-simple {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
}

.analytics-header-simple {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
}

.analytics-title-simple {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.analytics-body-simple {
    padding: 20px;
}

.analytics-layout {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.analytics-chart-wrapper {
    width: 100%;
    max-width: 250px;
    margin: 0 auto;
}

.analytics-chart-wrapper canvas {
    max-height: 250px;
}

.analytics-data {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.data-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #e9ecef;
}

.data-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.data-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.data-indicator.closed {
    background-color: #28a745;
}

.data-indicator.pending {
    background-color: #ffc107;
}

.data-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
}

.data-amount {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.data-amount.success {
    color: #28a745;
}

.data-amount.warning {
    color: #ffc107;
}

.data-conversion {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.4;
}

/* Responsive para tablets */
@media (min-width: 768px) {
    .analytics-layout {
        flex-direction: row;
        align-items: center;
    }
    
    .analytics-chart-wrapper {
        flex: 0 0 200px;
        max-width: 200px;
    }
    
    .analytics-data {
        flex: 1;
    }
}

/* Responsive para móviles */
@media (max-width: 767px) {
    .analytics-card-simple {
        margin-bottom: 20px;
    }
    
    .analytics-header-simple {
        padding: 12px 15px;
    }
    
    .analytics-title-simple {
        font-size: 0.85rem;
    }
    
    .analytics-body-simple {
        padding: 15px;
    }
    
    .analytics-chart-wrapper {
        max-width: 180px;
    }
    
    .analytics-chart-wrapper canvas {
        max-height: 180px;
    }
    
    .analytics-data {
        gap: 12px;
    }
    
    .data-item {
        padding: 12px;
    }
    
    .data-label {
        font-size: 0.75rem;
    }
    
    .data-amount {
        font-size: 1.2rem;
    }
    
    .data-conversion {
        font-size: 0.7rem;
    }
}

/* Financial Values Section */
.financial-values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.financial-value-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.financial-value-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.fv-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.fv-icon {
    font-size: 1.3rem;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.fv-icon.inventory {
    background: #e3f2fd;
    color: #1976d2;
}

.fv-icon.collected {
    background: #e8f5e9;
    color: #388e3c;
}

.fv-icon.differential {
    background: #e0f7fa;
    color: #00796b;
}

.fv-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.fv-amount {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 8px;
    line-height: 1.2;
}

.fv-amount.inventory {
    color: #1976d2;
}

.fv-amount.collected {
    color: #388e3c;
}

.fv-amount.differential {
    color: #00796b;
}

.fv-conversion {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 8px;
}

.fv-description {
    font-size: 0.8rem;
    color: #868e96;
}

/* Chart Cards Simple */
.chart-card-simple {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.chart-card-header {
    background: #f8f9fa;
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
}

.chart-title {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-card-body {
    padding: 20px;
}

.chart-card-body canvas {
    max-height: 300px;
}

/* Proportion Layout */
.proportion-layout {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.proportion-chart {
    width: 100%;
    max-width: 200px;
    margin: 0 auto;
}

.proportion-chart canvas {
    max-height: 200px;
}

.proportion-data {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.proportion-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.proportion-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.proportion-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.proportion-indicator.inventory {
    background-color: #ffc107;
}

.proportion-indicator.collected {
    background-color: #28a745;
}

.proportion-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.proportion-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 5px;
}

.proportion-conversion {
    font-size: 0.7rem;
    color: #6c757d;
}

/* Responsive para tablets */
@media (min-width: 768px) {
    .proportion-layout {
        flex-direction: row;
        align-items: center;
    }
    
    .proportion-chart {
        flex: 0 0 180px;
    }
    
    .proportion-data {
        flex: 1;
    }
}

/* Responsive para móviles */
@media (max-width: 767px) {
    .financial-values-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .financial-value-card {
        padding: 15px;
    }
    
    .fv-icon {
        font-size: 1.1rem;
        width: 32px;
        height: 32px;
    }
    
    .fv-label {
        font-size: 0.85rem;
    }
    
    .fv-amount {
        font-size: 1.5rem;
    }
    
    .fv-conversion {
        font-size: 0.7rem;
    }
    
    .fv-description {
        font-size: 0.75rem;
    }
    
    .chart-card-header {
        padding: 10px 12px;
    }
    
    .chart-title {
        font-size: 0.85rem;
    }
    
    .chart-card-body {
        padding: 15px;
    }
    
    .chart-card-body canvas {
        max-height: 250px;
    }
    
    .proportion-chart {
        max-width: 160px;
    }
    
    .proportion-chart canvas {
        max-height: 160px;
    }
    
    .proportion-item {
        padding: 10px;
    }
    
    .proportion-label {
        font-size: 0.75rem;
    }
    
    .proportion-value {
        font-size: 1.1rem;
    }
    
    .proportion-conversion {
        font-size: 0.65rem;
    }
}
');

?>

<div class="site-index">
    <div class="container-fluid px-3">
        <div class="text-center mb-5">
            <h2 class="text-start">Productos en Inventario</h2>
            <div class="text-start mt-3">
                <?= Html::a('Ver todos <i class="bi bi-arrow-right"></i>', Url::to(['productos/index']), [
                    'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                    'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                ]) ?>
            </div>
        </div>

        <?php if (empty($productos)): ?>
            <div class="no-products">
                <i class="bi bi-box-seam"></i>
                <h3>No hay productos disponibles</h3>
                <p>¡Agrega productos al inventario para mostrarlos aquí!</p>
            </div>
        <?php else: ?>
            <div class="product-carousel-wrapper">
                <!-- Navigation Buttons (Desktop only) -->
                <button class="carousel-nav-btn carousel-prev" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="carousel-nav-btn carousel-next" aria-label="Siguiente">
                    <i class="bi bi-chevron-right"></i>
                </button>
                
                <div class="product-carousel-scroll">
                    <?php foreach ($productos as $producto): ?>
                        <?= Html::beginTag('a', [
                            'href' => Url::to(['productos/view', 'id' => $producto->id]),
                            'class' => 'product-card'
                        ]) ?>
                            <div class="product-card-header">
                                <?php
                                $fotos = null;
                                if (!empty($producto->fotos)) {
                                    $fotosArray = json_decode($producto->fotos, true);
                                    if (is_array($fotosArray) && !empty($fotosArray)) {
                                        $fotos = reset($fotosArray); // Obtener la primera foto
                                    }
                                }
                                ?>
                                
                                <?php if ($fotos): ?>
                                    <?= Html::img(Yii::getAlias('@web') . '/' . $fotos, [
                                        'alt' => Html::encode($producto->marca . ' ' . $producto->modelo),
                                        'class' => 'product-card-image'
                                    ]) ?>
                                <?php else: ?>
                                    <div class="product-card-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-card-body">
                                <?php
                                // Crear título concatenando marca, modelo, color y descripción
                                $titulo_partes = array_filter([
                                    $producto->marca,
                                    $producto->modelo,
                                    $producto->color,
                                    $producto->descripcion
                                ]);
                                $titulo = implode(' - ', $titulo_partes);
                                if (empty($titulo)) {
                                    $titulo = 'Producto #' . $producto->id;
                                }
                                ?>
                                
                                <h3 class="product-card-title" title="<?= Html::encode($titulo) ?>">
                                    <?= Html::encode($titulo) ?>
                                </h3>
                                
                                <div class="product-card-footer">
                                    <div>
                                        <span class="product-card-price">
                                            $<?= number_format($producto->precio_venta, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                                        </span>
                                        
                                        <?php if ($precioParalelo && $precioOficial): ?>
                                            <?php 
                                            $precioVes = $producto->precio_venta * $precioParalelo->precio_ves;
                                            $precioUsdOficial = $precioVes / $precioOficial->precio_ves;
                                            ?>
                                            <div class="product-card-conversions">
                                                <span class="conversion-line">Bs. <?= number_format($precioVes, 2, ',', '.') ?></span>
                                                <span class="conversion-line"><strong>$<?= number_format($precioUsdOficial, 2) ?></strong> (BCV)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($producto->categoria): ?>
                                        <span class="product-card-category">
                                            <?= Html::encode($producto->categoria->titulo) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="product-card-category">
                                            Sin categoría
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?= Html::endTag('a') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <?= Html::a('Agregar producto <i class="bi bi-plus-circle"></i>', ['productos/create'], ['class' => 'btn btn-success w-100']) ?>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Carrusel de Clientes -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">Clientes Registrados</h2>
                    <button class="border-0 bg-transparent p-0 text-primary" style="font-size: 1.5rem; line-height: 1;" type="button" data-bs-toggle="collapse" data-bs-target="#clientesSection" aria-expanded="false" aria-controls="clientesSection" aria-label="Mostrar clientes">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div id="clientesSection" class="collapse">
                    <div class="text-start mt-3">
                        <?= Html::a('Ver todos <i class="bi bi-arrow-right"></i>', Url::to(['clientes/index']), [
                            'class' => 'btn btn-outline-secondary btn-sm fw-bold w-100',
                            'style' => 'background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);'
                        ]) ?>
                    </div>
                    
                    <?php if (empty($clientes)): ?>
                        <div class="no-products mt-4">
                            <i class="bi bi-person-x"></i>
                            <h4>No hay clientes registrados</h4>
                            <p>¡Registra clientes para mostrarlos aquí!</p>
                        </div>
                    <?php else: ?>
                        <div class="clients-carousel mt-4">
                            <div class="clients-scroll">
                                <?php foreach ($clientes as $cliente): ?>
                                    <?= Html::beginTag('a', [
                                        'href' => Url::to(['clientes/view', 'id' => $cliente->id]),
                                        'class' => 'client-card'
                                    ]) ?>
                                        <div class="client-card-header">
                                            <div class="client-card-avatar">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <h4 class="client-card-name" title="<?= Html::encode($cliente->nombre) ?>">
                                                <?= Html::encode($cliente->nombre) ?>
                                            </h4>
                                        </div>
                                        
                                        <div class="client-card-body">
                                            <div class="client-card-row">
                                                <span class="client-card-label">
                                                    <i class="bi bi-card-text me-2"></i>
                                                    Documento:
                                                </span>
                                                <span class="client-card-value" title="<?= Html::encode($cliente->documento_identidad ?: 'N/A') ?>">
                                                    <?= Html::encode($cliente->documento_identidad ?: 'N/A') ?>
                                                </span>
                                            </div>
                                            
                                            <div class="client-card-row">
                                                <span class="client-card-label">
                                                    <i class="bi bi-telephone me-2"></i>
                                                    Teléfono:
                                                </span>
                                                <span class="client-card-value" title="<?= Html::encode($cliente->telefono ?: 'N/A') ?>">
                                                    <?= Html::encode($cliente->telefono ?: 'N/A') ?>
                                                </span>
                                            </div>
                                            
                                            <div class="client-card-row">
                                                <span class="client-card-label">
                                                    <i class="bi bi-shield-check me-2"></i>
                                                    Status:
                                                </span>
                                                <div class="client-card-status">
                                                    <?php if ($cliente->isStatusSolvente()): ?>
                                                        <span class="badge badge-solvente px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Solvente
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-moroso px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            Moroso
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?= Html::endTag('a') ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="<?= Url::to(['clientes/create']) ?>" class="btn btn-success w-100">
                            Añadir más clientes <i class="bi bi-plus-circle"></i>
                        </a>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 offset-md-3">
                            <div class="analytics-card">
                                <div class="analytics-card-header">
                                    <h5 class="analytics-card-title">Estado de Clientes: Morosos vs Solventes</h5>
                                </div>
                                <div class="analytics-card-body">
                                    <div class="chart-container">
                                        <canvas id="clientesStatusChart"></canvas>
                                    </div>
                                    
                                    <div class="analytics-summary">
                                        <div class="summary-item">
                                            <div class="summary-label">
                                                <span class="summary-indicator" style="background-color: #28a745;"></span>
                                                Solventes
                                            </div>
                                            <div class="summary-value">
                                                <?= $clientesSolventes ?>
                                            </div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-label">
                                                <span class="summary-indicator" style="background-color: #dc3545;"></span>
                                                Morosos
                                            </div>
                                            <div class="summary-value">
                                                <?= $clientesMorosos ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Sección de Análisis de Facturas -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Análisis de Cobros de Facturas</h3>
                    <button class="border-0 bg-transparent p-0 text-primary" style="font-size: 1.5rem; line-height: 1;" type="button" data-bs-toggle="collapse" data-bs-target="#facturasSection" aria-expanded="false" aria-controls="facturasSection" aria-label="Mostrar análisis de facturas">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                
                <div id="facturasSection" class="collapse">
                    <!-- Gráfico de Pastel -->
                    <div class="row mb-5">
                        <div class="col-md-6 offset-md-3">
                            <div class="analytics-card-simple">
                                <div class="analytics-header-simple">
                                    <h6 class="analytics-title-simple">
                                        <i class="bi bi-pie-chart"></i>
                                        Estado de Cobros
                                    </h6>
                                </div>
                                <div class="analytics-body-simple">
                                    <div class="analytics-layout">
                                        <!-- Gráfico -->
                                        <div class="analytics-chart-wrapper">
                                            <canvas id="facturasPieChart"></canvas>
                                        </div>
                                        
                                        <!-- Resumen de datos -->
                                        <div class="analytics-data">
                                            <!-- Total Cobrado -->
                                            <div class="data-item">
                                                <div class="data-header">
                                                    <span class="data-indicator closed"></span>
                                                    <span class="data-label">Total Cobrado</span>
                                                </div>
                                                <div class="data-amount success">
                                                    $<?= number_format($cobrosCerradas, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                                                </div>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $cobrosCerradasVes = $cobrosCerradas * $precioParalelo->precio_ves;
                                                    $cobrosCerradasUsdOficial = $cobrosCerradasVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="data-conversion">
                                                        Bs. <?= number_format($cobrosCerradasVes, 2, ',', '.') ?> · 
                                                        $<?= number_format($cobrosCerradasUsdOficial, 2) ?> BCV
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Saldo Pendiente -->
                                            <div class="data-item">
                                                <div class="data-header">
                                                    <span class="data-indicator pending"></span>
                                                    <span class="data-label">Saldo Pendiente</span>
                                                </div>
                                                <div class="data-amount warning">
                                                    $<?= number_format($cobrosAbiertas, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                                                </div>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $cobrosAbiertasVes = $cobrosAbiertas * $precioParalelo->precio_ves;
                                                    $cobrosAbiertasUsdOficial = $cobrosAbiertasVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="data-conversion">
                                                        Bs. <?= number_format($cobrosAbiertasVes, 2, ',', '.') ?> · 
                                                        $<?= number_format($cobrosAbiertasUsdOficial, 2) ?> BCV
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Histórico de Cobros -->
                    <h4 class="mb-4">Histórico de Cobros</h4>
                    
                    <?php if (empty($cobrosParaMostrar)): ?>
                        <div class="no-products">
                            <i class="bi bi-receipt"></i>
                            <h4>No hay registros de cobros</h4>
                            <p>¡Los cobros aparecerán aquí cuando se registren!</p>
                        </div>
                    <?php else: ?>
                        <div class="cobros-lista-simple">
                            <?php foreach ($cobrosParaMostrar as $item): ?>
                                <?php
                                // Extraer datos (ahora la estructura es uniforme)
                                $factura = $item['factura'];
                                $cliente = $item['cliente'];
                                $totalCobrado = $item['totalCobrado'];
                                $cobros = $item['cobros'];
                                $esCerrada = $item['esCerrada'];
                                $montoRestante = $item['montoRestante'];
                                
                                // Calcular conversiones para el total cobrado
                                if ($precioParalelo && $precioOficial) {
                                    $totalVes = $totalCobrado * $precioParalelo->precio_ves;
                                    $totalUsdOficial = $totalVes / $precioOficial->precio_ves;
                                }
                                ?>
                                
                                <!-- Tarjeta Unificada de Factura -->
                                <div class="cobro-item" onclick="toggleCobroDetalle(this)">
                                    <div class="cobro-resumen">
                                        <div class="cobro-info-principal">
                                            <span class="cobro-factura"><?= Html::encode($factura->codigo) ?></span>
                                            <span class="cobro-cliente"><?= Html::encode($cliente ? $cliente->nombre : 'N/A') ?></span>
                                        </div>
                                        <div class="cobro-info-secundaria">
                                            <div>
                                                <?php
                                                // Determinar el símbolo de moneda según el currency de la factura
                                                $currencySymbol = ($factura->currency === 'VES') ? 'Bs. ' : '$';
                                                $currencyLabel = $factura->currency;
                                                ?>
                                                <span class="cobro-monto"><?= $currencySymbol ?><?= number_format($totalCobrado, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(<?= $currencyLabel ?>)</small></span>
                                            </div>
                                            <span class="cobro-estado <?= $esCerrada ? 'cerrada' : 'abierta' ?>">
                                                <i class="bi bi-<?= $esCerrada ? 'check-circle' : 'clock' ?>"></i> 
                                                <?= $esCerrada ? 'Cerrada' : 'Abierta' ?>
                                            </span>
                                            <button class="btn-ver-detalles" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDetallesCobros"
                                                data-factura-id="<?= $factura->id ?>"
                                                data-factura-codigo="<?= Html::encode($factura->codigo) ?>"
                                                data-factura-currency="<?= $factura->currency ?>"
                                                data-cliente-nombre="<?= Html::encode($cliente ? $cliente->nombre : 'N/A') ?>"
                                                data-factura-fecha="<?= date('d/m/Y', strtotime($factura->fecha)) ?>"
                                                data-total-cobrado="<?= number_format($totalCobrado, 2) ?>"
                                                data-cobros='<?= json_encode(array_map(function($c) { return ['id' => $c->id, 'fecha' => $c->fecha, 'monto' => $c->monto, 'currency' => $c->currency, 'metodo_pago' => $c->metodo_pago, 'nota' => $c->nota]; }, $cobros)) ?>'
                                                onclick="event.stopPropagation();"
                                                title="Ver detalles de pagos">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <i class="bi bi-chevron-down cobro-toggle-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="cobro-detalles" style="display: none;">
                                        <div class="cobro-detalles-header">
                                            <span><i class="bi bi-calendar3"></i> Fecha Factura: <?= date('d/m/Y', strtotime($factura->fecha)) ?></span>
                                            <?php if (!$esCerrada && $montoRestante > 0): ?>
                                                <?php
                                                // Determinar el símbolo de moneda según el currency de la factura
                                                $currencySymbol = ($factura->currency === 'VES') ? 'Bs. ' : '$';
                                                $currencyLabel = $factura->currency;
                                                ?>
                                                <span class="monto-restante">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    Restante: <?= $currencySymbol ?><?= number_format($montoRestante, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(<?= $currencyLabel ?>)</small>
                                                </span>
                                            <?php else: ?>
                                                <span><i class="bi bi-collection"></i> Total de pagos: <?= count($cobros) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="historico-cobros-tabla">
                                            <?php if (empty($cobros)): ?>
                                                <div class="alert alert-warning" style="margin: 1rem;">
                                                    <i class="bi bi-info-circle"></i> Esta factura no tiene pagos registrados aún.
                                                </div>
                                            <?php else: ?>
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Monto</th>
                                                            <th>Método de Pago</th>
                                                            <th>Nota</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($cobros as $cobro): ?>
                                                            <tr>
                                                                <td><?= date('d/m/Y', strtotime($cobro->fecha)) ?></td>
                                                                <td class="monto-cell">
                                                                    <?php
                                                                    // Determinar el símbolo de moneda según el currency del cobro
                                                                    $cobroCurrencySymbol = ($cobro->currency === 'VES') ? 'Bs. ' : '$';
                                                                    $cobroCurrencyLabel = $cobro->currency;

                                                                    ?>
                                                                    <strong><?= $cobroCurrencySymbol ?><?= number_format($cobro->monto, 2) ?></strong> <small class="text-muted">(<?= $cobroCurrencyLabel ?>)</small>
                                                                    <?php if ($precioParalelo && $precioOficial): ?>
                                                                        <?php 
                                                                        $cobroVes = $cobro->monto * $precioParalelo->precio_ves;
                                                                        $cobroUsdOficial = $cobroVes / $precioOficial->precio_ves;
                                                                        $cobroUsdParalelo = $cobroVes / $precioParalelo->precio_ves;
                                                                        ?>
                                                                        <?php if ($cobro->currency === 'USDT'): ?>
                                                                            <small class="conversion-text">
                                                                                Bs. <?= number_format($cobroVes, 2, ',', '.') ?> / 
                                                                                $<?= number_format($cobroUsdOficial, 2) ?> BCV
                                                                            </small>
                                                                        <?php endif; ?>

                                                                        <?php if ($cobro->currency === 'BCV'): ?>
                                                                            <small class="conversion-text">
                                                                                Bs. <?= number_format($cobroVes, 2, ',', '.') ?> / 
                                                                                $<?= number_format($cobroUsdParalelo, 2) ?> USDT
                                                                            </small>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= Html::encode($cobro->metodo_pago ?: '-') ?></td>
                                                                <td class="nota-cell"><?= Html::encode($cobro->nota ?: '-') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <script>
                    function toggleCobroDetalle(element) {
                        const detalles = element.querySelector('.cobro-detalles');
                        const icon = element.querySelector('.cobro-toggle-icon');
                        
                        if (detalles.style.display === 'none') {
                            detalles.style.display = 'block';
                            icon.classList.remove('bi-chevron-down');
                            icon.classList.add('bi-chevron-up');
                            element.classList.add('expandido');
                        } else {
                            detalles.style.display = 'none';
                            icon.classList.remove('bi-chevron-up');
                            icon.classList.add('bi-chevron-down');
                            element.classList.remove('expandido');
                        }
                    }
                    </script>
                </div>
            </div>
        </div>
        
        <!-- Separador horizontal -->
        <hr class="my-5" style="border: 2px solid #e9ecef;">
        
        <!-- Sección de Estado Financiero -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Estado Financiero</h3>
                    <button class="border-0 bg-transparent p-0 text-primary" style="font-size: 1.5rem; line-height: 1;" type="button" data-bs-toggle="collapse" data-bs-target="#finanzasSection" aria-expanded="false" aria-controls="finanzasSection" aria-label="Mostrar estado financiero">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                
                <div id="finanzasSection" class="collapse">
                    <!-- Valores financieros principales -->
                    <div class="financial-values-grid mb-4">
                        <!-- Valor de Inventario -->
                        <div class="financial-value-card">
                            <div class="fv-header">
                                <i class="bi bi-box-seam fv-icon inventory"></i>
                                <span class="fv-label">Inventario</span>
                            </div>
                            <div class="fv-amount inventory">
                                $<?= number_format($valorInventario, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                            </div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $valorInventarioVes = $valorInventario * $precioParalelo->precio_ves;
                                $valorInventarioUsdOficial = $valorInventarioVes / $precioOficial->precio_ves;
                                ?>
                                <div class="fv-conversion">
                                    Bs. <?= number_format($valorInventarioVes, 2, ',', '.') ?> · 
                                    $<?= number_format($valorInventarioUsdOficial, 2) ?> BCV
                                </div>
                            <?php endif; ?>
                            <div class="fv-description">Último período cerrado</div>
                        </div>
                        
                        <!-- Valor Recaudado -->
                        <div class="financial-value-card">
                            <div class="fv-header">
                                <i class="bi bi-cash-stack fv-icon collected"></i>
                                <span class="fv-label">Recaudado</span>
                            </div>
                            <div class="fv-amount collected">
                                $<?= number_format($valorRecaudado, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                            </div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $valorRecaudadoVes = $valorRecaudado * $precioParalelo->precio_ves;
                                $valorRecaudadoUsdOficial = $valorRecaudadoVes / $precioOficial->precio_ves;
                                ?>
                                <div class="fv-conversion">
                                    Bs. <?= number_format($valorRecaudadoVes, 2, ',', '.') ?> · 
                                    $<?= number_format($valorRecaudadoUsdOficial, 2) ?> BCV
                                </div>
                            <?php endif; ?>
                            <div class="fv-description">Total cobros realizados</div>
                        </div>
                        
                        <!-- Diferencial (solo si es positivo) -->
                        <?php 
                        $diferencial = $valorRecaudado - $valorInventario;
                        if ($diferencial > 0): 
                        ?>
                        <div class="financial-value-card">
                            <div class="fv-header">
                                <i class="bi bi-graph-up-arrow fv-icon differential"></i>
                                <span class="fv-label">Diferencial</span>
                            </div>
                            <div class="fv-amount differential">
                                $<?= number_format($diferencial, 2) ?> <small class="text-muted" style="font-size: 0.65em;">(USDT)</small>
                            </div>
                            <?php if ($precioParalelo && $precioOficial): ?>
                                <?php 
                                $diferencialVes = $diferencial * $precioParalelo->precio_ves;
                                $diferencialUsdOficial = $diferencialVes / $precioOficial->precio_ves;
                                ?>
                                <div class="fv-conversion">
                                    Bs. <?= number_format($diferencialVes, 2, ',', '.') ?> · 
                                    $<?= number_format($diferencialUsdOficial, 2) ?> BCV
                                </div>
                            <?php endif; ?>
                            <div class="fv-description">Exceso sobre inventario</div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Gráficas en diseño simple -->
                    <div class="row g-3">
                        <!-- Gráfica de Barras -->
                        <div class="col-lg-6">
                            <div class="chart-card-simple">
                                <div class="chart-card-header">
                                    <h6 class="chart-title">
                                        <i class="bi bi-bar-chart"></i>
                                        Comparativa Financiera
                                    </h6>
                                </div>
                                <div class="chart-card-body">
                                    <canvas id="financialBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráfica de Proporción -->
                        <div class="col-lg-6">
                            <div class="chart-card-simple">
                                <div class="chart-card-header">
                                    <h6 class="chart-title">
                                        <i class="bi bi-pie-chart"></i>
                                        Proporción Inventario vs Recaudado
                                    </h6>
                                </div>
                                <div class="chart-card-body">
                                    <div class="proportion-layout">
                                        <div class="proportion-chart">
                                            <canvas id="deudaRecaudadoChart"></canvas>
                                        </div>
                                        <div class="proportion-data">
                                            <div class="proportion-item">
                                                <div class="proportion-header">
                                                    <span class="proportion-indicator inventory"></span>
                                                    <span class="proportion-label">Inventario</span>
                                                </div>
                                                <div class="proportion-value">
                                                    $<?= number_format($proporcionDeuda, 2) ?>
                                                </div>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $proporcionDeudaVes = $proporcionDeuda * $precioParalelo->precio_ves;
                                                    $proporcionDeudaUsdOficial = $proporcionDeudaVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="proportion-conversion">
                                                        Bs. <?= number_format($proporcionDeudaVes, 2, ',', '.') ?> · 
                                                        $<?= number_format($proporcionDeudaUsdOficial, 2) ?> BCV
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="proportion-item">
                                                <div class="proportion-header">
                                                    <span class="proportion-indicator collected"></span>
                                                    <span class="proportion-label">Recaudado</span>
                                                </div>
                                                <div class="proportion-value">
                                                    $<?= number_format($proporcionRecaudado, 2) ?>
                                                </div>
                                                <?php if ($precioParalelo && $precioOficial): ?>
                                                    <?php 
                                                    $proporcionRecaudadoVes = $proporcionRecaudado * $precioParalelo->precio_ves;
                                                    $proporcionRecaudadoUsdOficial = $proporcionRecaudadoVes / $precioOficial->precio_ves;
                                                    ?>
                                                    <div class="proportion-conversion">
                                                        Bs. <?= number_format($proporcionRecaudadoVes, 2, ',', '.') ?> · 
                                                        $<?= number_format($proporcionRecaudadoUsdOficial, 2) ?> BCV
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Registrar Chart.js desde CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js', [
    'position' => \yii\web\View::POS_HEAD
]);

// Datos para el gráfico
$totalCobros = $cobrosCerradas + $cobrosAbiertas;

// Preparar datos seguros para JavaScript
$cobrosCerradasJs = (float)$cobrosCerradas;
$cobrosAbiertasJs = (float)$cobrosAbiertas;
$totalCobrosJs = (float)$totalCobros;

$js = "
(function() {
    // Esperar a que Chart.js se cargue
    function initChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initChart, 100);
            return;
        }
        
        const ctx = document.getElementById('facturasPieChart');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }
        
        const chartData = {
            labels: ['Total Cobrado (Cerradas)', 'Saldo Pendiente (Abiertas)'],
            datasets: [{
                data: [" . $cobrosCerradasJs . ", " . $cobrosAbiertasJs . "],
                backgroundColor: [
                    '#28a745',
                    '#ffc107'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalCobrosJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': $' + value.toLocaleString('es-ES', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
})();
";

$this->registerJs($js, \yii\web\View::POS_END);

// JavaScript para el gráfico de Clientes (Morosos vs Solventes)
$clientesSolventesJs = (int)$clientesSolventes;
$clientesMorososJs = (int)$clientesMorosos;
$totalClientesJs = $clientesSolventesJs + $clientesMorososJs;

$jsClientes = "
(function() {
    function initClientesChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initClientesChart, 100);
            return;
        }
        
        const ctx = document.getElementById('clientesStatusChart');
        if (!ctx) {
            console.error('Canvas element clientesStatusChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Solventes', 'Morosos'],
            datasets: [{
                data: [" . $clientesSolventesJs . ", " . $clientesMorososJs . "],
                backgroundColor: [
                    '#28a745',
                    '#dc3545'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalClientesJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': ' + value + ' clientes (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating clientesStatusChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientesChart);
    } else {
        initClientesChart();
    }
})();
";

$this->registerJs($jsClientes, \yii\web\View::POS_END);

// JavaScript para gráfico de barras (Valor Inventario vs Valor Recaudado)
$valorInventarioJs = (float)$valorInventario;
$valorRecaudadoJs = (float)$valorRecaudado;

$jsBarChart = "
(function() {
    function initBarChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initBarChart, 100);
            return;
        }
        
        const ctx = document.getElementById('financialBarChart');
        if (!ctx) {
            console.error('Canvas element financialBarChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Valor de Inventario', 'Valor Recaudado'],
            datasets: [{
                label: 'Monto en \$',
                data: [" . $valorInventarioJs . ", " . $valorRecaudadoJs . "],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(40, 167, 69, 0.7)'
                ],
                borderColor: [
                    '#007bff',
                    '#28a745'
                ],
                borderWidth: 2
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            return '\$' + value.toLocaleString('es-ES', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '\$' + value.toLocaleString('es-ES');
                        }
                    }
                }
            },
            animation: {
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating financialBarChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBarChart);
    } else {
        initBarChart();
    }
})();
";

$this->registerJs($jsBarChart, \yii\web\View::POS_END);

// JavaScript para gráfico de tarta (Deuda vs Recaudado)
$proporcionDeudaJs = (float)$proporcionDeuda;
$proporcionRecaudadoJs = (float)$proporcionRecaudado;
$totalProporcionJs = $proporcionDeudaJs + $proporcionRecaudadoJs;

$jsPieChart = "
(function() {
    function initDeudaPieChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initDeudaPieChart, 100);
            return;
        }
        
        const ctx = document.getElementById('deudaRecaudadoChart');
        if (!ctx) {
            console.error('Canvas element deudaRecaudadoChart not found');
            return;
        }
        
        const chartData = {
            labels: ['Inventario (Último Período)', 'Recaudado'],
            datasets: [{
                data: [" . $proporcionDeudaJs . ", " . $proporcionRecaudadoJs . "],
                backgroundColor: [
                    '#ffc107',
                    '#28a745'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = " . $totalProporcionJs . ";
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': \$' + value.toLocaleString('es-ES', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        };
        
        try {
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating deudaRecaudadoChart:', error);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeudaPieChart);
    } else {
        initDeudaPieChart();
    }
})();
";

$this->registerJs($jsPieChart, \yii\web\View::POS_END);

// JavaScript para navegación del carrusel de productos
$jsCarousel = "
(function() {
    const wrapper = document.querySelector('.product-carousel-wrapper');
    if (!wrapper) return;
    
    const scroll = wrapper.querySelector('.product-carousel-scroll');
    const prevBtn = wrapper.querySelector('.carousel-prev');
    const nextBtn = wrapper.querySelector('.carousel-next');
    
    if (!scroll || !prevBtn || !nextBtn) return;
    
    // Solo habilitar navegación en desktop (768px+)
    function updateNavigation() {
        if (window.innerWidth >= 768) {
            const cards = scroll.querySelectorAll('.product-card');
            const cardWidth = cards[0]?.offsetWidth || 0;
            const gap = 20; // Gap entre cards
            const scrollPerPage = (cardWidth * 4) + (gap * 4); // 4 cards + gaps
            
            // Update button states
            function updateButtonStates() {
                const atStart = scroll.scrollLeft <= 0;
                const atEnd = scroll.scrollLeft + scroll.clientWidth >= scroll.scrollWidth - 1;
                
                prevBtn.disabled = atStart;
                nextBtn.disabled = atEnd;
            }
            
            // Button click handlers
            prevBtn.onclick = function() {
                scroll.scrollBy({
                    left: -scrollPerPage,
                    behavior: 'smooth'
                });
            };
            
            nextBtn.onclick = function() {
                scroll.scrollBy({
                    left: scrollPerPage,
                    behavior: 'smooth'
                });
            };
            
            // Listen for scroll events to update button states
            scroll.addEventListener('scroll', updateButtonStates);
            
            // Initial setup
            updateButtonStates();
        }
    }
    
    // Initialize on load
    updateNavigation();
    
    // Update on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateNavigation, 250);
    });
})();
";

$this->registerJs($jsCarousel, \yii\web\View::POS_END);

// JavaScript para expandir/contraer detalles de cobros agrupados
$jsDetalle = "
function toggleDetalle(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        button.classList.add('active');
        button.innerHTML = '<i class=\"bi bi-chevron-up\"></i> Ocultar detalle de pagos';
    } else {
        content.style.display = 'none';
        button.classList.remove('active');
        button.innerHTML = '<i class=\"bi bi-chevron-down\"></i> Ver detalle de pagos';
    }
}
";

$this->registerJs($jsDetalle, \yii\web\View::POS_END);
?>
