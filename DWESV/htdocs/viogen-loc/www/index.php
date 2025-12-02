<?php
// Punto de entrada único (Front Controller)
session_start();

require_once __DIR__ . '/config.php';

// Cargar modelos y controladores (simples, en ficheros separados)
require_once __DIR__ . '/modelos/User.php';
require_once __DIR__ . '/modelos/Victima.php';
require_once __DIR__ . '/modelos/Agresion.php';
require_once __DIR__ . '/controladores/AuthController.php';
require_once __DIR__ . '/controladores/VictimaController.php';
require_once __DIR__ . '/controladores/AgresionController.php';
require_once __DIR__ . '/controladores/ReportController.php';

// Rutas permitidas sin autenticación
$publicRoutes = ['login', 'do_login', 'datos_sql', 'assets'];

$action = $_GET['action'] ?? 'menu';

// Middleware: si no está autenticado y la ruta no es pública, devolver 401
if ((!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) && !in_array($action, $publicRoutes, true)) {
	http_response_code(401);
	// Mostrar la vista de login con mensaje y terminar
	AuthController::loginForm('Acceso no autorizado. Por favor identifíquese.');
	exit;
}

// Enrutamiento simple
switch ($action) {
	case 'login':
		AuthController::loginForm();
		break;
	case 'do_login':
		AuthController::doLogin();
		break;
	case 'logout':
		AuthController::logout();
		break;
	case 'victim_form':
		VictimaController::form();
		break;
	case 'victim_save':
		VictimaController::save();
		break;
	case 'agresion_form':
		AgresionController::form();
		break;
	case 'agresion_save':
		AgresionController::save();
		break;
	case 'report':
		ReportController::report();
		break;
	case 'menu':
	default:
		// Menú principal
		require __DIR__ . '/vistas/menu.php';
		break;
}

