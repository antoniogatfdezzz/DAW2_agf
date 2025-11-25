<?php
/**
 * Configuración principal de la aplicación Viogen.
 *
 * Contiene rutas internas y parámetros de conexión a la base de datos.
 * @return array<string,mixed> Array asociativo de configuración.
 */
return [
	'debug' => true,
	'dir_controladores' => __DIR__ . '/controladores/',
	'dir_vistas' => __DIR__ . '/vistas/',
	'dir_html' => __DIR__ . '/vistas/html/',
	'dir_modelos' => __DIR__ . '/modelos/',
	'bd_host' => 'localhost',
	'bd_nombre' => 'viogen',
	'bd_usuario' => 'uviogen',
	'bd_clave' => 'cviogen',
];
