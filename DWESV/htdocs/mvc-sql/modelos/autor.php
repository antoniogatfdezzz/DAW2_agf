<?php
    require_once __DIR__ . '/../servicios/bd.php';
    /** Modelo
     * Responsabilidades:
     * - Representar los datos del negocio
     * - Gestionar la persistencia
     */

    class Autor{
        private $nombre;
        private $fecha_nacimiento;
        private $fecha_muerte;
        private $nacionalidad;
        private $base_datos;
        private $id;

        public static function listar(){
            $base_datos = new BD();
            $sql = "SELECT id, nombre, fecha_nacimiento, fecha_muerte, nacionalidad FROM autores";
            return $base_datos->consultar($sql);
        }

        public function __construct($nombre, $fecha_nacimiento, $fecha_muerte, $nacionalidad){
            $this->nombre = $nombre;
            $this->fecha_nacimiento = $fecha_nacimiento;
            $this->fecha_muerte = $fecha_muerte;
            $this->nacionalidad = $nacionalidad;
            $this->base_datos = new BD();
        }

        public function __toString(){
            return $this->id;
        }


        public function guardar(){
            try{
                $sql = "INSERT INTO autores (nombre, fecha_nacimiento, fecha_muerte, nacionalidad) VALUES (?, ?, ?, ?)";
                $parametros = [$this->nombre, $this->fecha_nacimiento, $this->fecha_muerte, $this->nacionalidad];
                $this->id = $this->base_datos->insertar($sql, $parametros);
                return $this->id;
            }
            catch(Throwable $e){
                header("HTTP/1.1 500 Internal Server Error");
                echo "Codigo de error: 2";
                die();
            }
        }

        public function verDiv($autor){
            $html = "<div>";
            $html .= '<p><a href="controlador=ControladorAutor&metodo=conultar&id='.$autor->getId().'">';
            $html .= $autor.'</p>';
            $html .= "</div>";
        }
    }