<?php
/**
 * PROCESAR LOGIN
 * Maneja la autenticación de policías y víctimas
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modelos/policias.php';
require_once __DIR__ . '/modelos/victimas.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vistas/login.html?error=Método no permitido');
    exit;
}

// Obtener datos del formulario
$tipo_usuario = $_POST['tipo_usuario'] ?? '';
$usuario = sanitizar($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

// Validar campos
if (empty($usuario) || empty($password)) {
    header('Location: vistas/login.html?error=Usuario y contraseña son obligatorios');
    exit;
}

// Autenticar según tipo de usuario
if ($tipo_usuario === 'policia') {
    // Autenticar policía
    $resultado = validarCredencialesPolicia($usuario, $password);
    
    if (isset($resultado['error'])) {
        header('Location: vistas/login.html?error=' . urlencode($resultado['error']));
        exit;
    }
    
    // Iniciar sesión
    $_SESSION['usuario_id'] = $resultado['id'];
    $_SESSION['nombre'] = $resultado['nombre'];
    $_SESSION['apellidos'] = $resultado['apellidos'];
    $_SESSION['rol'] = $resultado['rol'];
    $_SESSION['unidad_policial'] = $resultado['unidad_policial'];
    $_SESSION['usuario'] = $resultado['usuario'];
    
    // Redirigir al dashboard
    header('Location: vistas/dashboard_policia.php');
    exit;
    
} elseif ($tipo_usuario === 'victima') {
    // Autenticar víctima
    $resultado = validarCredencialesVictima($usuario, $password);
    
    if (isset($resultado['error'])) {
        header('Location: vistas/login.html?error=' . urlencode($resultado['error']));
        exit;
    }
    
    // Iniciar sesión
    $_SESSION['usuario_id'] = $resultado['id'];
    $_SESSION['nombre'] = $resultado['nombre'];
    $_SESSION['apellidos'] = $resultado['apellidos'];
    $_SESSION['rol'] = $resultado['rol'];
    $_SESSION['usuario'] = $resultado['usuario'];
    
    // Redirigir al panel de víctima
    header('Location: vistas/dashboard_victima.php');
    exit;
    
} else {
    header('Location: vistas/login.html?error=Tipo de usuario no válido');
    exit;
}
?>
