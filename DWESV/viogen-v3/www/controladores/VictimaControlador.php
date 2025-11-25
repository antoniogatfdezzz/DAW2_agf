<?php
require_once __DIR__ . '/../modelos/Victima.php';

/**
 * Controlador para la gestión de víctimas.
 *
 * Permite mostrar el formulario y realizar el alta validando el documento.
 */
class VictimaControlador { 
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
     * Muestra el formulario de registro de víctima.
     *
     * @param string $mensaje Mensaje informativo.
     * @param string $error   Mensaje de error.
     */
    public function index($mensaje = '', $error = '') {
        require_once $this->config['dir_vistas'] . 'VictimaFormulario.php';
        $vista = new VictimaVista($this->config, $mensaje, $error);
        $vista->mostrar();
    }

    /**
     * Procesa el envío del formulario de víctima.
     *
     * Valida documento y al menos nombre u observaciones.
     */
    public function guardar() {
        $nombre = strip_tags(trim($_POST['nombre'] ?? ''));
        $apellidos = strip_tags(trim($_POST['apellidos'] ?? ''));
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $documento = strip_tags(trim($_POST['documento'] ?? ''));
        $telefono = strip_tags(trim($_POST['telefono'] ?? ''));
        $observaciones = strip_tags(trim($_POST['observaciones'] ?? ''));

        if (empty($nombre) && empty($observaciones)) {
            $this->index('', 'Debe indicar al menos el Nombre o las Observaciones.');
            return;
        }

        if (!empty($documento)) {
            if (!$this->validarDocumento($tipo_documento, $documento)) {
                $this->index('', 'El documento introducido no es válido para el tipo seleccionado.');
                return;
            }
        }

        $datos = [
            'nombre' => !empty($nombre) ? $nombre : null,
            'apellidos' => !empty($apellidos) ? $apellidos : null,
            'tipo_documento' => !empty($tipo_documento) ? $tipo_documento : null,
            'documento' => !empty($documento) ? $documento : null,
            'telefono' => !empty($telefono) ? $telefono : null,
            'observaciones' => !empty($observaciones) ? $observaciones : null
        ];

        if (VictimaModelo::crear($datos)) {
            header('Location: index.php?controlador=inicio&metodo=index&mensaje=Víctima registrada correctamente');
        } else {
            $this->index('', 'Error al guardar la víctima.');
        }
    }

    /**
     * Valida un número de documento según su tipo.
     *
     * @param string $tipo   Tipo de documento (NIF|NIE|Pasaporte).
     * @param string $numero Número de documento.
     * @return bool true si el formato y letra son válidos.
     */
    private function validarDocumento($tipo, $numero) {
        $numero = strtoupper($numero);
        if ($tipo === 'NIF') {
            return preg_match('/^[0-9]{8}[A-Z]$/', $numero) && $this->letraNIF($numero);
        } elseif ($tipo === 'NIE') {
            return preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $numero) && $this->letraNIE($numero);
        } elseif ($tipo === 'Pasaporte') {
            return preg_match('/^[A-Z0-9]{6,9}$/', $numero);
        }
        return false;
    }

    /**
     * Comprueba la letra de un NIF.
     *
     * @param string $nif NIF completo.
     * @return bool true si la letra es correcta.
     */
    private function letraNIF($nif) {
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $numero = substr($nif, 0, 8);
        $letra = substr($nif, -1);
        return $letras[$numero % 23] === $letra;
    }

    /**
     * Comprueba la letra de un NIE.
     *
     * @param string $nie NIE completo.
     * @return bool true si la letra es correcta.
     */
    private function letraNIE($nie) {
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letraInicial = substr($nie, 0, 1);
        $numero = substr($nie, 1, 7);
        $letraFinal = substr($nie, -1);
        
        $prefijo = 0;
        if ($letraInicial == 'X') $prefijo = 0;
        elseif ($letraInicial == 'Y') $prefijo = 1;
        elseif ($letraInicial == 'Z') $prefijo = 2;

        $numeroCompleto = $prefijo . $numero;
        return $letras[$numeroCompleto % 23] === $letraFinal;
    }
}

