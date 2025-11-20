# Documentación: Sesión y autenticación (viogen)

Este breve documento explica cómo funciona la sesión de usuario tras un login correcto y cómo probar el middleware de autenticación del punto de entrada (`index.php`).

Resumen rápido
- Tras un login correcto el sistema guarda únicamente el identificador del usuario en la sesión: `$_SESSION['user_id']` y además `$_SESSION['user_nombre']` para mostrar nombre en la UI. Nunca se guarda la clave en la sesión.
- El punto de entrada único `index.php` actúa como middleware: si no existe `$_SESSION['user_id']` y la acción solicitada no está en la lista de rutas públicas, se devuelve HTTP 401 (Unauthorized) y se muestra el formulario de login con un mensaje.

Dónde se inicia la sesión
- `index.php` llama a `session_start()` al inicio y ejerce de Front Controller.
- Los controladores que pueden invocarse fuera del flujo estándar (por ejemplo `AuthController::loginForm()` o `AuthController::doLogin()`) comprueban y arranca la sesión si no está activa para evitar warnings y asegurar que `$_SESSION` está disponible.

Cómo probar (rápido)
1. Asegúrate de tener Apache y MySQL en ejecución (XAMPP u otro).
2. Abrir en el navegador la URL de la aplicación. Ejemplo (ajusta según tu virtual host):

   http://localhost/viogen-v2/www/index.php?action=menu

   - Si no has iniciado sesión deberías recibir HTTP 401 y ver el formulario de login con el mensaje "Acceso no autorizado. Por favor identifíquese.".

3. Ir a la pantalla de login explícitamente:

   http://localhost/viogen-v2/www/index.php?action=login

   - Introduce credenciales válidas.
   - Tras login correcto serás redirigido a `index.php?action=menu`.
   - En la sesión del servidor encontrarás `$_SESSION['user_id']` (identificador entero o string) y `$_SESSION['user_nombre']`.

4. Cerrar sesión:

   http://localhost/viogen-v2/www/index.php?action=logout

   - La sesión se destruye y se redirige al login.

Notas de seguridad y recomendaciones
- Asegúrate de que las contraseñas en la base de datos estén protegidas con `password_hash()` y verificadas con `password_verify()` en `User::findByCredentials` (revisar implementación si es necesario).
- Configurar `session.cookie_secure = 1` cuando el sitio sirva sobre HTTPS.
- Considerar habilitar `session.cookie_samesite = 'Lax'` o `Strict` según necesidades.
- Evitar exponer las vistas HTML estáticas sin pasar por `index.php`. Si los archivos en `vistas/html/` son directamente accesibles por URL, considera bloquear el acceso directo mediante `.htaccess` o moviendo esos archivos fuera del directorio público.

Contacto
- Si quieres que añada el `.htaccess` para proteger `vistas/html/` o que genere un `README.md` en la raíz con instrucciones de despliegue, indícamelo y lo añado.
