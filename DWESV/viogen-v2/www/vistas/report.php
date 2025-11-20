<?php
// Informe / Buscador de agresiones
// Iniciar sesión sólo si no hay ninguna sesión activa para evitar el notice
// "Ignoring session_start() because a session is already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/html/report.html';
