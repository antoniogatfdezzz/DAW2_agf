<?php
/**
 * Clase de acceso a datos (envoltorio de PDO).
 *
 * Encapsula la apertura de la conexión a la base de datos utilizando los parámetros definidos en `config.php`.
 * Permite obtener una instancia reutilizable de PDO para ejecutar consultas/preparar sentencias.
 */
class BD {
    /** @var PDO Conexion PDO activa */
    private $conexion;

    /**
     * Constructor.
     *
     * Intenta establecer la conexión a la base de datos. En caso de fallo, muestra mensaje (si debug habilitado) y detiene ejecución.
     */
    public function __construct() {
        try {
            $config = require(__DIR__ . '/../config.php');
            $host = $config['bd_host'];
            $nombre = $config['bd_nombre'];
            $usuario = $config['bd_usuario'];
            $clave = $config['bd_clave'];
            $stringConexion = "mysql:host=$host;dbname=$nombre;charset=utf8mb4";

            $this->conexion = new PDO($stringConexion, $usuario, $clave);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            if (isset($config['debug']) && $config['debug'])
                echo "Error en modelos/bd.php: ". $exception;
            die();
        }
    }

    /**
     * Devuelve la conexión PDO activa.
     *
     * @return PDO Instancia de conexión.
     */
    public function obtenerConexion() {
        return $this->conexion;
    }
}
