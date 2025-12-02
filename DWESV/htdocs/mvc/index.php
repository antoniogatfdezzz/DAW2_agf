<?php
/**
 * index.php
 *  Responsabilisades:
 *   - Cargar la configuración
 *   - Middleware
 *   - Procesar la petición
 */

    try{
        $config = require_once'config.php';
            if ($config['debug']){
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);
            }else{
                ini_set('display_errors', 0);
                ini_set('display_startup_errors', 0);
                error_reporting(0);
            }

            // Middeleware
            

            // Procesar la peticion
            $controlador = $_GET['controlador'];
            $metodo = $_GET['metodo'];

            require_once ($config['dir_controladores'].strtolower($controlador).'.php');
            $controlador = new $controlador();
            $controlador->$metodo();

            echo "<br>TRON:FIN</br>";
    } catch (throwable $e) {
        header("HTTP/1.1 500 Internal Server Error");
    }