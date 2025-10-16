<?php
/**
 * LISTA DE VÍCTIMAS
 * Muestra todas las víctimas registradas con búsqueda y filtros
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/victimas.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener todas las víctimas
$victimas = obtenerTodasVictimas();

// Búsqueda
$busqueda = $_GET['busqueda'] ?? '';
if (!empty($busqueda)) {
    $victimas = buscarVictimasPorNombre($busqueda);
}

// Filtro por estado
$filtro_estado = $_GET['estado'] ?? 'todas';
if ($filtro_estado === 'activas') {
    $victimas = listarVictimasActivas();
}

$total = count($victimas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Lista de Víctimas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="dashboard">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <li><a href="dashboard_policia.php">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php" class="active">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="header-bar">
                <h1>👥 Víctimas Registradas</h1>
                <a href="registrar_victima.php" class="btn btn-primary">➕ Nueva Víctima</a>
            </div>

            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                <strong>✅ Éxito:</strong> <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
            <?php endif; ?>

            <!-- Filtros y búsqueda -->
            <div class="card">
                <form method="GET" action="" class="grid grid-3">
                    <div class="form-group">
                        <label for="busqueda">🔍 Buscar por nombre</label>
                        <input type="text" id="busqueda" name="busqueda" 
                               placeholder="Nombre o apellidos..." 
                               value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="todas" <?php echo $filtro_estado === 'todas' ? 'selected' : ''; ?>>Todas</option>
                            <option value="activas" <?php echo $filtro_estado === 'activas' ? 'selected' : ''; ?>>Solo activas</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3>📊 Total de víctimas registradas: <strong><?php echo $total; ?></strong></h3>
            </div>

            <!-- Tabla de víctimas -->
            <div class="card">
                <?php if ($total > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Edad</th>
                            <th>Teléfono</th>
                            <th>Valoraciones</th>
                            <th>Fecha Registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($victimas as $victima): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($victima['id']); ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($victima['tipo_documento'] . ': ' . $victima['num_documento']); ?></td>
                            <td><?php echo $victima['edad']; ?> años</td>
                            <td><?php echo htmlspecialchars($victima['telefono']); ?></td>
                            <td>
                                <span class="badge-nivel nivel-bajo">
                                    <?php echo count($victima['valoraciones']); ?> valoraciones
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($victima['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($victima['activo']): ?>
                                    <span style="color: #4CAF50; font-weight: bold;">● Activa</span>
                                <?php else: ?>
                                    <span style="color: #9E9E9E; font-weight: bold;">● Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="ver_victima.php?id=<?php echo $victima['id']; ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                        👁️ Ver
                                    </a>
                                    <a href="nueva_valoracion.php?id_victima=<?php echo $victima['id']; ?>" 
                                       class="btn btn-success" 
                                       style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                        📋 VPR
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="alert alert-info">
                    <strong>ℹ️ No se encontraron víctimas</strong>
                    <p>No hay víctimas registradas con los criterios de búsqueda especificados.</p>
                    <a href="registrar_victima.php" class="btn btn-primary mt-2">Registrar primera víctima</a>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
