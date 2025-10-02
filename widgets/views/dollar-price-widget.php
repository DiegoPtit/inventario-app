<?php

use yii\helpers\Html;

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
?>

<div id="dollar-price-widget" class="dollar-price-container" onclick="handleDollarPriceClick()" style="cursor: pointer;" title="Hacer clic para actualizar precio paralelo">
    <?php foreach ($precios as $index => $precio): ?>
        <div class="dollar-price-item <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
            <span class="dollar-price-value"><?= Html::encode($precio['precio']) ?></span>
            <span class="dollar-price-label">TASA:</span>
            <span class="dollar-price-type <?= $precio['class'] ?>"><?= Html::encode($precio['tipo']) ?></span>
        </div>
    <?php endforeach; ?>
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

.dollar-price-item {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    width: 100%;
    justify-content: center;
}

.dollar-price-item.active {
    opacity: 1;
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

/* Responsive */
@media (max-width: 768px) {
    .dollar-price-container {
        width: 150px;
        padding: 6px 12px;
        height: 32px;
    }
    
    .dollar-price-item {
        font-size: 0.75rem;
        gap: 4px;
    }
    
    .dollar-price-label {
        font-size: 0.7rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.dollar-price-item');
    let currentIndex = 0;
    let updateTimer;
    let fadeTimer;
    
    // Función para alternar entre precios
    function showNext() {
        if (items.length <= 1) return;
        
        // Ocultar actual
        items[currentIndex].classList.remove('active');
        
        // Mostrar siguiente
        currentIndex = (currentIndex + 1) % items.length;
        items[currentIndex].classList.add('active');
    }
    
    // Función para actualizar precio oficial desde la API del BCV
    function updateDollarRates() {
        // Solo actualizar precio oficial (BCV)
        fetch('<?= \yii\helpers\Url::to(['site/update-dollar-rate']) ?>', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Precio oficial actualizado:', data.data.precio);
                // Si se actualizó el precio, recargar los datos del widget
                if (data.data.actualizado) {
                    refreshWidgetData();
                }
            } else {
                console.warn('Error al actualizar precio oficial:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al actualizar precio oficial:', error);
        });
    }
    
    // Función para refrescar los datos del widget
    function refreshWidgetData() {
        fetch('<?= \yii\helpers\Url::to(['site/dollar-prices']) ?>', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Actualizar el contenido del widget
                const container = document.getElementById('dollar-price-widget');
                if (container) {
                    // Limpiar items existentes
                    container.querySelectorAll('.dollar-price-item').forEach(item => item.remove());
                    
                    // Agregar nuevos items
                    data.data.forEach((precioData, index) => {
                        const item = document.createElement('div');
                        item.className = `dollar-price-item ${index === 0 ? 'active' : ''}`;
                        item.setAttribute('data-index', index);
                        item.innerHTML = `
                            <span class="dollar-price-value">${precioData.precio}</span>
                            <span class="dollar-price-label">TASA:</span>
                            <span class="dollar-price-type ${precioData.class}">${precioData.tipo}</span>
                        `;
                        container.appendChild(item);
                    });
                    
                    // Reiniciar el ciclo de fade
                    currentIndex = 0;
                    clearInterval(fadeTimer);
                    if (data.data.length > 1) {
                        fadeTimer = setInterval(showNext, 10000);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al refrescar datos del widget:', error);
        });
    }
    
    // Inicializar timers
    if (items.length > 1) {
        fadeTimer = setInterval(showNext, 10000);
    }
    
    // Actualizar precios cada 10 segundos
    updateTimer = setInterval(updateDollarRates, 10000);
    
    // Limpiar timers cuando la página se descarga
    window.addEventListener('beforeunload', function() {
        if (fadeTimer) clearInterval(fadeTimer);
        if (updateTimer) clearInterval(updateTimer);
    });
});

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
