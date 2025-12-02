<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Administración</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><i class="fas fa-users"></i> Usuarios</div>
            <div class="user-actions"><a class="btn btn-secondary" href="/admin/dashboard">Volver</a></div>
        </div>
    </header>
    <main class="container main-content">
        <div class="content-header"><h1>Gestión de Usuarios</h1></div>
        <div class="card">
            <div class="card-body">
                <div class="search-container">
                    <div class="search-input-group">
                        <div class="search-input-icon">
                            <i class="fas fa-search"></i>
                            <input id="usuariosSearchInput" class="search-input-field" placeholder="Buscar usuarios..." />
                        </div>
                        <button id="usuariosSearchClear" class="search-clear-btn">Limpiar</button>
                    </div>
                    <div id="usuariosSearchInfo" class="search-info">
                        Mostrando <span id="usuariosSearchResults">0</span> resultados
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table searchable-table" id="tabla-usuarios">
                        <thead>
                            <tr>
                                <th data-sortable>Usuario</th>
                                <th data-sortable>Tipo</th>
                                <th data-sortable>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['usuario'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($u['tipo_usuario'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="#" aria-label="Ver"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center p-3">Sin usuarios</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="/js/app.js"></script>
    <script src="/js/search-bar.js"></script>
    <script src="/js/admin-usuarios.js"></script>
</body>
</html>
