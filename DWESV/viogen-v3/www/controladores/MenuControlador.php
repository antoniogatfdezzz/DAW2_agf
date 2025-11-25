<?php
require_once __DIR__ . '/../modelos/Agresion.php';

/**
 * Controlador del menú/inicio.
 *
 * Gestiona la pantalla inicial y la búsqueda de agresiones.
 */
class MenuControlador {
    /**
     * Configuración global de la aplicación.
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config Configuración de la aplicación.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Acción principal: muestra el menú y resultados de búsqueda.
     */
    public function index() {
        $mensaje = $_GET['mensaje'] ?? '';
        $resultados = [];
        $busqueda = $_POST['busqueda'] ?? '';

        if (!empty($busqueda)) {
            $resultados = AgresionModelo::buscar($busqueda);
        }

        require_once $this->config['dir_vistas'] . 'Menu.php';
        $vista = new InicioVista($this->config, $resultados, $busqueda, $mensaje);
        $vista->mostrar();
    }
}

