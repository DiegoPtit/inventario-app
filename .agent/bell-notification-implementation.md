# Actualización: Bell Notification y Widget de Precios

## Cambios Realizados

### 1. **Widget de Precios del Dólar en Sidebar (Desktop)**
**Ubicación:** `main.php` - Líneas 141-154 (Sidebar Footer)

**Implementación:**
- Agregado el `DollarPriceWidget` en el footer del sidebar para usuarios autenticados
- Posicionado arriba del botón de Cerrar Sesión
- Centrado con clase `d-flex justify-content-center`
- Margen inferior de 3 unidades (`mb-3`) para separación

```php
<?php if (!Yii::$app->user->isGuest): ?>
    <!-- Dollar Price Widget -->
    <div class="mb-3 d-flex justify-content-center">
        <?= DollarPriceWidget::widget() ?>
    </div>
<?php endif; ?>
```

### 2. **Bell Notification Icon en Footer Mobile**
**Ubicación:** `main.php` - Líneas 177-203 (Footer Mobile)

**Implementación:**
- Agregado el icono de bell notification en el footer móvil
- Posicionado antes del widget de precios del dólar
- Incluye las mismas características que la versión desktop:
  - Red micro-dot pulsante
  - Badge con contador de clientes pendientes
  - Click handler para abrir el modal

**IDs Únicos para Mobile:**
- `bell-notification-container-mobile`
- `bell-icon-mobile`
- `bell-notification-dot-mobile`
- `bell-notification-count-mobile`

### 3. **Actualización del JavaScript**
**Archivo:** `_main-js-snippets.php`

**Cambios en `updateBellNotification(count)`:**
- Ahora actualiza **AMBOS** iconos simultaneamente (desktop y mobile)
- Verifica la existencia de cada conjunto de elementos antes de actualizar
- Mantiene sincronizados los estados visuales

**Nuevo Handler:**
- `bellContainerMobile` - Click handler para la versión móvil
- Misma funcionalidad que la versión desktop
- Abre el modal `modalRecordatorioCobros`
- Permite reabrir el modal removiendo el flag de sessionStorage

### 4. **Estilos CSS Adicionales**
**Archivo:** `_main-css.php`

**Estilos para Mobile:**
```css
#bell-notification-container-mobile {
    display: inline-flex;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.3s ease;
}
```

- Animaciones compartidas (pulse-dot, badge-bounce)
- Efecto activo con `transform: scale(0.95)`
- Todas las transiciones suaves (0.3s ease)

## Contador Corregido

### Antes:
- Contaba el número total de **facturas** pendientes de pago
- Ejemplo: Cliente A (3 facturas) + Cliente B (2 facturas) = Badge mostraba "5"

### Ahora:
- Cuenta el número de **clientes** con cobros pendientes
- Ejemplo: Cliente A + Cliente B = Badge muestra "2"

**Código JavaScript:**
```javascript
// Count total clients with pending invoices (not total invoices)
let totalPendingClients = data.data.length;
updateBellNotification(totalPendingClients);
```

## Resumen de Elementos

### Desktop (Sidebar):
1. ✅ Bell notification icon (header)
2. ✅ DollarPriceWidget (footer)
3. ✅ Login/Logout button (footer)

### Mobile (Footer):
1. ✅ Home icon
2. ✅ Menu icon
3. ✅ Login/Logout icon
4. ✅ Bell notification icon (NUEVO)
5. ✅ DollarPriceWidget

## Comportamiento Sincronizado

Ambos iconos (desktop y mobile) están completamente sincronizados:
- ✅ Se actualizan al mismo tiempo
- ✅ Muestran el mismo contador
- ✅ Cambian de color simultáneamente (gris → rojo)
- ✅ Abren el mismo modal
- ✅ Responden al mismo evento de carga de página

## Compatibilidad

- ✅ Funciona en desktop (sidebar)
- ✅ Funciona en mobile (footer)
- ✅ Responsive design
- ✅ Animaciones suaves
- ✅ Sin conflictos entre versiones
