<?php
/**
 * Archivo de ejemplo para las credenciales de Google OAuth
 * 
 * Para usar la autenticación con Google:
 * 1. Copia este archivo como 'google-credentials.php'
 * 2. Ve a https://console.developers.google.com/
 * 3. Crea un nuevo proyecto o selecciona uno existente
 * 4. Habilita la API de Google+ o Google Identity
 * 5. Crea credenciales OAuth 2.0
 * 6. Configura las URIs de redirección autorizadas:
 *    - http://localhost/site/auth?authclient=google (para desarrollo)
 *    - https://tudominio.com/site/auth?authclient=google (para producción)
 * 7. Reemplaza los valores de ejemplo con tus credenciales reales
 */

return [
    'clientId' => 'TU_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
    'clientSecret' => 'TU_GOOGLE_CLIENT_SECRET',
];
