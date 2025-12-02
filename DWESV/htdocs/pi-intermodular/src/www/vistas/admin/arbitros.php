<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Árbitros - Administración</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><i class="fas fa-whistle"></i> Árbitros</div>
            <div class="user-actions"><a class="btn btn-secondary" href="/admin/dashboard">Volver</a></div>
        </div>
    </header>
    <main class="container main-content">
        <div class="content-header"><h1>Gestión de Árbitros</h1></div>
        <div class="card">
            <div class="card-body">
                <div class="search-container">
                    <div class="search-input-group">
                        <div class="search-input-icon">
                            <i class="fas fa-search"></i>
                            <input id="arbitrosSearchInput" class="search-input-field" placeholder="Buscar árbitros..." />
                        </div>
                        <button id="arbitrosSearchClear" class="search-clear-btn">Limpiar</button>
                    </div>
                    <div id="arbitrosSearchInfo" class="search-info">
                        Mostrando <span id="arbitrosSearchResults">0</span> resultados
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table searchable-table" id="tabla-arbitros">
                        <thead>
                            <tr>
                                <th data-sortable>Nombre</th>
                                <th data-sortable>Apellidos</th>
                                <th data-sortable>Licencia</th>
                                <th data-sortable>Ciudad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($arbitros)): ?>
                                <?php foreach ($arbitros as $a): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a['nombre'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($a['apellidos'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($a['numero_licencia'] ?? $a['licencia'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($a['ciudad'] ?? '') ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="#" aria-label="Ver"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center p-3">Sin árbitros</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="/js/app.js"></script>
    <script src="/js/search-bar.js"></script>
    <script src="/js/admin-arbitros.js"></script>
</body>
</html>
