<?php

use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Tasas de Cambio';

// Registrar CSS personalizado para las tarjetas de tasas
$this->registerCss('
.dollar-statics-container {
    min-height: 100vh;
    padding: 20px;
}

.rates-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
    padding-top: 40px;
}

/* Desktop: 2 columnas centradas */
@media (min-width: 768px) {
    .rates-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
}

.rate-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.rate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.rate-card-header {
    padding: 20px;
    text-align: center;
    font-size: 1.1rem;
    font-weight: 700;
    border-bottom: 1px solid #f1f1f1;
}

.rate-card-header.oficial {
    color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
}

.rate-card-header.paralelo {
    color: #007bff;
    background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
}

.rate-card-body {
    padding: 40px 20px;
    text-align: center;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.rate-price {
    font-size: 3rem;
    font-weight: 700;
    position: relative;
    overflow: hidden;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rate-price-prefix {
    margin-right: 5px;
}

.rate-price-value {
    display: inline-block;
    position: relative;
}

.rate-price.oficial {
    color: #28a745;
}

.rate-price.paralelo {
    color: #007bff;
}

/* Animación de slot machine */
.digit-container {
    display: inline-block;
    position: relative;
    width: 1.2ch;
    height: 1.2em;
    vertical-align: middle;
    overflow: hidden;
}

.digit-reel {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.digit-reel.spinning {
    animation: spin 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

@keyframes spin {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-200%);
    }
    100% {
        transform: translateY(0);
    }
}

.rate-loading {
    color: #6c757d;
    font-size: 1rem;
    font-weight: 400;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.rate-loading i {
    animation: spin-loading 1s linear infinite;
}

@keyframes spin-loading {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.rate-meta {
    margin-top: 20px;
    font-size: 0.85rem;
    color: #6c757d;
}

.rate-footer {
    padding: 15px 20px;
    border-top: 1px solid #f1f1f1;
    background: #f8f9fa;
    text-align: center;
    font-size: 0.75rem;
    color: #6c757d;
}

.rate-footer .diff-positive {
    color: #28a745;
    font-weight: 600;
}

.rate-footer .diff-negative {
    color: #dc3545;
    font-weight: 600;
}

.rate-footer .diff-neutral {
    color: #6c757d;
    font-weight: 600;
}

.back-button {
    margin-bottom: 30px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

/* Coma y punto decimal */
.decimal-separator {
    display: inline-block;
    width: 0.5ch;
}

.thousands-separator {
    display: inline-block;
    width: 0.3ch;
}

/* Chart section */
.chart-section {
    max-width: 1200px;
    margin: 40px auto 0;
    padding: 0 15px;
}

.chart-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    padding: 25px;
    margin-top: 20px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.chart-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.chart-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 6px 14px;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

.filter-btn.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.chart-canvas-wrapper {
    position: relative;
    height: 400px;
}

@media (max-width: 768px) {
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .chart-filters {
        width: 100%;
    }
    
    .filter-btn {
        flex: 1;
        text-align: center;
    }
}

/* Ajustes generales para móviles (pantallas pequeñas) */
@media (max-width: 576px) {
    .dollar-statics-container {
        padding: 12px 8px;
    }

    .back-button {
        margin-bottom: 20px;
    }

    .rate-card-body {
        padding: 24px 14px;
    }

    .rate-price {
        font-size: 2.2rem;
        min-height: 60px;
    }

    .chart-container {
        padding: 16px 12px;
    }

    .chart-title {
        font-size: 1.05rem;
    }

    .chart-canvas-wrapper {
        height: 260px;
    }

    .filter-btn {
        font-size: 0.78rem;
        padding: 5px 8px;
    }

    .differential-container {
        padding: 18px 14px;
    }

    .differential-value {
        font-size: 1.6rem;
    }

    .differential-value small {
        font-size: 0.9rem;
    }
}

/* Differential section */
.differential-section {
    max-width: 1200px;
    margin: 30px auto 0;
    padding: 0 15px;
}

.differential-container {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    border: 1px solid #e3e6ea;
    padding: 24px 20px;
    color: #212529;
}

.differential-title {
    font-size: 1.25rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 20px;
    color: #343a40;
}

.differential-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 768px) {
    .differential-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.differential-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 18px 16px;
    text-align: center;
    border: 1px solid #e3e6ea;
}

.differential-label {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.differential-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
}

.differential-value small {
    font-size: 1rem;
    opacity: 0.7;
    margin-left: 5px;
}

/* Calculator section */
.calculator-section {
    max-width: 1200px;
    margin: 30px auto 0;
    padding: 0 15px;
}

.calculator-container {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    border: 1px solid #e3e6ea;
    padding: 20px 18px;
}

.calculator-title {
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 18px;
    color: #343a40;
}

.calculator-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.6fr 1.2fr;
    gap: 12px;
    align-items: center;
}

.calculator-field-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 4px;
}

.calculator-field {
    display: flex;
    flex-direction: column;
}

.calculator-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.calculator-actions .btn {
    min-width: 120px;
}

@media (max-width: 768px) {
    .calculator-grid {
        grid-template-columns: 1fr;
    }

    .calculator-actions {
        justify-content: center;
    }

    .calculator-actions .btn {
        flex: 1;
    }
}
');
?>

<div class="dollar-statics-container">
    <div class="container-fluid px-3">
        <div class="back-button">
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary btn-sm fw-bold w-100" style="background-color: #f8f9fa; border-color: #dee2e6; color: #495057; border-radius: 2rem; padding-left: 1.5rem; padding-right: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <i class="bi bi-arrow-left"></i> Volver al inicio
            </a>
        </div>

        <div class="rates-grid">
            <!-- Tarjeta TASA BCV -->
            <div class="rate-card">
                <div class="rate-card-header oficial">
                    TASA BCV
                </div>
                <div class="rate-card-body">
                    <div class="rate-price oficial" id="rate-oficial">
                        <span class="rate-loading">
                            <i class="bi bi-arrow-repeat"></i> Cargando...
                        </span>
                    </div>
                    <div class="rate-meta" id="meta-oficial">
                        Última actualización: --
                    </div>
                </div>
                <div class="rate-footer" id="footer-oficial" style="display: none;">
                    <!-- Se llenará dinámicamente con comparación del día anterior -->
                </div>
            </div>

            <!-- Tarjeta TASA BINANCE/PARALELO -->
            <div class="rate-card">
                <div class="rate-card-header paralelo">
                    TASA BINANCE/PARALELO
                </div>
                <div class="rate-card-body">
                    <div class="rate-price paralelo" id="rate-paralelo">
                        <span class="rate-loading">
                            <i class="bi bi-arrow-repeat"></i> Cargando...
                        </span>
                    </div>
                    <div class="rate-meta" id="meta-paralelo">
                        Última actualización: --
                    </div>
                </div>
                <div class="rate-footer" id="footer-paralelo" style="display: none;">
                    <!-- Se llenará dinámicamente con comparación del día anterior -->
                </div>
            </div>
        </div>

        <!-- Differential Section -->
        <div class="differential-section" id="differential-section" style="display: none;">
            <div class="differential-container">
                <h3 class="differential-title">Diferencial Cambiario</h3>
                <div class="differential-grid">
                    <div class="differential-item">
                        <div class="differential-label">Diferencia en Bs.</div>
                        <div class="differential-value" id="diff-bs">--</div>
                    </div>
                    <div class="differential-item">
                        <div class="differential-label">Diferencia Valor USD</div>
                        <div class="differential-value" id="diff-usd">--</div>
                    </div>
                    <div class="differential-item">
                        <div class="differential-label">Diferencial %</div>
                        <div class="differential-value" id="diff-percent">--</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Calculator -->
        <div class="calculator-section">
            <div class="calculator-container">
                <h3 class="calculator-title">Calculadora Rápida</h3>
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="calc-currency" class="form-label mb-1">Moneda</label>
                        <select id="calc-currency" class="form-select">
                            <option value="USDT">USDT (Paralelo/Binance)</option>
                            <option value="BCV">BCV (Tasa Oficial)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="calculator-field">
                            <label for="calc-ves" class="calculator-field-label">Monto en VES</label>
                            <input type="number" step="0.01" min="0" id="calc-ves" class="form-control" placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="calculator-field">
                            <label for="calc-foreign" class="calculator-field-label">Monto en USDT/BCV</label>
                            <input type="number" step="0.0001" min="0" id="calc-foreign" class="form-control" placeholder="0,0000">
                        </div>
                    </div>
                </div>
                <div class="calculator-actions">
                    <button type="button" id="calc-swap" class="btn btn-outline-secondary btn-sm">
                        Alternar Montos
                    </button>
                    <button type="button" id="calc-clear" class="btn btn-outline-danger btn-sm">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Histórico de Tasas (Todos los Datos)</h3>
                </div>
                <div class="chart-canvas-wrapper">
                    <canvas id="ratesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<?php
$dollarPricesUrl = Url::to(['site/dollar-prices']);
$historicalDataUrl = Url::to(['/dollar-statics/historical-data']);

$js = <<<JS
// Estado previo de los precios
let previousPrices = {
    oficial: null,
    paralelo: null
};

// Tasas numéricas actuales para la calculadora
let currentNumericRates = {
    oficial: null,
    paralelo: null
};

// Referencias a elementos de la calculadora
const calcCurrencySelect = document.getElementById('calc-currency');
const calcVesInput = document.getElementById('calc-ves');
const calcForeignInput = document.getElementById('calc-foreign');
const calcSwapBtn = document.getElementById('calc-swap');
const calcClearBtn = document.getElementById('calc-clear');

let calcDirection = 'ves-to-foreign'; // o 'foreign-to-ves'
let calcIsUpdating = false;

// Parsear precio con formato venezolano (puntos miles, coma decimal) a float
function parsePriceToFloat(price) {
    if (!price) return null;
    const normalized = price.toString().replace(/\./g, '').replace(',', '.');
    const value = parseFloat(normalized);
    return isNaN(value) ? null : value;
}

function getSelectedRateValue() {
    if (!calcCurrencySelect) return null;
    const sel = calcCurrencySelect.value; // 'USDT' o 'BCV'
    const type = sel === 'BCV' ? 'oficial' : 'paralelo';
    return currentNumericRates[type];
}

function recalcFromVES() {
    if (!calcVesInput || !calcForeignInput) return;
    const rate = getSelectedRateValue();
    const ves = parseFloat(calcVesInput.value);
    if (!rate || isNaN(ves)) return;
    calcIsUpdating = true;
    calcForeignInput.value = (ves / rate).toFixed(4);
    calcIsUpdating = false;
}

function recalcFromForeign() {
    if (!calcVesInput || !calcForeignInput) return;
    const rate = getSelectedRateValue();
    const foreign = parseFloat(calcForeignInput.value);
    if (!rate || isNaN(foreign)) return;
    calcIsUpdating = true;
    calcVesInput.value = (foreign * rate).toFixed(2);
    calcIsUpdating = false;
}

// Chart instance
let ratesChart = null;

// Función para crear la estructura de dígitos con animación
function createDigitStructure(price) {
    const parts = price.split(',');
    const integerPart = parts[0];
    const decimalPart = parts[1] || '00';
    
    let html = '<span class="rate-price-prefix">Bs.</span><span class="rate-price-value">';
    
    // Procesar parte entera (con separadores de miles)
    const integerDigits = integerPart.split('.');
    for (let i = 0; i < integerDigits.length; i++) {
        const digitGroup = integerDigits[i];
        for (let j = 0; j < digitGroup.length; j++) {
            html += '<span class="digit-container"><span class="digit-reel">' + digitGroup[j] + '</span></span>';
        }
        if (i < integerDigits.length - 1) {
            html += '<span class="thousands-separator">.</span>';
        }
    }
    
    // Agregar coma decimal
    html += '<span class="decimal-separator">,</span>';
    
    // Procesar parte decimal
    for (let i = 0; i < decimalPart.length; i++) {
        html += '<span class="digit-container"><span class="digit-reel">' + decimalPart[i] + '</span></span>';
    }
    
    html += '</span>';
    return html;
}

// Función para animar el cambio de dígitos
function animateDigitChange(container, newPrice, previousPrice) {
    if (!previousPrice || previousPrice === newPrice) {
        // Si no hay precio previo o es igual, solo mostrar sin animación
        container.innerHTML = createDigitStructure(newPrice);
        return;
    }
    
    // Crear nueva estructura
    const newHtml = createDigitStructure(newPrice);
    container.innerHTML = newHtml;
    
    // Animar los dígitos que cambiaron
    const digitContainers = container.querySelectorAll('.digit-container .digit-reel');
    digitContainers.forEach(reel => {
        reel.classList.add('spinning');
        setTimeout(() => {
            reel.classList.remove('spinning');
        }, 500);
    });
}

// Función para actualizar las tasas desde el servidor
function updateRates() {
    fetch('$dollarPricesUrl')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                data.data.forEach(rate => {
                    const rateType = rate.class; // 'oficial' o 'paralelo'
                    const container = document.getElementById('rate-' + rateType);
                    const metaContainer = document.getElementById('meta-' + rateType);
                    const footerContainer = document.getElementById('footer-' + rateType);
                    
                    if (container) {
                        // Animar solo si el precio cambió
                        animateDigitChange(container, rate.precio, previousPrices[rateType]);
                        
                        // Actualizar precio previo
                        previousPrices[rateType] = rate.precio;

                        // Guardar valor numérico para la calculadora
                        currentNumericRates[rateType] = parsePriceToFloat(rate.precio);
                    }
                    
                    if (metaContainer) {
                        // Mostrar la fecha/hora exactamente como viene desde el backend/BD
                        // Prioridad: campo de fecha explícito, luego timestamp (como respaldo)
                        let fechaTexto = '--';

                        if (rate.fecha_db) {
                            // Si el backend envía el valor tal cual de la BD
                            fechaTexto = rate.fecha_db;
                        } else if (rate.fecha) {
                            // O cualquier otro campo de fecha ya formateado por el backend
                            fechaTexto = rate.fecha;
                        } else if (rate.timestamp) {
                            // Respaldo: si solo llega timestamp, se convierte a string legible
                            const serverDate = new Date(rate.timestamp * 1000);
                            fechaTexto = serverDate.toISOString().replace('T', ' ').substring(0, 19);
                        }

                        metaContainer.textContent = 'Última actualización: ' + fechaTexto;
                    }
                    
                    // Actualizar footer con comparación del día anterior si existe
                    if (footerContainer && rate.ayer) {
                        const diff = rate.ayer.diferencia;
                        const percent = rate.ayer.porcentaje;
                        const fecha = rate.ayer.fecha;
                        
                        let diffClass = 'diff-neutral';
                        let diffIcon = '';
                        
                        if (diff > 0) {
                            diffClass = 'diff-positive';
                            diffIcon = '▲';
                        } else if (diff < 0) {
                            diffClass = 'diff-negative';
                            diffIcon = '▼';
                        }
                        
                        footerContainer.innerHTML = `
                            <span class="\${diffClass}">
                                \${diffIcon} \${Math.abs(percent).toFixed(2)}%
                            </span> 
                            de diferencia con respecto al día de ayer \${fecha}
                        `;
                        footerContainer.style.display = 'block';
                    }
                });
                
                // Actualizar sección del diferencial cambiario
                if (data.diferencial) {
                    const diffSection = document.getElementById('differential-section');
                    const diffBs = document.getElementById('diff-bs');
                    const diffUsd = document.getElementById('diff-usd');
                    const diffPercent = document.getElementById('diff-percent');
                    
                    if (diffSection && diffBs && diffUsd && diffPercent) {
                        diffBs.innerHTML = 'Bs. ' + data.diferencial.diferencia_bs.toFixed(2);
                        diffUsd.innerHTML = data.diferencial.diferencia_usd.toFixed(4) + ' <small>USD</small>';
                        diffPercent.innerHTML = data.diferencial.porcentaje.toFixed(2) + ' <small>%</small>';
                        
                        diffSection.style.display = 'block';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al actualizar tasas:', error);
        });
}

// Función para cargar datos históricos y actualizar el gráfico
function loadHistoricalData() {
    const url = '$historicalDataUrl';
    console.log('Fetching all historical data from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text.substring(0, 200));
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    updateChart(data.data);
                } else {
                    console.error('Error al cargar datos históricos:', data.message);
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                console.error('Response was:', text);
            }
        })
        .catch(error => {
            console.error('Error al cargar datos históricos:', error);
        });
}

