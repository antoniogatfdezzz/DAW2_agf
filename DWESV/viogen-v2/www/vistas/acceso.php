<?php
/**
 * Vista PHP: Formulario de acceso (login).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/html/acceso.html';
