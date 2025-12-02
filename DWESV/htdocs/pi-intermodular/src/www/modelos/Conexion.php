<?php
class Conexion {
    private ?PDO $conn = null;

    public function obtenerConexion(): ?PDO {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }
        try {
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            return $this->conn;
        } catch (PDOException $e) {
            error_log('Error de conexión a base de datos: ' . $e->getMessage());
            if (APP_DEBUG) {
                echo 'Error de conexión a BD';
            }
            return null;
        }
    }
}