// Función para actualizar el gráfico
function updateChart(data) {
    const ctx = document.getElementById('ratesChart').getContext('2d');
    
    // Destruir gráfico existente si existe
    if (ratesChart) {
        ratesChart.destroy();
    }
    
    // Preparar datasets
    const datasets = [];
    
    if (data.oficial && data.oficial.labels.length > 0) {
        datasets.push({
            label: 'Tasa BCV',
            data: data.oficial.values,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            borderWidth: 2,
            tension: 0.1,
            pointRadius: 3,
            pointHoverRadius: 5
        });
    }
    
    if (data.paralelo && data.paralelo.labels.length > 0) {
        datasets.push({
            label: 'Tasa Binance/Paralelo',
            data: data.paralelo.values,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            tension: 0.1,
            pointRadius: 3,
            pointHoverRadius: 5
        });
    }
    
    // Usar las etiquetas del primer dataset disponible
    const labels = data.oficial?.labels || data.paralelo?.labels || [];
    
    // Crear nuevo gráfico
    ratesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Bs. ' + context.parsed.y.toFixed(2);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return 'Bs. ' + value.toFixed(2);
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            }
        }
    });
}



// Eventos de la calculadora
if (calcVesInput && calcForeignInput) {
    calcVesInput.addEventListener('input', () => {
        if (calcIsUpdating) return;
        calcDirection = 'ves-to-foreign';
        recalcFromVES();
    });

    calcForeignInput.addEventListener('input', () => {
        if (calcIsUpdating) return;
        calcDirection = 'foreign-to-ves';
        recalcFromForeign();
    });
}

