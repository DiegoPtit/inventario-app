# Migración del Sistema de Usuarios

## ✅ Cambios Realizados

El sistema ahora usa el modelo `Usuarios` en lugar de `User` para la autenticación. Los cambios implementados incluyen:

### 1. Modelo Usuarios
- ✅ Implementa `IdentityInterface` de Yii2
- ✅ Métodos de autenticación con base de datos real
- ✅ Encriptación segura de contraseñas con `Yii::$app->security`
- ✅ Generación automática de `auth_key`
- ✅ Timestamps automáticos (`created_at`, `updated_at`)
- ✅ Validaciones mejoradas

### 2. Configuración
- ✅ `config/web.php` actualizado para usar `app\models\Usuarios`
- ✅ `LoginForm` actualizado para usar el nuevo modelo
- ✅ Vista de login actualizada

### 3. Funcionalidades Nuevas
- ✅ Método `createUser()` para crear usuarios fácilmente
- ✅ Método `setPassword()` para encriptar contraseñas
- ✅ Validación de username (solo letras, números y guiones bajos)

## 🔧 Pasos para Completar la Migración

### 1. Crear Usuarios Iniciales
Ejecuta el script de migración para crear usuarios de prueba:

```php
// Desde la consola o un controlador
use app\models\Usuarios;

// Crear usuario admin
$admin = Usuarios::createUser('admin', 'admin', 'Administrador', 1);

// Crear usuario demo  
$demo = Usuarios::createUser('demo', 'demo', 'Usuario Demo', 0);
```

### 2. Verificar la Tabla de Usuarios
Asegúrate de que la tabla `usuarios` exista en tu base de datos con la siguiente estructura:

```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    access_token VARCHAR(255) NULL,
    auth_key VARCHAR(255) NULL,
    admin INT DEFAULT 0,
    google_id VARCHAR(255) NULL,
    google_access_token VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 3. Limpiar Archivos Antiguos (Opcional)
Una vez verificado que todo funciona correctamente, puedes:

- Mantener `models/User.php` como respaldo temporal
- Actualizar tests para usar `Usuarios` en lugar de `User`
- Eliminar referencias obsoletas en comentarios

## 🔐 Creación de Nuevos Usuarios

### Método Simple
```php
$usuario = Usuarios::createUser('nuevo_user', 'password123', 'Nombre Completo');
```

### Método Detallado
```php
$usuario = new Usuarios();
$usuario->username = 'nuevo_user';
$usuario->nombre = 'Nombre Completo';
$usuario->admin = 0; // 0 = usuario normal, 1 = admin
$usuario->setPassword('password123'); // Encripta automáticamente
$usuario->save();
```

## 🧪 Verificación

Para verificar que todo funciona:

1. Accede a `/site/login`
2. Usa las credenciales que hayas creado
3. El login debe funcionar correctamente
4. La sesión debe mantenerse con "Remember Me"

## ⚠️ Notas Importantes

- Las contraseñas ahora se encriptan con `Yii::$app->security`
- Los `auth_key` se generan automáticamente
- Los usuarios antiguos del array estático ya no funcionarán
- Los tests necesitarán actualización para usar datos reales de BD

## 🔄 Rollback (si es necesario)

Si necesitas revertir los cambios:

1. Restaura `config/web.php`:
   ```php
   'identityClass' => 'app\models\User',
   ```

2. Revierte `LoginForm.php` para usar `User::findByUsername()`

3. Los usuarios antiguos (`admin/admin`, `demo/demo`) volverán a funcionar
