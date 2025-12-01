# Guía de Pruebas - Registro de Devoluciones

## Preparación

1. Asegúrate de tener productos con stock disponible
2. Crea un cliente de prueba
3. Ten acceso a la base de datos para verificar los registros

## Escenarios de Prueba

### Escenario 1: Reducción de Cantidad

**Pasos:**
1. Crear una factura nueva con:
   - Cliente: Cualquiera
   - Producto A: 10 unidades
2. Anotar el stock inicial del Producto A
3. Editar la factura y reducir el Producto A a 5 unidades
4. Guardar cambios

**Resultado Esperado:**
- Stock del Producto A aumenta en 5 unidades
- En `historico_movimientos`: 
  - Se crea un registro con acción 'ENTRADA'
  - Cantidad: 5
  - id_lugar_destino: (lugar del producto)
  - referencia_id: ID de la factura
- En `entradas`:
  - Se crea un registro con cantidad: 5
  - nro_documento: DEV-{facturaId}-{timestamp}
  - id_proveedor: NULL

**SQL de verificación:**
```sql
-- Verificar movimiento
SELECT * FROM historico_movimientos 
WHERE referencia_id = {factura_id} 
  AND accion = 'ENTRADA' 
ORDER BY created_at DESC;

-- Verificar entrada
SELECT * FROM entradas 
WHERE nro_documento LIKE 'DEV-{factura_id}-%' 
ORDER BY created_at DESC;
```

---

### Escenario 2: Cambio de Producto

**Pasos:**
1. Crear una factura nueva con:
   - Producto A: 5 unidades
2. Anotar stock inicial de Producto A y Producto B
3. Editar la factura y cambiar Producto A por Producto B (5 unidades)
4. Guardar cambios

**Resultado Esperado:**
- Stock de Producto A aumenta en 5 unidades (devolución completa)
- Stock de Producto B disminuye en 5 unidades (nueva venta)
- En `historico_movimientos`:
  - ENTRADA para Producto A: 5 unidades (devolución)
  - VENTA para Producto B: 5 unidades (si se registra desde la creación original)
- En `entradas`:
  - Registro de devolución de Producto A con 5 unidades

---

### Escenario 3: Eliminación de Producto

**Pasos:**
1. Crear una factura nueva con:
   - Producto A: 8 unidades
   - Producto B: 3 unidades
2. Anotar stock inicial de ambos productos
3. Editar la factura y eliminar completamente Producto B
4. Guardar cambios

**Resultado Esperado:**
- Stock de Producto A: sin cambios (no se tocó)
- Stock de Producto B: aumenta en 3 unidades (devolución completa)
- En `historico_movimientos`:
  - ENTRADA para Producto B: 3 unidades con motivo de eliminación
- En `entradas`:
  - Registro de devolución de Producto B con 3 unidades
  - nro_documento: DEV-{facturaId}-{timestamp}

---

### Escenario 4: Múltiples Cambios Simultáneos

**Pasos:**
1. Crear una factura nueva con:
   - Producto A: 10 unidades
   - Producto B: 5 unidades
   - Producto C: 8 unidades
2. Editar la factura y hacer varios cambios:
   - Producto A: reducir a 3 unidades
   - Producto B: eliminar completamente
   - Producto C: mantener igual (8 unidades)
   - Agregar Producto D: 4 unidades
3. Guardar cambios

**Resultado Esperado:**
- Stock de Producto A: +7 unidades (devolución de diferencia)
- Stock de Producto B: +5 unidades (devolución completa)
- Stock de Producto C: sin cambios
- Stock de Producto D: -4 unidades (nueva venta)
- En `historico_movimientos`:
  - ENTRADA para Producto A: 7 unidades
  - ENTRADA para Producto B: 5 unidades
- En `entradas`:
  - 2 registros de devolución (A y B)

---

## Verificaciones en la Base de Datos

### 1. Contar devoluciones por factura
```sql
SELECT 
    f.codigo,
    f.id,
    COUNT(hm.id) as total_devoluciones
FROM facturas f
LEFT JOIN historico_movimientos hm 
    ON hm.referencia_id = f.id 
    AND hm.accion = 'ENTRADA'
    AND hm.id_lugar_origen IS NULL
GROUP BY f.id, f.codigo
HAVING total_devoluciones > 0
ORDER BY f.id DESC;
```

### 2. Ver detalles de devoluciones
```sql
SELECT 
    hm.id,
    hm.created_at as fecha_devolucion,
    p.marca,
    p.modelo,
    hm.cantidad,
    l.nombre as lugar_destino,
    f.codigo as factura
FROM historico_movimientos hm
JOIN productos p ON hm.id_producto = p.id
LEFT JOIN lugares l ON hm.id_lugar_destino = l.id
JOIN facturas f ON hm.referencia_id = f.id
WHERE hm.accion = 'ENTRADA' 
    AND hm.id_lugar_origen IS NULL
ORDER BY hm.created_at DESC
LIMIT 20;
```

### 3. Verificar coherencia entre Stock, HistoricoMovimientos y Entradas
```sql
-- Para una factura específica
SET @factura_id = 1; -- Cambiar por ID de factura a verificar

SELECT 
    'Movimientos' as tabla,
    COUNT(*) as registros
FROM historico_movimientos 
WHERE referencia_id = @factura_id 
    AND accion = 'ENTRADA'
    AND id_lugar_origen IS NULL

UNION ALL

SELECT 
    'Entradas' as tabla,
    COUNT(*) as registros
FROM entradas 
WHERE nro_documento LIKE CONCAT('DEV-', @factura_id, '-%');
```

## Casos Edge (Extremos)

### Caso 1: Editar factura sin cliente
- Debe funcionar normalmente
- Las devoluciones se registran igual

### Caso 2: Producto sin stock previo
- El sistema debería manejar esto creando o actualizando el stock
- Verificar que no falle

### Caso 3: Editar factura múltiples veces
- Cada edición debe generar sus propios registros de devolución
- Verificar que no haya duplicados

## Rollback de Transacción

**Prueba de integridad:**
1. Modificar temporalmente el código para forzar un error después del registro de devoluciones
2. Intentar editar una factura
3. Verificar que:
   - El stock NO cambió
   - NO se crearon registros en historico_movimientos
   - NO se crearon registros en entradas
   - La factura permanece sin cambios

Esto confirma que la transacción funciona correctamente.

