<?php
    /**
     * controlador1.php
     *  Responsabilidades:
     *   - Recibir los datos de usuario
     *   - Aplicar reglas de negocio
     *   - Navegación entre vistas
     */

    class Controlador1 {
        private $config;

        public function __construct($config){
            $this->config = $config;
        }
        public function metodo1() {
            echo "Ejecutando metodo1 de Controlador1<br>";
        }
        public function verVista1() {
            require_once($this->config['dir_vistas'].'vista.html');
        }
        public function guardar(){

            try{
                $texto = $_POST['campo1'];
                require_once($this->config['dir_modelos'].'modelo1.php');
                $modelo = new Modelo1();
                $modelo->guardar($texto);
                $resultado = "Bien";
            }catch (Throwable $e){
                $resultado = "Todo mal";
            }
            
        }
    }