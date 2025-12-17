# API Map - Sistema de Inventario

Documentación de todos los endpoints disponibles en la API REST.

**Base URL:** `http://tu-dominio.com/index.php?r=api/`

**Formato de respuesta:** JSON

**Versión:** 1.0

---

## Estructura de Respuesta Estándar

### Respuesta Exitosa
```json
{
    "success": true,
    "message": "OK",
    "data": { ... },
    "timestamp": "2025-12-17 08:31:00"
}
```

### Respuesta de Error
```json
{
    "success": false,
    "message": "Descripción del error",
    "error_code": 400,
    "timestamp": "2025-12-17 08:31:00"
}
```

---

## Endpoints

### 1. Productos

#### GET `/api/productos`

Obtiene la lista de todos los productos con información completa.

**URL completa:** `index.php?r=api/productos`

**Método:** `GET`

**Parámetros de Query (opcionales):**

| Parámetro   | Tipo   | Descripción                                      |
|-------------|--------|--------------------------------------------------|
| `categoria` | int    | Filtra productos por ID de categoría             |
| `buscar`    | string | Búsqueda por marca, modelo, descripción o código |
| `limit`     | int    | Limita la cantidad de resultados                 |
| `offset`    | int    | Salta N primeros resultados (para paginación)    |

**Ejemplo de solicitud:**
```
GET /index.php?r=api/productos&categoria=1&limit=10&offset=0
```

**Ejemplo de respuesta exitosa:**
```json
{
    "success": true,
    "message": "OK",
    "data": {
        "productos": [
            {
                "id": 1,
                "marca": "Samsung",
                "modelo": "Galaxy S21",
                "color": "Negro",
                "descripcion": "Smartphone de alta gama",
                "contenido_neto": null,
                "contenido_neto_valor": null,
                "unidad_medida": null,
                "costo": 500.00,
                "precio_venta": 750.00,
                "codigo_barra": "7591234567890",
                "sku": "SAM-S21-BLK",
                "id_categoria": 1,
                "categoria": "Electrónicos",
                "stock": 25,
                "foto": "http://dominio.com/uploads/abc123.jpg"
            }
        ],
        "total": 100,
        "count": 10,
        "limit": 10,
        "offset": 0
    },
    "timestamp": "2025-12-17 08:31:00"
}
```

---

#### GET `/api/producto/{id}`

Obtiene información detallada de un producto específico.

**URL completa:** `index.php?r=api/producto&id={id}`

**Método:** `GET`

**Parámetros de Ruta:**

| Parámetro | Tipo | Descripción          |
|-----------|------|----------------------|
| `id`      | int  | ID único del producto |

**Ejemplo de solicitud:**
```
GET /index.php?r=api/producto&id=1
```

**Ejemplo de respuesta exitosa:**
```json
{
    "success": true,
    "message": "OK",
    "data": {
        "id": 1,
        "marca": "Samsung",
        "modelo": "Galaxy S21",
        "color": "Negro",
        "descripcion": "Smartphone de alta gama",
        "contenido_neto": null,
        "contenido_neto_valor": null,
        "unidad_medida": null,
        "costo": 500.00,
        "precio_venta": 750.00,
        "codigo_barra": "7591234567890",
        "sku": "SAM-S21-BLK",
        "id_categoria": 1,
        "categoria": "Electrónicos",
        "stock": 25,
        "foto": "http://dominio.com/uploads/abc123.jpg",
        "fotos": [
            "http://dominio.com/uploads/abc123.jpg",
            "http://dominio.com/uploads/def456.jpg"
        ],
        "stock_por_ubicacion": [
            {
                "id_lugar": 1,
                "lugar": "Almacén Principal",
                "cantidad": 15
            },
            {
                "id_lugar": 2,
                "lugar": "Tienda Centro",
                "cantidad": 10
            }
        ],
        "created_at": "2025-01-15 10:30:00",
        "updated_at": "2025-12-17 08:00:00"
    },
    "timestamp": "2025-12-17 08:31:00"
}
```

**Posibles errores:**

| Código | Mensaje                  |
|--------|--------------------------|
| 404    | Producto no encontrado   |
| 500    | Error interno del servidor |

---

### 2. Categorías

#### GET `/api/categorias`

Obtiene la lista de todas las categorías disponibles.

**URL completa:** `index.php?r=api/categorias`

**Método:** `GET`

**Parámetros:** Ninguno

**Ejemplo de solicitud:**
```
GET /index.php?r=api/categorias
```

**Ejemplo de respuesta exitosa:**
```json
{
    "success": true,
    "message": "OK",
    "data": [
        {
            "id": 1,
            "titulo": "Electrónicos",
            "descripcion": "Dispositivos electrónicos y gadgets"
        },
        {
            "id": 2,
            "titulo": "Hogar",
            "descripcion": "Artículos para el hogar"
        }
    ],
    "timestamp": "2025-12-17 10:15:00"
}
```

---

## Códigos de Estado HTTP

| Código | Descripción              |
|--------|--------------------------|
| 200    | Solicitud exitosa        |
| 400    | Solicitud incorrecta     |
| 404    | Recurso no encontrado    |
| 500    | Error interno del servidor |

---

## Notas

- Todos los endpoints soportan CORS para consumo desde diferentes dominios.
- Las respuestas incluyen siempre un `timestamp` con la fecha/hora de la respuesta.
- El campo `foto` contiene la URL completa de la primera imagen del producto.
- En la vista detallada (`/api/producto`), el campo `fotos` contiene todas las imágenes.
- El campo `stock` siempre refleja el total sumado de todas las ubicaciones.

---

*Última actualización: 2025-12-17*
