<?php
/**
 * Vista PHP: Formulario de registro de agresión.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/html/agresion_formulario.html';