if (calcCurrencySelect) {
    calcCurrencySelect.addEventListener('change', () => {
        // Recalcular según el último sentido utilizado
        if (calcDirection === 'ves-to-foreign') {
            recalcFromVES();
        } else {
            recalcFromForeign();
        }
    });
}

if (calcSwapBtn && calcVesInput && calcForeignInput) {
    calcSwapBtn.addEventListener('click', () => {
        calcIsUpdating = true;
        const tmp = calcVesInput.value;
        calcVesInput.value = calcForeignInput.value;
        calcForeignInput.value = tmp;
        calcIsUpdating = false;

        calcDirection = calcDirection === 'ves-to-foreign'
            ? 'foreign-to-ves'
            : 'ves-to-foreign';
    });
}

if (calcClearBtn && calcVesInput && calcForeignInput) {
    calcClearBtn.addEventListener('click', () => {
        calcIsUpdating = true;
        calcVesInput.value = '';
        calcForeignInput.value = '';
        calcIsUpdating = false;
    });
}

// Actualizar inmediatamente al cargar la página
updateRates();
loadHistoricalData();

// Actualizar cada 30 segundos
setInterval(updateRates, 30000);

// Recargar gráfico cada 2 minutos
setInterval(() => {
    loadHistoricalData();
}, 120000);
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>
