<?php
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * Controlador de autenticación (login/logout).
 * @package Controladores
 */
class AutenticacionControlador
{
    /**
     * Muestra el formulario de login.
     * @param string|null $mensaje Mensaje flash opcional
     * @return void
     */
    public static function formulario($mensaje = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($mensaje) {
            $_SESSION['flash'] = $mensaje;
        }
        require __DIR__ . '/../vistas/acceso.php';
    }

    /**
     * Verifica credenciales y crea sesión.
     * @return void
     */
    public static function acceder()
    {
        $nombre = sanitize_text($_POST['nombre'] ?? '', 100);
        $clave = sanitize_text($_POST['clave'] ?? '', 100);

        if (mb_strlen($nombre) < 4 || mb_strlen($clave) < 4) {
            self::formulario('El nombre de usuario y la clave deben tener al menos 4 caracteres.');
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $pdo = get_db();
        $usuario = Usuario::buscarPorCredenciales($pdo, $nombre, $clave);
        if ($usuario) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_nombre'] = $usuario['nombre'];
            redirectTo('menu');
        }

        self::formulario('Credenciales incorrectas.');
    }

    /**
     * Cierra la sesión y redirige al login.
     * @return void
     */
    public static function cerrarSesion()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_unset();
        session_destroy();
        redirectTo('iniciar_sesion');
    }
}
