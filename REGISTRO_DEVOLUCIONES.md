# Registro de Devoluciones en Edición de Facturas

## Objetivo
Registrar en `HistoricoMovimientos` y `Entradas` las devoluciones de productos cuando un cliente devuelve productos al editar una factura.

## Cambios Implementados

### 1. Modificaciones en `controllers/PosController.php`

#### a) Importaciones Agregadas
```php
use app\models\Entradas;
```

#### b) Nuevo Método: `registrarDevolucion()`
Se creó un método privado que registra las devoluciones en dos tablas:

**HistoricoMovimientos:**
- `accion`: `ENTRADA` (devolución del cliente)
- `id_lugar_destino`: Lugar donde se devuelve el producto
- `id_lugar_origen`: `null` (viene del cliente)
- `cantidad`: Cantidad devuelta
- `referencia_id`: ID de la factura

**Entradas:**
- `id_producto`: Producto devuelto
- `cantidad`: Cantidad devuelta
- `id_lugar`: Lugar donde se devuelve
- `tipo_entrada`: 'Donación' (para evitar validación de proveedor)
- `nro_documento`: `DEV-{facturaId}-{timestamp}` (formato identificable)
- `id_proveedor`: `null` (no aplica para devoluciones)

#### c) Modificaciones en `actionUpdateInvoice()`

**1. Almacenamiento de información original mejorado:**
- Ahora se guarda también el `id_lugar` del stock original para cada item
- Esto permite rastrear de dónde salió originalmente cada producto

**2. Registro de devoluciones al actualizar items:**
Se registran devoluciones en tres casos:

- **Caso 1: Cambio de producto completo**
  - Si se reemplaza un producto por otro diferente
  - Se devuelve toda la cantidad del producto anterior
  - Motivo: "Devolución por cambio de producto en factura"

- **Caso 2: Reducción de cantidad**
  - Si se reduce la cantidad del mismo producto
  - Se devuelve solo la diferencia (cantidad anterior - cantidad nueva)
  - Motivo: "Devolución por reducción de cantidad en factura"

- **Caso 3: Eliminación de producto**
  - Si se elimina completamente un producto de la factura
  - Se devuelve toda la cantidad del producto eliminado
  - Motivo: "Devolución por eliminación de producto de factura"

## Flujo de Funcionamiento

### Al editar una factura:

1. **Se obtienen los items originales** con su ubicación de stock
2. **Para cada item que se modifica:**
   - Se restaura el stock (como antes)
   - **NUEVO:** Se registra la devolución en HistoricoMovimientos y Entradas
3. **Para cada item que se elimina:**
   - Se restaura el stock (como antes)
   - **NUEVO:** Se registra la devolución en HistoricoMovimientos y Entradas

## Consultas SQL para Verificar

### Ver devoluciones en HistoricoMovimientos:
```sql
SELECT hm.*, p.marca, p.modelo, l.nombre as lugar 
FROM historico_movimientos hm
JOIN productos p ON hm.id_producto = p.id
LEFT JOIN lugares l ON hm.id_lugar_destino = l.id
WHERE hm.accion = 'ENTRADA' 
  AND hm.id_lugar_origen IS NULL
ORDER BY hm.created_at DESC;
```

### Ver devoluciones en Entradas:
```sql
SELECT e.*, p.marca, p.modelo, l.nombre as lugar
FROM entradas e
JOIN productos p ON e.id_producto = p.id
JOIN lugares l ON e.id_lugar = l.id
WHERE e.nro_documento LIKE 'DEV-%'
ORDER BY e.created_at DESC;
```

## Beneficios

1. **Trazabilidad completa:** Ahora se puede rastrear exactamente qué productos fueron devueltos y cuándo
2. **Auditoría:** Queda registrada la razón de cada devolución
3. **Historial de movimientos coherente:** Los movimientos de entrada por devoluciones aparecen en los reportes
4. **Consistencia de datos:** El stock se equilibra correctamente y queda evidencia del por qué

## Notas Técnicas

- Las devoluciones se registran dentro de la misma transacción de actualización de factura
- Si algo falla, todo se revierte automáticamente (rollback)
- El campo `tipo_entrada` se establece como 'Donación' para evitar la validación de proveedor requerido
- El número de documento sigue el formato `DEV-{facturaId}-{timestamp}` para fácil identificación

