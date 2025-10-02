# Formulario de Productos - Documentación

## Descripción General

Se ha implementado un formulario completo y moderno para la creación y edición de productos en el inventario, con diseño inspirado en las tarjetas de productos de la página principal.

## Características Implementadas

### 1. Campos del Formulario

#### Información Básica
- **Marca**: Marca del producto (opcional)
- **Modelo**: Modelo del producto (opcional)
- **Color**: Color del producto (opcional)
- **SKU**: Código único del producto (opcional)
- **Descripción**: Descripción detallada del producto (opcional)

#### Especificaciones y Medidas
- **Contenido Neto**: Cantidad numérica con decimales (opcional)
- **Unidad de Medida**: Campo de texto libre (ml, gr, kg, unidades, etc.)
- **Código de Barra**: Código de barras único del producto (opcional)

#### Precios
- **Costo**: Costo de adquisición del producto (numérico con decimales)
- **Precio de Venta**: Precio de venta al público (numérico con decimales)

#### Ubicación y Categorización
- **Lugar de Almacén**: Dropdown que referencia la tabla `lugares`
- **Categoría**: Dropdown que referencia la tabla `categorias`
- **Cantidad Inicial**: Campo numérico que solo aparece al crear un nuevo producto

#### Imágenes
- **Fotos del Producto**: Permite subir múltiples imágenes (hasta 10)
- Formatos aceptados: PNG, JPG, JPEG, GIF, WEBP
- Las imágenes se guardan en `@webroot/uploads/`
- Las rutas se almacenan como array JSON en el campo `fotos`

### 2. Funcionalidades Automáticas

#### Timestamps
- `created_at` y `updated_at` se rellenan automáticamente
- Estos campos están ocultos en el formulario

#### Gestión de Stock
Al crear un nuevo producto con cantidad inicial:
1. Se crea el producto
2. Se crea una entrada en la tabla `entradas` con la cantidad especificada
3. Se crea o actualiza el registro en la tabla `stock` con la cantidad correspondiente

Todo esto se maneja en una transacción de base de datos para garantizar la integridad de los datos.

### 3. Subida de Imágenes

#### Características:
- Múltiples imágenes por producto
- Nombres únicos generados con `uniqid()`
- Las imágenes se mantienen al actualizar (no se eliminan)
- Al editar, se pueden agregar más imágenes a las existentes
- Preview de imágenes existentes en modo edición

#### Estructura de Almacenamiento:
```
web/
  uploads/
    [timestamp]_[filename].[ext]
```

#### Formato en Base de Datos:
```json
[
  "uploads/abc123_imagen1.jpg",
  "uploads/def456_imagen2.png"
]
```

## Diseño Visual

El formulario incluye:
- Diseño moderno con bordes redondeados
- Secciones claramente divididas con iconos
- Animaciones suaves en hover
- Diseño responsive para móviles
- Colores consistentes con el resto de la aplicación
- Campos agrupados lógicamente

### Paleta de Colores:
- Verde (#28a745): Botón de crear
- Azul (#007bff): Botón de actualizar e iconos
- Gris claro (#f8f9fa): Fondos
- Gris oscuro (#333): Textos principales

## Archivos Modificados

### Modelo
- `models/Productos.php`: Agregadas propiedades `imageFiles` y `cantidad`, método `uploadImages()`

### Controlador
- `controllers/ProductosController.php`: 
  - Modificado `actionCreate()` para manejar imágenes y crear entradas/stock
  - Modificado `actionUpdate()` para manejar imágenes adicionales

### Vistas
- `views/productos/_form.php`: Formulario completo con diseño moderno
- `views/productos/create.php`: Header mejorado con iconos y descripción
- `views/productos/update.php`: Header mejorado con nombre del producto
- `views/site/index.php`: Botón "Agregar más productos" enlazado correctamente

### Estructura
- `web/uploads/`: Directorio para almacenar las imágenes de productos

## Uso

### Crear Nuevo Producto

1. Navegar a `/productos/create` o hacer clic en "Agregar más productos al Inventario" desde la página principal
2. Completar los campos requeridos
3. Seleccionar el lugar de almacén
4. (Opcional) Ingresar cantidad inicial para crear stock automáticamente
5. (Opcional) Subir imágenes del producto
6. Hacer clic en "Crear Producto"

### Editar Producto Existente

1. Navegar a `/productos/update?id=[ID]`
2. Modificar los campos necesarios
3. (Opcional) Agregar más imágenes (las existentes se mantienen)
4. Hacer clic en "Actualizar Producto"

## Validaciones

- Campos numéricos validados en el servidor
- Extensiones de archivo validadas
- Máximo 10 imágenes por carga
- Código de barras único (no se permiten duplicados)
- Referencias a `lugares` y `categorias` validadas

## Mensajes Flash

El sistema muestra mensajes al usuario:
- ✅ Éxito: "Producto creado exitosamente" / "Producto actualizado exitosamente"
- ❌ Error: Mensajes específicos según el tipo de error

## Consideraciones Técnicas

### Transacciones
Se utilizan transacciones de base de datos para garantizar que:
- Si falla la creación del producto, no se crea la entrada ni el stock
- Si falla la creación de la entrada, se revierten todos los cambios
- Si falla la actualización del stock, se revierten todos los cambios

### Seguridad
- Validación de tipos de archivo en el servidor
- Nombres de archivo únicos para evitar sobrescritura
- Validación de relaciones de base de datos (lugares, categorías)
- Protección contra inyección SQL mediante Active Record de Yii2

### Performance
- Carga de imágenes optimizada
- Dropdowns cargados una sola vez
- CSS y JS inline para evitar peticiones adicionales

## Mejoras Futuras Sugeridas

1. **Eliminación de imágenes individuales**: Permitir eliminar imágenes específicas al editar
2. **Reordenamiento de imágenes**: Drag & drop para cambiar el orden
3. **Compresión de imágenes**: Optimizar tamaño automáticamente
4. **Validación de dimensiones**: Sugerir tamaños específicos
5. **Preview antes de subir**: Mostrar miniaturas antes de guardar
6. **Gestión de stock más avanzada**: Permitir agregar stock desde la edición
7. **Código de barras escaneado**: Integración con lector de códigos de barras
8. **Generación automática de SKU**: Basado en categoría y secuencia

## Soporte

Para cualquier problema o pregunta sobre el formulario de productos, consulta la documentación de Yii2 Framework o revisa los archivos de código mencionados anteriormente.
