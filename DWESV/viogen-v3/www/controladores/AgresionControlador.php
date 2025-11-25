<?php
require_once __DIR__ . '/../modelos/Agresion.php';
require_once __DIR__ . '/../modelos/Victima.php';

/**
 * Controlador para el registro de agresiones.
 *
 * Muestra el formulario y gestiona la persistencia de los datos enviados.
 */
class AgresionControlador {
    /**
     * Configuración global de la aplicación (rutas, parámetros BD, etc.).
     * @var array
     */
    private $config;

    /**
     * Constructor del controlador.
     *
     * @param array $config Array de configuración.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Página principal del formulario de agresiones.
     *
     * Recupera las víctimas para el desplegable y renderiza la vista.
     * @param string $mensaje Mensaje informativo (opcional).
     * @param string $error   Mensaje de error (opcional).
     */
    public function index($mensaje = '', $error = '') {
        $victimas = VictimaModelo::obtenerTodas();
        require_once $this->config['dir_vistas'] . 'AgresionFormulario.php';
        $vista = new AgresionVista($this->config, $victimas, $mensaje, $error);
        $vista->mostrar();
    }

    /**
     * Procesa el envío del formulario de agresión.
     *
     * Valida campos obligatorios y realiza inserción en base de datos.
     */
    public function guardar() {
        $id_victima = $_POST['id_victima'] ?? '';
        $agresor = strip_tags(trim($_POST['agresor'] ?? ''));
        $tipo_agresion = $_POST['tipo_agresion'] ?? '';
        $fecha_hora = $_POST['fecha_hora'] ?? '';
        $observaciones = strip_tags(trim($_POST['observaciones'] ?? ''));

        if (empty($id_victima) || empty($tipo_agresion) || empty($fecha_hora)) {
            $this->index('', 'Víctima, Tipo de Agresión y Fecha/Hora son obligatorios.');
            return;
        }

        $datos = [
            'id_victima' => $id_victima,
            'agresor' => !empty($agresor) ? $agresor : null,
            'tipo_agresion' => $tipo_agresion,
            'fecha_hora' => $fecha_hora,
            'observaciones' => !empty($observaciones) ? $observaciones : null
        ];

        if (AgresionModelo::crear($datos)) {
            header('Location: index.php?controlador=inicio&metodo=index&mensaje=Agresión registrada correctamente');
        } else {
            $this->index('', 'Error al guardar la agresión.');
        }
    }
}

