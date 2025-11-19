<?php
// Configuración principal de la aplicación
// Constantes de conexión a la base de datos
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'viogen');
define('DB_USER', 'uviogen');
define('DB_PASS', 'cviogen');

// Tiempo de sesión (opcional)
ini_set('session.cookie_httponly', 1);

/**
 * Obtiene una conexión PDO configurada
 * @return PDO
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
			// En producción no mostrar detalles
			http_response_code(500);
			echo 'Error de conexión a la base de datos.';
			exit;
		}
	}
	return $pdo;
}
