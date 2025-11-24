<?php
require_once __DIR__ . '/../modelos/Agresion.php';

/**
 * Controlador del menú principal.
 * @package Controladores
 */
class MenuControlador
{
    /**
     * Muestra la vista del menú y resultados opcionales.
     * @return void
     */
    public static function inicio()
    {
        $pdo = get_db();
        $term = sanitize_text($_GET['q'] ?? '', 100);
        $results = [];
        if ($term !== '') {
            $results = Agresion::buscar($pdo, $term);
        }
        require __DIR__ . '/../vistas/menu.php';
    }
}
