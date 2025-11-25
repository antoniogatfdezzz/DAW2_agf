<?php

try {
	
	$config = require_once(__DIR__ . '/config.php');
	if ($config['debug']) {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	} else {
		ini_set('display_errors', 0);
		ini_set('display_startup_errors', 0);
		error_reporting(0);
	}


	

	session_start();
	
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		
		switch ($action) {
			case 'iniciar_sesion':
				$controlador = 'AutenticacionControlador'; $metodo = 'index'; break;
			case 'acceder':
				$controlador = 'AutenticacionControlador'; $metodo = 'autenticar'; break;
			case 'cerrar_sesion':
				$controlador = 'AutenticacionControlador'; $metodo = 'logout'; break;
			case 'victima_formulario':
				$controlador = 'VictimaControlador'; $metodo = 'index'; break;
			case 'victima_guardar':
				$controlador = 'VictimaControlador'; $metodo = 'guardar'; break;
			case 'agresion_formulario':
				$controlador = 'AgresionControlador'; $metodo = 'index'; break;
			case 'agresion_guardar':
				$controlador = 'AgresionControlador'; $metodo = 'guardar'; break;
			case 'menu':
			default:
				$controlador = 'MenuControlador'; $metodo = 'index'; break;
		}
		
		if (isset($_GET['q']) && !isset($_POST['busqueda'])) {
			$_POST['busqueda'] = $_GET['q'];
		}
	} else {
		
		$controlador = $_GET['controlador'] ?? 'AutenticacionControlador';
		$metodo = $_GET['metodo'] ?? 'index';
	}

	if (!isset($_SESSION['usuario_id'])) {
		
		if (strtolower($controlador) !== 'autenticacioncontrolador') {
			
			require_once($config['dir_controladores'] . 'AutenticacionControlador.php');
			$controladorObj = new AutenticacionControlador($config);
			$controladorObj->index('Debe iniciar sesión para acceder.');
			exit();
		}
	}
    

	
	if (strtolower($controlador) === 'inicio') {
		$controlador = 'MenuControlador';
		
		$metodo = $metodo ?? 'index';
	}

	$archivoControlador = $config['dir_controladores'] . strtolower($controlador) . '.php';
	if (file_exists($archivoControlador)) {
		require_once($archivoControlador);
		<?php
		/**
		 * Front Controller de la aplicación Viogen.
		 *
		 * Resuelve la acción solicitada, carga el controlador correspondiente y ejecuta el método indicado.
		 * Gestiona también el control de acceso basado en sesión.
		 */
		try {
			$config = require_once(__DIR__ . '/config.php');
			// Configuración de errores según modo debug.
			if ($config['debug']) {
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
			} else {
				ini_set('display_errors', 0);
				ini_set('display_startup_errors', 0);
				error_reporting(0);
			}

			session_start();

			// Resolución de acción simplificada.
			if (isset($_GET['action'])) {
				$action = $_GET['action'];
				switch ($action) {
					case 'iniciar_sesion':
						$controlador = 'AutenticacionControlador'; $metodo = 'index'; break;
					case 'acceder':
						$controlador = 'AutenticacionControlador'; $metodo = 'autenticar'; break;
					case 'cerrar_sesion':
						$controlador = 'AutenticacionControlador'; $metodo = 'logout'; break;
					case 'victima_formulario':
						$controlador = 'VictimaControlador'; $metodo = 'index'; break;
					case 'victima_guardar':
						$controlador = 'VictimaControlador'; $metodo = 'guardar'; break;
					case 'agresion_formulario':
						$controlador = 'AgresionControlador'; $metodo = 'index'; break;
					case 'agresion_guardar':
						$controlador = 'AgresionControlador'; $metodo = 'guardar'; break;
					case 'menu':
					default:
						$controlador = 'MenuControlador'; $metodo = 'index'; break;
				}
				if (isset($_GET['q']) && !isset($_POST['busqueda'])) {
					$_POST['busqueda'] = $_GET['q'];
				}
			} else {
				$controlador = $_GET['controlador'] ?? 'AutenticacionControlador';
				$metodo = $_GET['metodo'] ?? 'index';
			}

			// Validación de sesión para acceso restringido.
			if (!isset($_SESSION['usuario_id'])) {
				if (strtolower($controlador) !== 'autenticacioncontrolador') {
					require_once($config['dir_controladores'] . 'AutenticacionControlador.php');
					$controladorObj = new AutenticacionControlador($config);
					$controladorObj->index('Debe iniciar sesión para acceder.');
					exit();
				}
			}

			// Alias 'inicio' -> MenuControlador.
			if (strtolower($controlador) === 'inicio') {
				$controlador = 'MenuControlador';
				$metodo = $metodo ?? 'index';
			}

			$archivoControlador = $config['dir_controladores'] . strtolower($controlador) . '.php';
			if (file_exists($archivoControlador)) {
				require_once($archivoControlador);
				$clase = ucfirst($controlador);
				if (class_exists($clase)) {
					$controladorObj = new $clase($config);
					if (method_exists($controladorObj, $metodo)) {
						$controladorObj->$metodo();
					} else {
						throw new Exception("Método '$metodo' no encontrado en el controlador '$clase'.");
					}
				} else {
					throw new Exception("Clase '$clase' no encontrada.");
				}
			} else {
				throw new Exception("Controlador '$controlador' no encontrado.");
			}
		} catch (Throwable $exception) {
			header('HTTP/2 500 Internal Server Error');
			if (!empty($config['debug'])) {
				echo "Error en index.php: " . $exception;
			}
		}


