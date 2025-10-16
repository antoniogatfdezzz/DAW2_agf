<?php
/**
 * CERRAR SESIÓN
 */

require_once __DIR__ . '/config.php';

// Registrar cierre de sesión en auditoría
if (estaAutenticado()) {
    registrarAuditoria(
        $_SESSION['usuario'] ?? 'DESCONOCIDO',
        'LOGOUT',
        'Cierre de sesión'
    );
}

// Cerrar sesión
cerrarSesion();

// Redirigir al login
header('Location: vistas/login.html?mensaje=Sesión cerrada correctamente');
exit;
?>
