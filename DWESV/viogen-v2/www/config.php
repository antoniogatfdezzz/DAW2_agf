<?php
/**
 * Fichero de configuración global de la aplicación.
 *
 * Define las constantes de conexión a la base de datos y expone la función
 * de acceso singleton get_db(). También aplica ajustes de sesión seguros.
 *
 * NOTA: Las credenciales deberían externalizarse (variables de entorno) en
 * una fase posterior para mejorar la seguridad y facilitar despliegues.
 */
// Constantes de conexión a la base de datos
/** Host de la base de datos */
define('DB_HOST', '127.0.0.1');
/** Nombre de la base de datos */
define('DB_NAME', 'viogen');
/** Usuario de conexión */
define('DB_USER', 'uviogen');
/** Contraseña del usuario de conexión */
define('DB_PASS', 'cviogen');

/** Tipos válidos de agresión permitidos en el sistema */
define('TIPOS_AGRESION', ['física', 'psicológica', 'sexual', 'vicaria']);
/** Tipos válidos de documento para víctima */
define('TIPOS_DOCUMENTO', ['NIF', 'NIE', 'Pasaporte', null, '']);

// Ajustes de sesión (opcional)
// Sólo aplicar cambios a las directivas de sesión si no hay una sesión activa.
// Evita warnings como "Session ini settings cannot be changed when a session is active".
if (session_status() === PHP_SESSION_NONE) {
	ini_set('session.cookie_httponly', 1);
}

/**
 * Obtiene una conexión PDO configurada (singleton).
 *
 * Opciones activadas:
 *  - ERRMODE_EXCEPTION para gestión de errores.
 *  - FETCH_ASSOC para resultados asociativos.
 *
 * @return PDO Conexión reutilizable.
 */
function get_db()
{
	static $pdo = null;
	if ($pdo === null) {
		$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];
		try {
			$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		} catch (PDOException $e) {
			http_response_code(500);
			echo 'Error de conexión a la base de datos.';
			exit;
		}
	}
	return $pdo;
}

/**
 * Registra un mensaje flash que será mostrado en la siguiente vista.
 *
 * @param string $mensaje Texto a mostrar.
 * @return void
 */
function flash(string $mensaje): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	$_SESSION['flash'] = $mensaje;
}

/**
 * Redirige a una acción del front controller de forma segura.
 * Evita repetir header() y exit en controladores.
 *
 * @param string $action Acción destino (sin validar aquí, responsabilidad del controlador).
 * @return void
 */
function redirectTo(string $action): void
{
	header('Location: index.php?action=' . urlencode($action));
	exit;
}

/**
 * Sanitiza texto de entrada (capa defensiva adicional a trim). Elimina etiquetas,
 * normaliza espacios y limita longitud para prevenir almacenamiento excesivo.
 *
 * @param string|null $valor Valor original.
 * @param int $maxLength Máximo de caracteres permitidos.
 * @return string Valor sanitizado (puede ser cadena vacía).
 */
function sanitize_text(?string $valor, int $maxLength = 255): string
{
	if ($valor === null) {
		return '';
	}
	$valor = trim($valor);
	// Eliminar etiquetas HTML y caracteres de control
	$valor = strip_tags($valor);
	$valor = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $valor);
	// Normalizar espacios múltiples
	$valor = preg_replace('/\s{2,}/u', ' ', $valor);
	if (mb_strlen($valor) > $maxLength) {
		$valor = mb_substr($valor, 0, $maxLength);
	}
	return $valor;
}

/**
 * Sanitiza texto que puede ser nulo; devuelve null si tras trim queda vacío.
 *
 * @param string|null $valor Valor original.
 * @param int $maxLength Máximo de caracteres.
 * @return string|null Texto sanitizado o null.
 */
function sanitize_nullable(?string $valor, int $maxLength = 255): ?string
{
	$san = sanitize_text($valor, $maxLength);
	return $san === '' ? null : $san;
}
