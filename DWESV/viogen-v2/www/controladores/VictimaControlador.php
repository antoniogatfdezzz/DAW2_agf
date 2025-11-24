<?php
require_once __DIR__ . '/../modelos/Victima.php';

/**
 * Controlador de gestión de víctimas.
 * @package Controladores
 */
class VictimaControlador
{
    /**
     * Muestra el formulario de alta.
     * @param array $errores Errores (no usado)
     * @return void
     */
    public static function formulario($errores = [])
    {
        $pdo = get_db();
        require __DIR__ . '/../vistas/victima_formulario.php';
    }

    /**
     * Valida y guarda la víctima enviada por POST.
     * @return void
     */
    public static function guardar()
    {
        // Datos de entrada
        $data = [];
        $data['nombre'] = sanitize_nullable($_POST['nombre'] ?? null, 100);
        $data['apellidos'] = sanitize_nullable($_POST['apellidos'] ?? null, 150);
        $data['tipo_documento'] = $_POST['tipo_documento'] ?? null; // validación específica abajo
        $data['documento'] = sanitize_nullable($_POST['documento'] ?? null, 30);
        $data['telefono'] = sanitize_nullable($_POST['telefono'] ?? null, 30);
        $data['observaciones'] = sanitize_nullable($_POST['observaciones'] ?? null, 500);

        if (($data['nombre'] === null || $data['nombre'] === '') && ($data['observaciones'] === null || $data['observaciones'] === '')) {
            flash('Debe introducir al menos un nombre o observaciones.');
            self::formulario();
            return;
        }

        if (!in_array($data['tipo_documento'], TIPOS_DOCUMENTO, true)) {
            flash('Tipo de documento no válido.');
            self::formulario();
            return;
        }

        $pdo = get_db();
        Victima::crear($pdo, $data);
        flash('Víctima registrada correctamente.');
        redirectTo('menu');
    }
}
