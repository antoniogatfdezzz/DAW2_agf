<?php $usuario = $_SESSION['user_name'] ?? 'Admin'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><i class="fas fa-user-shield"></i> Administración</div>
            <div class="user-info">
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <div><?="Hola, " . htmlspecialchars($usuario)?></div>
                <div class="user-actions"><a class="btn btn-secondary" href="/auth/salir">Salir</a></div>
            </div>
        </div>
    </header>
    <main class="container main-content">
        <div class="content-header">
            <h1>Dashboard</h1>
            <div class="breadcrumb">Inicio / Administración</div>
        </div>
        <div class="card">
            <div class="card-header">Resumen</div>
            <div class="card-body">
                <p>Bienvenido al panel de administración. Selecciona una sección del sistema:</p>
                <div class="d-flex gap-3">
                    <a class="btn btn-primary" href="/admin/usuarios"><i class="fas fa-users"></i> Usuarios</a>
                    <a class="btn btn-primary" href="/admin/arbitros"><i class="fas fa-whistle"></i> Árbitros</a>
                    <a class="btn btn-primary" href="/admin/partidos"><i class="fas fa-volleyball"></i> Partidos</a>
                    <a class="btn btn-primary" href="/admin/licencias"><i class="fas fa-id-card"></i> Licencias</a>
                    <a class="btn btn-primary" href="/admin/liquidaciones"><i class="fas fa-file-invoice"></i> Liquidaciones</a>
                </div>
            </div>
        </div>
    </main>
    <script src="/js/app.js"></script>
    <script src="/js/search-bar.js"></script>
    <script src="/js/ajax.js"></script>
 </body>
</html>
