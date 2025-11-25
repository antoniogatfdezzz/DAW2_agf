<?php
class InicioVista {
    private $config;
    private $resultados;
    private $busqueda;
    private $mensaje;

    public function __construct($config, $resultados, $busqueda, $mensaje) {
        $this->config = $config;
        $this->resultados = $resultados;
        $this->busqueda = $busqueda;
        $this->mensaje = $mensaje;
    }

    public function mostrar() {
        $results = $this->resultados;
        $currentQuery = $this->busqueda;
        $flash = $this->mensaje;

        include $this->config['dir_html'] . 'menu.html';
    }
}

