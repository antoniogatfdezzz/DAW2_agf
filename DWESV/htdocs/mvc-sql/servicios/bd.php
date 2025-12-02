<?php 

    class BD{
        private $conexion;
        
        public function __construct(){
            try {
                $config = require __DIR__ . '/../config.php';
                $host = $config['bd_host'];
                $nombre = $config['bd_nombre'];
                $usuario = $config['bd_usuario'];
                $clave = $config['bd_clave'];
                $stringConexion = "mysql:host=$host;dbname=$nombre;charset=utf8mb4";

                $this->conexion = new PDO($stringConexion, $usuario, $clave);
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch (Exception $e) {
                header("HTTP/1.1 500 Internal Server Error");
                if ($config['debug']) {
                    echo 'Error de conexión a la base de datos: ' . $e->getMessage();
                    echo 'Error code: ' . $e->getCode();
                }
            }
        }

        public function conectar(){
            try {
                $this->conexion = new PDO("mysql:host=localhost;dbname=test", "usuario", "password");
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        public function insertar($sql, $parametros = []){
            try {
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute($parametros);
                return (int)$this->conexion->lastInsertId();
            } catch (Exception $e) {
                header("HTTP/1.1 500 Internal Server Error");
                echo "Codigo de error: BD-INSERT";
                if (isset($config) && !empty($config['debug'])) {
                    echo ' Detalle: ' . $e->getMessage();
                }
                die();
            }
        }

        public function consultar($sql, $parametros = []){
            try {
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute($parametros);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                header("HTTP/1.1 500 Internal Server Error");
                echo "Codigo de error: BD-SELECT";
                // Intentar obtener configuración para modo debug
                $config = @require __DIR__ . '/../config.php';
                if (!empty($config['debug'])) {
                    echo ' Detalle: ' . $e->getMessage();
                }
                die();
            }
        }
    }