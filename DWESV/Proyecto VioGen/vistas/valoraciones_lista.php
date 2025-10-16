<?php
/**
 * LISTA DE VALORACIONES
 * Muestra todas las valoraciones VPR/VPER con filtros por nivel de riesgo
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/valoraciones.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/agresores.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener valoraciones
global $valoraciones, $victimas, $agresores;
$lista_valoraciones = array_values($valoraciones);

// Ordenar por fecha descendente (más recientes primero)
usort($lista_valoraciones, function($a, $b) {
    return strtotime($b['fecha_valoracion']) - strtotime($a['fecha_valoracion']);
});

// Filtro por nivel de riesgo
$filtro_nivel = $_GET['filtro_nivel'] ?? '';
if ($filtro_nivel) {
    $lista_valoraciones = array_filter($lista_valoraciones, function($val) use ($filtro_nivel) {
        return $val['nivel_riesgo'] === (int)$filtro_nivel;
    });
}

// Filtro por tipo (VPR/VPER)
$filtro_tipo = $_GET['filtro_tipo'] ?? '';
if ($filtro_tipo) {
    $lista_valoraciones = array_filter($lista_valoraciones, function($val) use ($filtro_tipo) {
        return $val['tipo'] === $filtro_tipo;
    });
}

// Filtro por búsqueda de víctima
$busqueda = $_GET['busqueda'] ?? '';
if ($busqueda) {
    $lista_valoraciones = array_filter($lista_valoraciones, function($val) use ($victimas, $busqueda) {
        $victima = $victimas[$val['victima_id']] ?? null;
        if (!$victima) return false;
        $texto = strtolower($victima['nombre'] . ' ' . $victima['apellidos'] . ' ' . $victima['num_documento']);
        return strpos($texto, strtolower($busqueda)) !== false;
    });
}

// Estadísticas
$total_valoraciones = count($valoraciones);
$por_nivel = [
    NIVEL_NO_APRECIADO => 0,
    NIVEL_BAJO => 0,
    NIVEL_MEDIO => 0,
    NIVEL_ALTO => 0,
    NIVEL_EXTREMO => 0
];

foreach ($valoraciones as $val) {
    $nivel = $val['nivel_riesgo'];
    if (isset($por_nivel[$nivel])) {
        $por_nivel[$nivel]++;
    }
}

$vpr_count = count(array_filter($valoraciones, fn($v) => $v['tipo'] === 'VPR'));
$vper_count = count(array_filter($valoraciones, fn($v) => $v['tipo'] === 'VPER'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Lista de Valoraciones</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .filter-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #E0E0E0;
        }
        tbody tr:hover {
            background: #F5F5F5;
        }
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #757575;
        }
        .badge-tipo {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            background: #2196F3;
            color: white;
        }
        .badge-tipo.vper {
            background: #9C27B0;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <li><a href="dashboard_policia.php">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php" class="active">📋 Valoraciones</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>📋 Valoraciones de Riesgo</h1>
                <a href="nueva_valoracion.php" class="btn btn-danger">➕ Nueva Valoración VPR</a>
            </div>

            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
            <?php endif; ?>

            <!-- ESTADÍSTICAS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_valoraciones; ?></div>
                    <div>Total Valoraciones</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid <?php echo COLOR_NO_APRECIADO; ?>;">
                    <div class="stat-number" style="color: <?php echo COLOR_NO_APRECIADO; ?>;"><?php echo $por_nivel[NIVEL_NO_APRECIADO]; ?></div>
                    <div>No Apreciado</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid <?php echo COLOR_BAJO; ?>;">
                    <div class="stat-number" style="color: <?php echo COLOR_BAJO; ?>;"><?php echo $por_nivel[NIVEL_BAJO]; ?></div>
                    <div>Riesgo Bajo</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid <?php echo COLOR_MEDIO; ?>;">
                    <div class="stat-number" style="color: <?php echo COLOR_MEDIO; ?>;"><?php echo $por_nivel[NIVEL_MEDIO]; ?></div>
                    <div>Riesgo Medio</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid <?php echo COLOR_ALTO; ?>;">
                    <div class="stat-number" style="color: <?php echo COLOR_ALTO; ?>;"><?php echo $por_nivel[NIVEL_ALTO]; ?></div>
                    <div>Riesgo Alto</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid <?php echo COLOR_EXTREMO; ?>;">
                    <div class="stat-number" style="color: <?php echo COLOR_EXTREMO; ?>;"><?php echo $por_nivel[NIVEL_EXTREMO]; ?></div>
                    <div>Riesgo Extremo</div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="filter-bar">
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="busqueda">Buscar Víctima</label>
                            <input type="text" 
                                   id="busqueda" 
                                   name="busqueda" 
                                   placeholder="Nombre o documento..."
                                   value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="filtro_nivel">Filtrar por Nivel de Riesgo</label>
                            <select id="filtro_nivel" name="filtro_nivel">
                                <option value="">Todos los niveles</option>
                                <option value="<?php echo NIVEL_NO_APRECIADO; ?>" <?php echo $filtro_nivel == NIVEL_NO_APRECIADO ? 'selected' : ''; ?>>
                                    No Apreciado
                                </option>
                                <option value="<?php echo NIVEL_BAJO; ?>" <?php echo $filtro_nivel == NIVEL_BAJO ? 'selected' : ''; ?>>
                                    Bajo
                                </option>
                                <option value="<?php echo NIVEL_MEDIO; ?>" <?php echo $filtro_nivel == NIVEL_MEDIO ? 'selected' : ''; ?>>
                                    Medio
                                </option>
                                <option value="<?php echo NIVEL_ALTO; ?>" <?php echo $filtro_nivel == NIVEL_ALTO ? 'selected' : ''; ?>>
                                    Alto
                                </option>
                                <option value="<?php echo NIVEL_EXTREMO; ?>" <?php echo $filtro_nivel == NIVEL_EXTREMO ? 'selected' : ''; ?>>
                                    Extremo
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="filtro_tipo">Tipo de Valoración</label>
                            <select id="filtro_tipo" name="filtro_tipo">
                                <option value="">Todas</option>
                                <option value="VPR" <?php echo $filtro_tipo === 'VPR' ? 'selected' : ''; ?>>VPR (<?php echo $vpr_count; ?>)</option>
                                <option value="VPER" <?php echo $filtro_tipo === 'VPER' ? 'selected' : ''; ?>>VPER (<?php echo $vper_count; ?>)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- TABLA DE VALORACIONES -->
            <div class="table-container">
                <?php if (empty($lista_valoraciones)): ?>
                <div class="empty-state">
                    <h3>📋 No hay valoraciones registradas</h3>
                    <p>Comienza creando una nueva valoración VPR</p>
                    <a href="nueva_valoracion.php" class="btn btn-danger">➕ Nueva Valoración</a>
                </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Víctima</th>
                            <th>Agresor</th>
                            <th>Fecha</th>
                            <th>Puntuación</th>
                            <th>Nivel de Riesgo</th>
                            <th>Evaluador</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista_valoraciones as $val): 
                            $victima = $victimas[$val['victima_id']] ?? null;
                            $agresor = $agresores[$val['agresor_id']] ?? null;
                            $nombre_nivel = obtenerNombreNivel($val['nivel_riesgo']);
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($val['id']); ?></strong></td>
                            
                            <td>
                                <span class="badge-tipo <?php echo strtolower($val['tipo']); ?>">
                                    <?php echo htmlspecialchars($val['tipo']); ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php if ($victima): ?>
                                    <strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($victima['num_documento']); ?></small>
                                <?php else: ?>
                                    <em>Víctima no encontrada</em>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if ($agresor): ?>
                                    <?php echo htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']); ?>
                                <?php else: ?>
                                    <em>No registrado</em>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($val['fecha_valoracion'])); ?>
                            </td>
                            
                            <td>
                                <strong style="font-size: 1.25rem;"><?php echo $val['puntuacion_total']; ?></strong> puntos
                            </td>
                            
                            <td>
                                <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel); ?>">
                                    <?php echo htmlspecialchars($nombre_nivel); ?>
                                </span>
                            </td>
                            
                            <td>
                                <small><?php echo htmlspecialchars($val['evaluador_id']); ?></small>
                            </td>
                            
                            <td>
                                <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>" 
                                   class="btn-small btn-primary" 
                                   title="Ver detalles completos">
                                    👁️ Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="padding: 1rem; text-align: center; background: #F5F5F5;">
                    <strong>Total: <?php echo count($lista_valoraciones); ?> valoraciones</strong>
                    <?php if ($busqueda || $filtro_nivel || $filtro_tipo): ?>
                        | <a href="valoraciones_lista.php">🔄 Limpiar filtros</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
