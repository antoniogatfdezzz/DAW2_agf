<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partidos - Administración</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><i class="fas fa-volleyball"></i> Partidos</div>
            <div class="user-actions"><a class="btn btn-secondary" href="/admin/dashboard">Volver</a></div>
        </div>
    </header>
    <main class="container main-content">
        <div class="content-header"><h1>Gestión de Partidos</h1></div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table" id="tabla-partidos">
                        <thead>
                            <tr>
                                <th data-sortable>Fecha</th>
                                <th data-sortable>Local</th>
                                <th data-sortable>Visitante</th>
                                <th data-sortable>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($partidos)): ?>
                                <?php foreach ($partidos as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['fecha'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['equipo_local'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['equipo_visitante'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['categoria'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['estado'] ?? '') ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="#" aria-label="Ver"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center p-3">Sin partidos</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="/js/app.js"></script>
</body>
</html>
