<?php

use yii\helpers\Html;
use yii\helpers\Json;

$precios = [];
if ($precioOficial) {
    $precios[] = [
        'precio' => number_format($precioOficial->precio_ves, 2, ',', '.'),
        'tipo' => $precioOficial->displayTipo(),
        'class' => 'text-success-light'
    ];
}

if ($precioParalelo) {
    $precios[] = [
        'precio' => number_format($precioParalelo->precio_ves, 2, ',', '.'),
        'tipo' => $precioParalelo->displayTipo(),
        'class' => 'text-warning'
    ];
}

if (empty($precios)) {
    return;
}

// Convertir a JSON para JavaScript
$preciosJson = Json::encode($precios);
?>

<div id="dollar-price-widget" class="dollar-price-container" onclick="handleDollarPriceClick()" style="cursor: pointer;" title="Hacer clic para actualizar precio paralelo">
    <div class="dollar-price-display">
        <span class="dollar-price-value" id="dpw-value"></span>
        <span class="dollar-price-label">TASA:</span>
        <span class="dollar-price-type" id="dpw-type"></span>
    </div>
</div>

<style>
.dollar-price-container {
    position: relative;
    width: 200px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #495057;
    border-radius: 20px;
    padding: 8px 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    border: 1px solid #6c757d;
    overflow: hidden;
    transition: all 0.3s ease;
}

.dollar-price-container:hover {
    background: #6c757d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.dollar-price-display {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    opacity: 1;
    transition: opacity 0.4s ease-in-out;
}

.dollar-price-display.fade-out {
    opacity: 0;
}

.dollar-price-value {
    color: #fff;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.dollar-price-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.75rem;
    font-weight: 500;
}

.dollar-price-type {
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.text-success-light {
    color: #90EE90 !important; /* Verde claro para precio oficial */
}

.text-warning {
    color: #ffc107 !important; /* Amarillo para precio paralelo */
}

/* Responsive */
@media (max-width: 768px) {
    .dollar-price-container {
        width: 150px;
        padding: 6px 12px;
        height: 32px;
    }
    
    .dollar-price-display {
        font-size: 0.75rem;
        gap: 4px;
    }
    
    .dollar-price-label {
        font-size: 0.7rem;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // Widget state - MANTIENE ESTADO EN JAVASCRIPT
    const DollarPriceWidget = {
        precios: <?= $preciosJson ?>,
        currentIndex: 0,
        fadeTimer: null,
        updateTimer: null,
        isUpdating: false,
        
        // Elementos del DOM
        elements: {
            display: null,
            value: null,
            type: null
        },
        
        // Inicializar widget
        init: function() {
            // Obtener elementos del DOM
            this.elements.display = document.querySelector('.dollar-price-display');
            this.elements.value = document.getElementById('dpw-value');
            this.elements.type = document.getElementById('dpw-type');
            
            if (!this.elements.display || !this.elements.value || !this.elements.type) {
                console.error('DollarPriceWidget: No se encontraron elementos del DOM');
                return;
            }
            
            // Mostrar el primer precio
            this.updateDisplay(false);
            
            // Iniciar rotación si hay más de un precio
            if (this.precios.length > 1) {
                this.fadeTimer = setInterval(() => this.rotate(), 8000);
            }
            
            // Actualizar precios desde el servidor cada 15 segundos
            this.updateTimer = setInterval(() => this.fetchPrices(), 15000);
            
            // Limpiar timers al salir
            window.addEventListener('beforeunload', () => this.cleanup());
            
            console.log('DollarPriceWidget: Inicializado con', this.precios.length, 'precio(s)');
        },
        
        // Actualizar la visualización
        updateDisplay: function(withFade = true) {
            if (this.precios.length === 0) {
                console.warn('DollarPriceWidget: No hay precios para mostrar');
                return;
            }
            
            const precio = this.precios[this.currentIndex];
            
            if (!precio) {
                console.error('DollarPriceWidget: Precio no encontrado en índice', this.currentIndex);
                return;
            }
            
            const updateContent = () => {
                this.elements.value.textContent = precio.precio;
                this.elements.type.textContent = precio.tipo;
                this.elements.type.className = 'dollar-price-type ' + precio.class;
            };
            
            if (withFade && !this.isUpdating) {
                // Fade out
                this.elements.display.classList.add('fade-out');
                
                setTimeout(() => {
                    // Actualizar contenido
                    updateContent();
                    
                    // Fade in
                    this.elements.display.classList.remove('fade-out');
                }, 400);
            } else {
                // Sin animación
                updateContent();
            }
        },
        
        // Rotar al siguiente precio
        rotate: function() {
            if (this.precios.length <= 1) return;
            
            this.currentIndex = (this.currentIndex + 1) % this.precios.length;
            this.updateDisplay(true);
        },
        
        // Obtener precios actualizados del servidor
        fetchPrices: function() {
            if (this.isUpdating) return;
            
            this.isUpdating = true;
            
            fetch('<?= \yii\helpers\Url::to(['site/dollar-prices']) ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    // Actualizar array de precios
                    this.precios = data.data;
                    
                    // Si el índice actual está fuera de rango, resetearlo
                    if (this.currentIndex >= this.precios.length) {
                        this.currentIndex = 0;
                    }
                    
                    // Actualizar display
                    this.updateDisplay(false);
                    
                    // Re-iniciar rotación si es necesario
                    if (this.precios.length > 1 && !this.fadeTimer) {
                        this.fadeTimer = setInterval(() => this.rotate(), 8000);
                    } else if (this.precios.length <= 1 && this.fadeTimer) {
                        clearInterval(this.fadeTimer);
                        this.fadeTimer = null;
                    }
                    
                    console.log('DollarPriceWidget: Precios actualizados desde el servidor');
                }
            })
            .catch(error => {
                console.error('DollarPriceWidget: Error al obtener precios:', error);
            })
            .finally(() => {
                this.isUpdating = false;
            });
        },
        
        // Limpiar timers
        cleanup: function() {
            if (this.fadeTimer) {
                clearInterval(this.fadeTimer);
                this.fadeTimer = null;
            }
            if (this.updateTimer) {
                clearInterval(this.updateTimer);
                this.updateTimer = null;
            }
            console.log('DollarPriceWidget: Limpieza completada');
        }
    };
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => DollarPriceWidget.init());
    } else {
        DollarPriceWidget.init();
    }
    
    // Exponer para debugging
    window.DollarPriceWidget = DollarPriceWidget;
})();

// Función global para manejar el clic en el widget de precio del dólar
function handleDollarPriceClick() {
    // Verificar si el usuario está logueado
    <?php if (Yii::$app->user->isGuest): ?>
        // Si no está logueado, redirigir a login
        window.location.href = '<?= \yii\helpers\Url::to(['site/login']) ?>';
    <?php else: ?>
        // Si está logueado, abrir el modal
        openUpdatePriceModal();
    <?php endif; ?>
}

// Función global para abrir el modal de actualización de precio
function openUpdatePriceModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalActualizarPrecioParalelo'));
    modal.show();
}
</script>
