<?php
require_once __DIR__ . '/../modelos/Agresion.php';
require_once __DIR__ . '/../modelos/Victima.php';

/**
 * Controlador para registrar agresiones.
 * @package Controladores
 */
class AgresionControlador
{
    /**
     * Muestra el formulario de nueva agresión.
     * @param array $errores Errores de validación (sin uso actual)
     * @return void
     */
    public static function formulario($errores = [])
    {
        $pdo = get_db();
        $victimas = Victima::todas($pdo);
        require __DIR__ . '/../vistas/agresion_formulario.php';
    }

    /**
     * Valida y guarda la agresión enviada por POST.
     * @return void
     */
    public static function guardar()
    {
        // Datos de entrada
        $data = [];
        $data['id_victima'] = intval($_POST['id_victima'] ?? 0);
        $data['agresor'] = sanitize_nullable($_POST['agresor'] ?? null, 100);
        $data['tipo_agresion'] = sanitize_text($_POST['tipo_agresion'] ?? '', 20);
        $data['fecha_hora'] = sanitize_text($_POST['fecha_hora'] ?? '', 25);
        $data['observaciones'] = sanitize_nullable($_POST['observaciones'] ?? null, 500);

        if ($data['id_victima'] <= 0) {
            $_SESSION['flash'] = 'Debe seleccionar una víctima.';
            self::formulario();
            return;
        }
        if (!in_array($data['tipo_agresion'], TIPOS_AGRESION, true)) {
            flash('Tipo de agresión no válido.');
            self::formulario();
            return;
        }
        if ($data['fecha_hora'] === '') {
            flash('Debe indicar fecha y hora.');
            self::formulario();
            return;
        }

        $pdo = get_db();
        Agresion::crear($pdo, $data);
        flash('Agresión registrada correctamente.');
        redirectTo('menu');
    }
}
