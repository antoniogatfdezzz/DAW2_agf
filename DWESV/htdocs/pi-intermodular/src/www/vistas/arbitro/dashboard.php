<?php $usuario = $_SESSION['user_name'] ?? 'Árbitro'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Árbitro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><i class="fas fa-whistle"></i> Árbitro</div>
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
            <div class="breadcrumb">Inicio / Árbitro</div>
        </div>
        <div class="card">
            <div class="card-header">Accesos rápidos</div>
            <div class="card-body d-flex gap-3">
                <a class="btn btn-primary" href="/arbitro/disponibilidad"><i class="fas fa-calendar-check"></i> Disponibilidad</a>
                <a class="btn btn-primary" href="/arbitro/partidos"><i class="fas fa-volleyball"></i> Partidos</a>
                <a class="btn btn-primary" href="/arbitro/liquidaciones"><i class="fas fa-file-invoice"></i> Liquidaciones</a>
                <a class="btn btn-primary" href="/arbitro/perfil"><i class="fas fa-user-cog"></i> Perfil</a>
            </div>
        </div>
    </main>
    <script src="/js/app.js"></script>
</body>
</html>
