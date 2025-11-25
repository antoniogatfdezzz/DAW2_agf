<?php
class LoginVista {
    private $error;

    private $config;

    public function __construct($config, $error = '') {
        $this->config = $config;
        $this->error = $error;
    }

    public function mostrar() {
        $flash = $this->error;
        include $this->config['dir_html'] . 'acceso.html';
    }
}

