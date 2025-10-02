<p align="center">
    <img src="web/uploads/Logos/ico_01-10-2025.png" height="100px" alt="Hava Inventario Logo">
    <h1 align="center" style="font-weight: bold; background: linear-gradient(135deg, #71ce5d 0%, #2ab693 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Hava Inventario</h1>
    <br>
</p>

**Hava Inventario** es un sistema completo de gestión de inventarios desarrollado con [Yii 2](https://www.yiiframework.com/), diseñado para pequeñas y medianas empresas que necesitan un control eficiente de sus productos, ventas, clientes y facturación.

El sistema incluye funcionalidades avanzadas como punto de venta (POS), gestión de clientes, control de stock en múltiples ubicaciones, facturación, seguimiento de cobros, y conversión de monedas con precios del dólar actualizables.

## 🚀 Características Principales

- **📦 Gestión de Productos**: Control completo de inventario con códigos de barras, categorías, imágenes y precios
- **🏪 Sistema POS**: Punto de venta integrado con facturación en tiempo real
- **👥 Gestión de Clientes**: Control de clientes con seguimiento de estado (solvente/moroso)
- **📊 Control de Stock**: Manejo de inventario en múltiples ubicaciones/almacenes
- **💰 Facturación**: Sistema completo de facturas con items detallados
- **📈 Seguimiento de Cobros**: Control de cuentas por cobrar y pagos
- **💵 Conversión de Monedas**: Precios del dólar oficial y paralelo actualizables
- **📋 Histórico de Movimientos**: Registro completo de entradas, salidas y ventas
- **🔐 Autenticación Google**: Login con Google OAuth además de usuarios locales
- **📱 Interfaz Moderna**: Diseño responsive y amigable al usuario

## 📁 Estructura del Proyecto

```
inventario-app/
├── assets/             # Definiciones de assets CSS/JS
├── commands/           # Comandos de consola
├── config/             # Configuraciones de la aplicación
│   ├── db.php         # Configuración de base de datos
│   ├── web.php        # Configuración principal
│   └── google-credentials.php # Credenciales OAuth Google
├── controllers/        # Controladores de la aplicación
│   ├── ProductosController.php    # Gestión de productos
│   ├── ClientesController.php     # Gestión de clientes
│   ├── FacturasController.php     # Gestión de facturas
│   ├── PosController.php          # Sistema punto de venta
│   ├── EntradasController.php     # Control de entradas
│   ├── SalidasController.php      # Control de salidas
│   └── ...
├── models/             # Modelos de datos (ActiveRecord)
│   ├── Productos.php              # Modelo de productos
│   ├── Clientes.php               # Modelo de clientes
│   ├── Facturas.php               # Modelo de facturas
│   ├── Stock.php                  # Modelo de inventario
│   ├── HistoricoMovimientos.php   # Histórico de movimientos
│   └── ...
├── views/              # Vistas de la aplicación
│   ├── productos/     # Vistas de productos
│   ├── clientes/      # Vistas de clientes
│   ├── facturas/      # Vistas de facturas
│   ├── pos/           # Vista del sistema POS
│   └── ...
├── web/                # Archivos web públicos
│   ├── uploads/       # Archivos subidos (imágenes de productos)
│   └── assets/        # Assets compilados
└── widgets/            # Widgets personalizados
    └── DollarPriceWidget.php # Widget de precios del dólar
```



## 🔧 Requisitos del Sistema

- **PHP**: >= 7.4.0
- **Base de datos**: MySQL/MariaDB
- **Servidor web**: Apache/Nginx
- **Extensiones PHP requeridas**:
  - PDO MySQL
  - GD (para manejo de imágenes)
  - cURL (para autenticación Google)
  - OpenSSL
  - Mbstring


## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone [URL_DEL_REPOSITORIO] hava-inventario
cd hava-inventario
```

### 2. Instalar Dependencias

Si no tienes [Composer](https://getcomposer.org/), instálalo siguiendo las instrucciones en [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

```bash
composer install
```

### 3. Configurar Base de Datos

1. Crea una base de datos MySQL/MariaDB
2. Edita el archivo `config/db.php` con tus datos de conexión:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=hava_inventario',
    'username' => 'tu_usuario',
    'password' => 'tu_contraseña',
    'charset' => 'utf8',
];
```

### 4. Configurar Autenticación Google (Opcional)

1. Copia el archivo de ejemplo: `cp config/google-credentials.example.php config/google-credentials.php`
2. Configura tus credenciales de Google OAuth siguiendo la guía en `GOOGLE_AUTH_SETUP.md`

### 5. Configurar Permisos

```bash
# En sistemas Unix/Linux
chmod 777 runtime web/assets web/uploads
chmod 755 yii
```

### 6. Acceder a la Aplicación

Configura tu servidor web para que apunte al directorio `web/` y accede a:

```
http://localhost/hava-inventario/web/
```


### 7. Instalación con Docker (Alternativa)

Si prefieres usar Docker:

```bash
# Actualizar dependencias
docker-compose run --rm php composer update --prefer-dist

# Ejecutar instalación
docker-compose run --rm php composer install    

# Iniciar contenedores
docker-compose up -d
```

Accede a la aplicación en: `http://127.0.0.1:8000`

**Notas Docker:**
- Versión mínima requerida: Docker Engine `17.04`
- La configuración usa un volumen host en `.docker-composer` para caché de composer


## ⚙️ Configuración

### Configuración Inicial de Usuarios

Después de la instalación, crea los usuarios iniciales ejecutando desde la consola:

```php
// Crear usuario administrador
use app\models\Usuarios;
$admin = Usuarios::createUser('admin', 'admin123', 'Administrador', 1);

// Crear usuario demo
$demo = Usuarios::createUser('demo', 'demo123', 'Usuario Demo', 0);
```

### Estructura de Base de Datos

El sistema requiere las siguientes tablas principales:

- **usuarios**: Gestión de usuarios del sistema
- **productos**: Catálogo de productos
- **clientes**: Base de datos de clientes
- **facturas**: Registro de facturas
- **items_factura**: Detalles de items por factura
- **stock**: Control de inventario por ubicación
- **entradas**: Registro de entradas de mercancía
- **salidas**: Registro de salidas de mercancía
- **historico_movimientos**: Histórico completo de movimientos
- **historico_cobros**: Seguimiento de cobros y pagos
- **lugares**: Ubicaciones/almacenes
- **categorias**: Categorías de productos
- **proveedores**: Base de datos de proveedores
- **historico_precios_dolar**: Precios históricos del dólar

### Configuración de Autenticación Google

Para habilitar el login con Google, consulta la documentación detallada en `GOOGLE_AUTH_SETUP.md`.

**NOTAS:**
- La base de datos debe crearse manualmente antes de acceder a la aplicación
- Revisa y edita los archivos en el directorio `config/` según tus necesidades
- Consulta la documentación en el directorio `tests/` para información sobre pruebas


## 🎯 Funcionalidades del Sistema

### 📦 Gestión de Productos
- **Catálogo completo**: Marca, modelo, color, SKU, descripción
- **Control de precios**: Costo y precio de venta
- **Códigos de barras**: Identificación única por producto
- **Imágenes**: Hasta 10 fotos por producto
- **Categorización**: Organización por categorías
- **Ubicaciones**: Control por almacén/lugar
- **Stock automático**: Creación de inventario inicial

### 🏪 Sistema POS (Punto de Venta)
- **Interfaz intuitiva**: Búsqueda rápida de productos
- **Facturación en tiempo real**: Generación automática de facturas
- **Gestión de clientes**: Selección y seguimiento de clientes
- **Control de stock**: Actualización automática del inventario
- **Conversión de monedas**: Precios en bolívares y dólares
- **Edición de facturas**: Modificación con registro de devoluciones

### 👥 Gestión de Clientes
- **Base de datos completa**: Información detallada de clientes
- **Estados de cuenta**: Solvente/Moroso automático
- **Histórico de compras**: Seguimiento de transacciones
- **Cuentas por cobrar**: Control de deudas pendientes

### 📊 Control de Inventario
- **Stock por ubicación**: Inventario separado por almacén
- **Entradas y salidas**: Registro detallado de movimientos
- **Histórico completo**: Trazabilidad de todos los movimientos
- **Reportes de inventario**: Valorización y cantidades
- **Alertas de stock**: Control de productos con bajo inventario

### 💰 Sistema de Facturación
- **Facturas detalladas**: Items con precios y cantidades
- **Códigos únicos**: Numeración automática
- **Múltiples monedas**: Soporte para bolívares y dólares
- **Histórico de cobros**: Seguimiento de pagos
- **Estados de factura**: Pendiente/Pagada/Parcial

### 📈 Reportes y Análisis
- **Dashboard principal**: Resumen ejecutivo del negocio
- **Valor del inventario**: Cálculo automático del valor total
- **Análisis de clientes**: Proporción solventes vs morosos
- **Histórico de precios**: Seguimiento del dólar oficial y paralelo
- **Movimientos detallados**: Registro completo de transacciones

## 🧪 Pruebas

Las pruebas están ubicadas en el directorio `tests/` y están desarrolladas con [Codeception PHP Testing Framework](https://codeception.com/).

Por defecto, hay 3 suites de pruebas:
- `unit`: Pruebas unitarias de componentes
- `functional`: Pruebas de interacción del usuario  
- `acceptance`: Pruebas en navegador real (deshabilitadas por defecto)

### Ejecutar Pruebas

```bash
# Ejecutar todas las pruebas
vendor/bin/codecept run

# Solo pruebas unitarias y funcionales
vendor/bin/codecept run unit,functional

# Con cobertura de código
vendor/bin/codecept run --coverage --coverage-html --coverage-xml
```

La salida de cobertura se encuentra en `tests/_output/`.

## 📚 Documentación Adicional

El proyecto incluye documentación detallada en archivos específicos:

- **`FORMULARIO_PRODUCTOS.md`**: Guía completa del formulario de productos y gestión de imágenes
- **`GOOGLE_AUTH_SETUP.md`**: Configuración paso a paso de autenticación con Google OAuth
- **`MIGRACION_USUARIOS.md`**: Documentación del sistema de usuarios y migración
- **`REGISTRO_DEVOLUCIONES.md`**: Funcionalidad de devoluciones en edición de facturas
- **`PRUEBAS_DEVOLUCIONES.md`**: Casos de prueba para el sistema de devoluciones

## 🔐 Seguridad

- **Autenticación robusta**: Sistema de usuarios con encriptación segura
- **Validación de datos**: Validaciones tanto en cliente como servidor
- **Protección CSRF**: Tokens de validación en formularios
- **Subida segura de archivos**: Validación de tipos y tamaños de imagen
- **Transacciones de BD**: Integridad de datos garantizada

## 🛠️ Tecnologías Utilizadas

- **Framework**: Yii 2.0.45
- **Frontend**: Bootstrap 5, jQuery, CSS3
- **Base de datos**: MySQL/MariaDB con Active Record
- **Autenticación**: Yii Auth + Google OAuth 2.0
- **Subida de archivos**: Yii UploadedFile
- **Testing**: Codeception Framework

## 🤝 Contribución

Para contribuir al proyecto:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia BSD-3-Clause. Ver el archivo `LICENSE.md` para más detalles.

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema:

- Revisa la documentación en los archivos `.md` del proyecto
- Consulta los issues existentes en el repositorio
- Crea un nuevo issue para reportar bugs o solicitar features

---

**Hava Inventario** - Sistema completo de gestión de inventarios desarrollado con ❤️ usando Yii Framework.
