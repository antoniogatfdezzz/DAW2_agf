<?php
/**
 * Front Controller de la aplicación.
 *
 * Responsable de:
 *  - Iniciar la sesión.
 *  - Cargar la configuración, modelos y controladores.
 *  - Verificar autenticación (excepto rutas públicas).
 *  - Delegar en el controlador adecuado según 'action'.
 *
 * Seguridad:
 *  - Restringe acceso a vistas protegidas devolviendo 401 si no hay sesión.
 *  - Evita acceso directo a ficheros del interior de MVC usando un único punto de entrada.
 *
 */

session_start();

require_once __DIR__ . '/config.php';

// Cargar modelos y controladores (simples, en ficheros separados)
require_once __DIR__ . '/modelos/Usuario.php';
require_once __DIR__ . '/modelos/Victima.php';
require_once __DIR__ . '/modelos/Agresion.php';
require_once __DIR__ . '/controladores/AutenticacionControlador.php';
require_once __DIR__ . '/controladores/VictimaControlador.php';
require_once __DIR__ . '/controladores/AgresionControlador.php';
require_once __DIR__ . '/controladores/MenuControlador.php';

// Rutas permitidas sin autenticación
$publicRoutes = ['iniciar_sesion', 'acceder', 'datos_sql', 'assets'];

$action = $_GET['action'] ?? 'menu';

// Middleware: si no está autenticado y la ruta no es pública, devolver 401
if ((!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) && !in_array($action, $publicRoutes, true)) {
	http_response_code(401);
	// Mostrar la vista de login con mensaje y terminar
	AutenticacionControlador::formulario('Acceso no autorizado. Por favor identifíquese.');
	exit;
}

// Enrutamiento simple
switch ($action) {
	case 'iniciar_sesion':
		AutenticacionControlador::formulario();
		break;
	case 'acceder':
		AutenticacionControlador::acceder();
		break;
	case 'cerrar_sesion':
		AutenticacionControlador::cerrarSesion();
		break;
	case 'victima_formulario':
		VictimaControlador::formulario();
		break;
	case 'victima_guardar':
		VictimaControlador::guardar();
		break;
	case 'agresion_formulario':
		AgresionControlador::formulario();
		break;
	case 'agresion_guardar':
		AgresionControlador::guardar();
		break;
	case 'menu':
	default:
		// Menú principal (con buscador de agresiones integrado)
		MenuControlador::inicio();
		break;
}

