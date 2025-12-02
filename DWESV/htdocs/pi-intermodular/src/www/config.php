<?php
// Configuración global de la aplicación (MVC)

// Modo debug (mostrar mensajes de error controlados)
define('APP_DEBUG', true);

// Zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

// Configuración de base de datos MySQL
// Ajusta estas variables según tu entorno local (XAMPP)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'pi_intermodular');
define('DB_USER', 'root');
define('DB_PASS', '');

// Rutas base
define('APP_BASE_PATH', __DIR__);
define('APP_ROOT', dirname(__DIR__));
define('APP_SRC', APP_ROOT . '/src');
define('APP_WWW', __DIR__);
define('APP_CONTROLADORES', __DIR__ . '/controladores');
define('APP_MODELOS', __DIR__ . '/modelos');
define('APP_VISTAS', __DIR__ . '/vistas');
define('APP_ASSETS', __DIR__ . '/assets');

// Autocarga simple de clases de modelos y controladores
spl_autoload_register(function ($clase) {
	$rutas = [
		APP_MODELOS . '/' . $clase . '.php',
		APP_CONTROLADORES . '/' . $clase . '.php',
	];
	foreach ($rutas as $ruta) {
		if (file_exists($ruta)) {
			require_once $ruta;
			return;
		}
	}
});

// Manejo básico de errores (sin warnings en consola)
if (APP_DEBUG) {
	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
	ini_set('display_errors', '1');
} else {
	error_reporting(0);
	ini_set('display_errors', '0');
}

// Helper de respuesta JSON controlada
function respuesta_json($data = [], int $status = 200): void {
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

// Helper para renderizar vistas dentro de layout simple
function vista(string $rutaVista, array $datos = []): void {
	$archivo = APP_VISTAS . '/' . ltrim($rutaVista, '/');
	if (!str_ends_with($archivo, '.php')) {
		$archivo .= '.php';
	}
	if (!file_exists($archivo)) {
		http_response_code(404);
		echo 'Vista no encontrada: ' . htmlspecialchars($rutaVista);
		exit;
	}
	extract($datos, EXTR_SKIP);
	require $archivo;
}

