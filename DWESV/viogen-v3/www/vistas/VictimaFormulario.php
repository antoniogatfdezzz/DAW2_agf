<?php
class VictimaVista {
    private $config;
    private $mensaje;
    private $error;

    public function __construct($config, $mensaje = '', $error = '') {
        $this->config = $config;
        $this->mensaje = $mensaje;
        $this->error = $error;
    }

    public function mostrar() {
        $flash = !empty($this->mensaje) ? $this->mensaje : $this->error;
        include $this->config['dir_html'] . 'victima_formulario.html';
    }
}

