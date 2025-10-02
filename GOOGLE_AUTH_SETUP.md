# Configuración de Autenticación con Google

## Pasos para configurar Google OAuth

### 1. Configurar Google Cloud Console

1. Ve a [Google Cloud Console](https://console.developers.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la API de Google Identity
4. Ve a "Credenciales" y crea credenciales OAuth 2.0
5. Configura las URIs de redirección autorizadas:
   - Para desarrollo: `http://localhost/site/auth?authclient=google`
   - Para producción: `https://tudominio.com/site/auth?authclient=google`

### 2. Configurar las credenciales en la aplicación

Edita el archivo `config/web.php` y reemplaza las credenciales de ejemplo:

```php
'authClientCollection' => [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\Google',
            'clientId' => 'TU_CLIENT_ID_REAL.apps.googleusercontent.com',
            'clientSecret' => 'TU_CLIENT_SECRET_REAL',
        ],
    ],
],
```

### 3. Funcionalidades implementadas

- **Login con Google**: Los usuarios pueden iniciar sesión usando su cuenta de Google
- **Registro con Google**: Los nuevos usuarios pueden registrarse automáticamente
- **Vinculación de cuentas**: Si un usuario ya existe con email/password, se vincula automáticamente con Google
- **Gestión de usuarios**: Se crean usuarios automáticamente con los datos de Google

### 4. Campos de base de datos utilizados

El modelo `Usuarios` ya incluye los campos necesarios:
- `google_id`: ID único de Google
- `google_access_token`: Token de acceso de Google
- `username`: Se usa el email de Google como username
- `nombre`: Se usa el nombre completo de Google

### 5. Flujo de autenticación

1. Usuario hace clic en "Continuar con Google"
2. Se redirige a Google para autorización
3. Google redirige de vuelta con los datos del usuario
4. La aplicación:
   - Busca si existe un usuario con ese `google_id`
   - Si existe, hace login
   - Si no existe, busca por email para vincular cuentas
   - Si no existe, crea un nuevo usuario

### 6. Personalización del botón

El botón de Google se muestra automáticamente en las páginas de login y registro usando el widget `AuthChoice`. Puedes personalizar su apariencia modificando los archivos CSS o creando un widget personalizado.

### 7. Seguridad

- Los tokens de acceso se almacenan de forma segura
- Se valida que el usuario tenga permisos de Google
- Se manejan errores de autenticación apropiadamente
- Los usuarios nuevos no son administradores por defecto

## Notas importantes

- Asegúrate de que las URIs de redirección en Google Console coincidan exactamente con tu dominio
- Para producción, usa HTTPS
- Mantén las credenciales seguras y no las subas al repositorio
- Considera implementar logout de Google si es necesario
