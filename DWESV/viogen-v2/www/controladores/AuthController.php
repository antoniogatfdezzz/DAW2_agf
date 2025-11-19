<?php
require_once __DIR__ . '/../modelos/User.php';

class AuthController
{
    public static function loginForm($msg = null)
    {
        if ($msg) {
            $_SESSION['flash'] = $msg;
        }
        require __DIR__ . '/../vistas/login.php';
    }

    public static function doLogin()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $clave = trim($_POST['clave'] ?? '');

        // Validaciones mínimas
        if (strlen($nombre) < 4 || strlen($clave) < 4) {
            self::loginForm('El nombre de usuario y la clave deben tener al menos 4 caracteres.');
            return;
        }

        $pdo = get_db();
        $user = User::findByCredentials($pdo, $nombre, $clave);
        if ($user) {
            // Almacenar id en sesión (no la clave)
            // Regenerar id de sesión para prevenir fijación
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            header('Location: index.php?action=menu');
            exit;
        }

        self::loginForm('Credenciales incorrectas.');
    }

    public static function logout()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Borrar variables de sesión
        $_SESSION = [];
        // Eliminar la cookie de sesión (si existe)
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        // Destruir la sesión
        session_unset();
        session_destroy();
        // Redirigir a login
        header('Location: index.php?action=login');
        exit;
    }
}
