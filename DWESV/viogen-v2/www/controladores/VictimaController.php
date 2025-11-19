<?php
require_once __DIR__ . '/../modelos/Victima.php';

class VictimaController
{
    public static function form($errors = [])
    {
        $pdo = get_db();
        require __DIR__ . '/../vistas/victima_form.php';
    }

    public static function save()
    {
        $data = [];
        // Sanitizar entradas
        $data['nombre'] = trim($_POST['nombre'] ?? '');
        $data['apellidos'] = trim($_POST['apellidos'] ?? '');
        $data['tipo_documento'] = $_POST['tipo_documento'] ?? null;
        $data['documento'] = trim($_POST['documento'] ?? '');
        $data['telefono'] = trim($_POST['telefono'] ?? '');
        $data['observaciones'] = trim($_POST['observaciones'] ?? '');

        // Regla: todos opcionales pero al menos nombre o observaciones
        if ($data['nombre'] === '' && $data['observaciones'] === '') {
            $_SESSION['flash'] = 'Debe introducir al menos un nombre o observaciones.';
            self::form();
            return;
        }

        // Validación tipo_documento
        $validTipos = ['NIF', 'NIE', 'Pasaporte', null, ''];
        if (!in_array($data['tipo_documento'], $validTipos, true)) {
            $_SESSION['flash'] = 'Tipo de documento no válido.';
            self::form();
            return;
        }

        $pdo = get_db();
        Victima::create($pdo, $data);
        $_SESSION['flash'] = 'Victima registrada correctamente.';
        header('Location: index.php?action=menu');
        exit;
    }
}
