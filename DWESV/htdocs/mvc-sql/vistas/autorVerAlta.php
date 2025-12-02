<?php
    class AutorVerAlta{
        private $config;

        public function __construct($config){
            $this->config = $config;
        }
        public function mostrar($mensaje){
            require_once($this->config['dir_html'].'autor_ver_alta.html');
            die();
        }
    }