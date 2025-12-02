<?php
// Front Controller (MVC)
require_once __DIR__ . '/config.php';

// Sesión
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Resolución de ruta desde REQUEST_URI
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '') ?: '';
if ($scriptName !== '/' && str_starts_with($requestUri, $scriptName)) {
	$requestUri = substr($requestUri, strlen($scriptName));
}
$requestUri = '/' . ltrim($requestUri, '/');

// Normalizar ruta base de la app (asumiendo src/www como docroot)
// Rutas de la app
// GET  /                      -> auth formulario
// POST /auth/iniciar          -> login
// GET  /auth/salir            -> logout
// GET  /auth/cambiar-password -> formulario cambiar
// POST /auth/cambiar-password -> procesar cambio
// GET  /unauthorized          -> no autorizado

$metodo = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Ruteo sencillo
try {
	switch (true) {
		// Página de inicio -> formulario login
		case $metodo === 'GET' && ($requestUri === '/' || $requestUri === '/index.php'):
			(new AutenticacionControlador())->formularioLogin();
			break;

		// Login
		case $metodo === 'POST' && $requestUri === '/auth/iniciar-sesion.php':
			(new AutenticacionControlador())->iniciarSesion();
			break;

		// Logout
		case $metodo === 'GET' && $requestUri === '/auth/salir':
			(new AutenticacionControlador())->cerrarSesion();
			break;

		// Cambiar contraseña - formulario
		case $metodo === 'GET' && $requestUri === '/auth/cambiar-password':
			(new AutenticacionControlador())->formularioCambiarPassword();
			break;

		// Cambiar contraseña - acción
		case $metodo === 'POST' && $requestUri === '/auth/cambiar-password':
			(new AutenticacionControlador())->cambiarPassword();
			break;

		// No autorizado
		case $metodo === 'GET' && $requestUri === '/unauthorized':
			vista('errores/no_autorizado');
			break;

		// Admin
		case $metodo === 'GET' && $requestUri === '/admin/dashboard':
			(new AdminControlador())->dashboard();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/usuarios':
			(new AdminControlador())->usuarios();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/arbitros':
			(new AdminControlador())->arbitros();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/partidos':
			(new AdminControlador())->partidos();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/licencias':
			(new AdminControlador())->licencias();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/liquidaciones':
			(new AdminControlador())->liquidaciones();
			break;
		case $metodo === 'GET' && $requestUri === '/admin/perfil':
			(new AdminControlador())->perfil();
			break;

		// Árbitro
		case $metodo === 'GET' && $requestUri === '/arbitro/dashboard':
			(new ArbitroControlador())->dashboard();
			break;
		case $metodo === 'GET' && $requestUri === '/arbitro/disponibilidad':
			(new ArbitroControlador())->disponibilidad();
			break;
		case $metodo === 'GET' && $requestUri === '/arbitro/liquidaciones':
			(new ArbitroControlador())->liquidaciones();
			break;
		case $metodo === 'GET' && $requestUri === '/arbitro/partidos':
			(new ArbitroControlador())->partidos();
			break;
		case $metodo === 'GET' && $requestUri === '/arbitro/perfil':
			(new ArbitroControlador())->perfil();
			break;

		// API Admin
		case $metodo === 'GET' && $requestUri === '/api/admin/usuarios':
			(new ApiAdminControlador())->usuarios();
			break;
		case $metodo === 'POST' && $requestUri === '/api/admin/usuarios':
			(new ApiAdminControlador())->crearUsuario();
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/usuarios/(\d+)/actualizar$#',$requestUri,$m):
			(new ApiAdminControlador())->actualizarUsuario((int)$m[1]);
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/usuarios/(\d+)/eliminar$#',$requestUri,$m):
			(new ApiAdminControlador())->eliminarUsuario((int)$m[1]);
			break;
		case $metodo === 'GET' && $requestUri === '/api/admin/arbitros':
			(new ApiAdminControlador())->arbitros();
			break;
		case $metodo === 'POST' && $requestUri === '/api/admin/arbitros':
			(new ApiAdminControlador())->crearArbitro();
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/arbitros/(\d+)/actualizar$#',$requestUri,$m):
			(new ApiAdminControlador())->actualizarArbitro((int)$m[1]);
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/arbitros/(\d+)/eliminar$#',$requestUri,$m):
			(new ApiAdminControlador())->eliminarArbitro((int)$m[1]);
			break;
		case $metodo === 'GET' && $requestUri === '/api/admin/partidos':
			(new ApiAdminControlador())->partidos();
			break;
		case $metodo === 'POST' && $requestUri === '/api/admin/partidos':
			(new ApiAdminControlador())->crearPartido();
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/partidos/(\d+)/actualizar$#',$requestUri,$m):
			(new ApiAdminControlador())->actualizarPartido((int)$m[1]);
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/partidos/(\d+)/eliminar$#',$requestUri,$m):
			(new ApiAdminControlador())->eliminarPartido((int)$m[1]);
			break;
		case $metodo === 'POST' && preg_match('#^/api/admin/partidos/(\\d+)/resultado$#',$requestUri,$m):
			(new ApiResultadosControlador())->guardar((int)$m[1]);
			break;

		// API Árbitro
		case $metodo === 'GET' && $requestUri === '/api/arbitro/partidos':
			(new ApiArbitroControlador())->misPartidos();
			break;

		// API Disponibilidad (admin)
		case in_array($metodo, ['GET','POST','DELETE'], true) && str_starts_with($requestUri, '/api/admin/disponibilidad'):
			(new ApiDisponibilidadControlador())->consultar();
			break;

		// API Alias de árbitros (admin)
		case in_array($metodo, ['GET','POST','DELETE'], true) && str_starts_with($requestUri, '/api/admin/arbitro-alias'):
			(new ApiArbitroAliasControlador())->manejar();
			break;

		// API Liquidaciones (admin)
		case in_array($metodo, ['GET','POST'], true) && str_starts_with($requestUri, '/api/admin/liquidaciones'):
			(new ApiLiquidacionesControlador())->manejar();
			break;

		default:
			http_response_code(404);
			echo 'Ruta no encontrada: ' . htmlspecialchars($requestUri);
			break;
	}
} catch (Throwable $e) {
	if (APP_DEBUG) {
		http_response_code(500);
		echo 'Error interno: ' . htmlspecialchars($e->getMessage());
	} else {
		http_response_code(500);
		echo 'Ha ocurrido un error. Inténtelo de nuevo más tarde.';
	}
}

