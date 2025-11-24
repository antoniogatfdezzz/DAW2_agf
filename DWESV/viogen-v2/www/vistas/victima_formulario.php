<?php
/**
 * Vista PHP: Formulario de registro de víctima.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/html/victima_formulario.html';
