<?php
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * Controlador de autenticación de usuarios.
 *
 * Gestiona el acceso, validación de credenciales y cierre de sesión.
 */
class AutenticacionControlador {
    /**
     * Configuración global (rutas de vistas, etc.).
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config Configuración de la aplicación.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Muestra la vista de acceso (login).
     *
     * @param string $error Mensaje de error opcional a mostrar.
     */
    public function index($error = '') {
        require_once $this->config['dir_vistas'] . 'Acceso.php';
        $vista = new LoginVista($this->config, $error);
        $vista->mostrar();
    }

    /**
     * Procesa la autenticación del usuario.
     *
     * Valida longitud mínima de usuario y clave, luego delega en el modelo.
     */
    public function autenticar() {
        $nombre = strip_tags(trim($_POST['nombre'] ?? ''));
        $clave = $_POST['clave'] ?? '';

        if (strlen($nombre) < 4 || strlen($clave) < 4) {
            $this->index('Usuario y clave deben tener al menos 4 caracteres.');
            return;
        }

        $usuario = Usuario::autenticar($nombre, $clave);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['user_nombre'] = $usuario['nombre'];
            header('Location: index.php?controlador=inicio&metodo=index');
            exit();
        } else {
            $this->index('Credenciales incorrectas.');
        }
    }

    /**
     * Cierra la sesión y redirige al índice.
     */
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit();
    }
}

