<?php
/**
 * Vista PHP: Menú principal.
 *
 * Presenta las opciones disponibles tras autenticación y permite realizar búsquedas
 * sobre agresiones. Recibe $results y otros datos preparados por el controlador.
 */
// Iniciar sesión sólo si no hay ninguna sesión activa para evitar el notice
// "Ignoring session_start() because a session is already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
// Asegurar que existan variables esperadas por la vista HTML
$results = $results ?? [];
// Mantener el término de búsqueda actual (si viene por GET)
$currentQuery = trim($_GET['q'] ?? '');
require __DIR__ . '/html/menu.html';
