<?php
require __DIR__ . '/configuración/config.php';
if (auth_user()) {
    redirect('vistas/dashboard_policia.php');
}
redirect('login.php');
