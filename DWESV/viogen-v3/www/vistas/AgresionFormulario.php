<?php
class AgresionVista {
    private $config;
    private $victimas;
    private $mensaje;
    private $error;

    public function __construct($config, $victimas, $mensaje = '', $error = '') {
        $this->config = $config;
        $this->victimas = $victimas;
        $this->mensaje = $mensaje;
        $this->error = $error;
    }

    public function mostrar() {
        $victimas = $this->victimas;
        $flash = !empty($this->mensaje) ? $this->mensaje : $this->error;

        include $this->config['dir_html'] . 'agresion_formulario.html';
    }
}

