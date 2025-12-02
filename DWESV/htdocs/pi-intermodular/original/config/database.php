<?php
require_once __DIR__ . '/config.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Configuración específica para conexiones remotas
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_TIMEOUT => 30, // Timeout de 30 segundos para conexiones remotas
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Para SSL si es necesario
            ];
            
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            // Log del error para debugging
            error_log("Error de conexión a base de datos: " . $exception->getMessage());
            
            if (defined('APP_DEBUG') && APP_DEBUG) {
                echo "Error de conexión: " . $exception->getMessage();
            } else {
                echo "Error de conexión a la base de datos. Por favor, contacte al administrador.";
            }
        }
        
        return $this->conn;
    }
}
