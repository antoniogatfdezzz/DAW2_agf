<?php
/**
 * index.php
 *  Responsabilisades:
 *   - Cargar la configuración
 *   - Middleware
 *   - Procesar la petición
 */

    try{
        $config = require_once 'config.php';
        if (!empty($config['debug'])) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        // Router simple
        $controlador = $_GET['controlador'] ?? 'ControladorAutor';
        $metodo = $_GET['metodo'] ?? 'verAlta' ?? 'listar';

        require_once $config['dir_controladores'] . strtolower($controlador) . '.php';
        $controlador = new $controlador($config);
        $controlador->$metodo();
    } catch (Throwable $e) {
        header("HTTP/1.1 500 Internal Server Error");
        if (!empty($config['debug'])) {
            echo $e->getMessage();
        }
    }