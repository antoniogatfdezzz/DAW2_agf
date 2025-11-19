<?php
require_once __DIR__ . '/../modelos/Agresion.php';
require_once __DIR__ . '/../modelos/Victima.php';

class AgresionController
{
    public static function form($errors = [])
    {
        $pdo = get_db();
        $victimas = Victima::all($pdo);
        require __DIR__ . '/../vistas/agresion_form.php';
    }

    public static function save()
    {
        $data = [];
        $data['id_victima'] = intval($_POST['id_victima'] ?? 0);
        $data['agresor'] = trim($_POST['agresor'] ?? '');
        $data['tipo_agresion'] = $_POST['tipo_agresion'] ?? '';
        $data['fecha_hora'] = trim($_POST['fecha_hora'] ?? '');
        $data['observaciones'] = trim($_POST['observaciones'] ?? '');

        // Validaciones
        if ($data['id_victima'] <= 0) {
            $_SESSION['flash'] = 'Debe seleccionar una víctima.';
            self::form();
            return;
        }
        $tipos = ['física', 'psicológica', 'sexual', 'vicaria'];
        if (!in_array($data['tipo_agresion'], $tipos, true)) {
            $_SESSION['flash'] = 'Tipo de agresión no válido.';
            self::form();
            return;
        }
        if ($data['fecha_hora'] === '') {
            $_SESSION['flash'] = 'Debe indicar fecha y hora.';
            self::form();
            return;
        }

        $pdo = get_db();
        Agresion::create($pdo, $data);
        $_SESSION['flash'] = 'Agresión registrada correctamente.';
        header('Location: index.php?action=menu');
        exit;
    }
}
