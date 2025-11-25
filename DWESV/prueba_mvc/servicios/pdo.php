<?php
/**
	Para configurar el acceso a SQL en XAMPP: Edita el fichero xampp/php/php.ini, descomenta la línea ;extension=sqlite3 (quitando el ;), salva el fichero y reinicia el servidor de XAMPP.
	Además, asegúrate de que hay permisos de escritura en el directorio de la base de datos para todos los usuarios y que hay permisos de escritura sobre el fichero de base de datos para cualquier usuario.
**/

	class PDO_SQLite{
		private $config;
		private $conexion;


		public function __construct($path_bd){
			try {
			$config = require_once('config.php');
			$db = $config['path_bd'];
            $stringConexion = "sqlite:$db";

            $this->conexion = new PDO($stringConexion);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            if (isset($config['debug']) && $config['debug'])
                echo "Error en pdo.php: ". $exception;
            die();
        }
		}

		public function listar(string $tabla, array $campos){

		}

		public function insertar(string $tabla, array $campos, array $valores){

		}

	}
