<?php

try{
	$config = require_once('config.php');

	//Configuración inicial
	if ($config['debug']){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}
	else{
		ini_set('display_errors', 0);
		ini_set('display_startup_errors', 0);
		error_reporting(0);
	}

	//Identifica el controlador y el método pedidos creando las variables $controlador y $metodo
	$controlador = $_GET['controlador'] ?? $_POST['controlador'] ?? 'ControladorCalificacion';				
	$metodo = $_GET['metodo'] ?? $_POST['metodo'] ?? 'listar'; 

	//Creación de controlador
	require_once($config['path_controladores'].strtolower($controlador).'.php');
	$controlador = new $controlador($config);
	$controlador->$metodo();

}catch(Throwable $excepcion){
	echo $excepcion;
	header('HTTP/2 500 Internal Server Error');
}

