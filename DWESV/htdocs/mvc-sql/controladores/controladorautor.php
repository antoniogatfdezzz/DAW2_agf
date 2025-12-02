<?php
    /**
     * controladorautor.php
     *  Responsabilidades:
     *   - Recibir los datos de usuario
     *   - Aplicar reglas de negocio
     *   - Navegación entre vistas
     */

    class ControladorAutor {
        private $config;

        public function __construct($config){
            $this->config = $config;
        }


        public function verAlta() {
            require_once($this->config['dir_vistas'].'autorVerAlta.php');
            $vista = new AutorVerAlta($this->config);
            $vista->mostrar($mensaje);
        }


        public function alta(){
            try{
                $nombre = $_POST['nombre'];
                $fecha_nacimiento = $_POST['fecha_nacimiento'];
                $fecha_muerte = $_POST['fecha_muerte'];
                $nacionalidad = $_POST['nacionalidad'];

                if ($fecha_muerte == '') $fecha_muerte = null;
                
                require_once($this->config['dir_modelos'].'autor.php');
                $autor = new Autor($nombre, $fecha_nacimiento, $fecha_muerte, $nacionalidad);
                $id = $autor->guardar();
                header("HTTP/1.1 201 Created");
                echo "Autor insertado con ID: $id";
                die();
            }catch(Throwable $e){
                header("HTTP/1.1 500 Internal Server Error");
                echo "Codigo de error: 1";
                die();
            }
        }


        public function listar(){
            require_once($this->config['dir_modelos'].'autor.php');
            require_once($this->config['dir_vistas'].'autorListar.php');
            $autores = Autor::listar();
            $vista = new AutorListar($this->config);
            $vista->mostrar($autores);
        }


        public function guardar(){

        }
    }