<?php
require_once APP_MODELOS . '/Funciones.php';

class AutenticacionControlador {
    public function __construct(private AutenticacionModelo $modelo = new AutenticacionModelo()) {}

    public function formularioLogin(): void {
        // Si ya está logueado, redirigir por tipo o a cambio de contraseña
        if (!empty($_SESSION['user_id'])) {
            if (!empty($_SESSION['password_temporal'])) {
                header('Location: /auth/cambiar-password');
                exit;
            }
            $this->redirigirPorTipo();
            return;
        }
        vista('auth/iniciar_sesion', [
            'mensaje' => ''
        ]);
    }

    public function iniciarSesion(): void {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $mensaje = '';
        if ($usuario === '' || $password === '') {
            $mensaje = error_message('Por favor, complete todos los campos');
            vista('auth/iniciar_sesion', compact('mensaje'));
            return;
        }
        if ($this->modelo->login($usuario, $password)) {
            if (!empty($_SESSION['password_temporal'])) {
                header('Location: /auth/cambiar-password');
                exit;
            }
            $this->redirigirPorTipo();
            return;
        }
        $mensaje = error_message('Usuario o contraseña incorrectos');
        vista('auth/iniciar_sesion', compact('mensaje'));
    }

    public function cerrarSesion(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /');
        exit;
    }

    public function formularioCambiarPassword(): void {
        $this->requerirLogin();
        vista('auth/cambiar_password', [
            'mensaje' => ''
        ]);
    }

    public function cambiarPassword(): void {
        $this->requerirLogin();
        $actual = sanitize_input($_POST['password_actual'] ?? '');
        $nueva = sanitize_input($_POST['password_nueva'] ?? '');
        $confirmar = sanitize_input($_POST['password_confirmar'] ?? '');
        if ($nueva !== $confirmar) {
            $mensaje = error_message('Las contraseñas nuevas no coinciden');
            vista('auth/cambiar_password', compact('mensaje'));
            return;
        }
        if (strlen($nueva) < 6) {
            $mensaje = error_message('La nueva contraseña debe tener al menos 6 caracteres');
            vista('auth/cambiar_password', compact('mensaje'));
            return;
        }
        $res = $this->modelo->cambiarPassword($actual, $nueva);
        if ($res['ok']) {
            $mensaje = success_message($res['msg']);
            vista('auth/cambiar_password', compact('mensaje'));
            return;
        }
        $mensaje = error_message($res['msg'] ?? 'Error al cambiar la contraseña');
        vista('auth/cambiar_password', compact('mensaje'));
    }

    private function redirigirPorTipo(): void {
        $tipo = $_SESSION['user_type'] ?? '';
        switch ($tipo) {
            case 'administrador':
                header('Location: /admin/dashboard');
                break;
            case 'arbitro':
                header('Location: /arbitro/dashboard');
                break;
            default:
                header('Location: /');
                break;
        }
        exit;
    }

    private function requerirLogin(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        if (!empty($_SESSION['password_temporal'])) {
            // Se permite el acceso únicamente a cambiar contraseña
            $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '';
            if ($uri !== '/auth/cambiar-password') {
                header('Location: /auth/cambiar-password');
                exit;
            }
        }
    }
}
