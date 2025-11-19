<?php
require_once __DIR__ . '/../modelos/Agresion.php';

class ReportController
{
    public static function report()
    {
        $pdo = get_db();
        $term = trim($_GET['q'] ?? '');
        $results = [];
        if ($term !== '') {
            $results = Agresion::search($pdo, $term);
        }
        require __DIR__ . '/../vistas/report.php';
    }
}
