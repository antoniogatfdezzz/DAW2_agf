<?php
/**
 * LISTA DE AGRESORES
 * Muestra todos los agresores registrados con factores de riesgo destacados
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/agresores.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener agresores
global $agresores;
$lista_agresores = array_values($agresores);

// Filtro de búsqueda
$busqueda = $_GET['busqueda'] ?? '';
if ($busqueda) {
    $lista_agresores = array_filter($lista_agresores, function($agresor) use ($busqueda) {
        $texto = strtolower($agresor['nombre'] . ' ' . $agresor['apellidos'] . ' ' . ($agresor['num_documento'] ?? ''));
        return strpos($texto, strtolower($busqueda)) !== false;
    });
}

// Filtro por factor de riesgo
$filtro_riesgo = $_GET['filtro_riesgo'] ?? '';
if ($filtro_riesgo) {
    $lista_agresores = array_filter($lista_agresores, function($agresor) use ($filtro_riesgo) {
        switch ($filtro_riesgo) {
            case 'armas':
                return $agresor['posesion_armas'] ?? false;
            case 'antecedentes':
                return $agresor['antecedentes_penales'] ?? false;
            case 'quebrantamientos':
                return $agresor['quebrantamientos_previos'] ?? false;
            case 'adicciones':
                return $agresor['alcohol_drogas'] ?? false;
            case 'salud_mental':
                return $agresor['trastorno_diagnosticado'] ?? false;
            case 'violencia_previa':
                return $agresor['historia_agresiones_previas'] ?? false;
        }
        return true;
    });
}

// Estadísticas
$total_agresores = count($agresores);
$con_armas = count(array_filter($agresores, fn($a) => $a['posesion_armas'] ?? false));
$con_antecedentes = count(array_filter($agresores, fn($a) => $a['antecedentes_penales'] ?? false));
$con_quebrantamientos = count(array_filter($agresores, fn($a) => $a['quebrantamientos_previos'] ?? false));
$con_adicciones = count(array_filter($agresores, fn($a) => $a['alcohol_drogas'] ?? false));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Lista de Agresores</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        .stat-card.danger {
            border-left: 4px solid #D32F2F;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #D32F2F;
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
            grid-template-columns: 1fr 1fr auto;
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
        .badge-factor {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .badge-armas { background: #D32F2F; color: white; }
        .badge-antecedentes { background: #FF5722; color: white; }
        .badge-quebrantamientos { background: #F44336; color: white; }
        .badge-adicciones { background: #FF9800; color: white; }
        .badge-salud { background: #FFC107; color: #333; }
        .badge-violencia { background: #E91E63; color: white; }
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
                    <li><a href="agresores_lista.php" class="active">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>⚠️ Agresores Registrados</h1>
                <a href="registrar_agresor.php" class="btn btn-danger">➕ Nuevo Agresor</a>
            </div>

            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
            <?php endif; ?>

            <!-- ESTADÍSTICAS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_agresores; ?></div>
                    <div>Total Agresores</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo $con_armas; ?></div>
                    <div>🔫 Con Armas</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo $con_antecedentes; ?></div>
                    <div>📋 Con Antecedentes</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo $con_quebrantamientos; ?></div>
                    <div>🚨 Quebrantamientos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #FF9800;"><?php echo $con_adicciones; ?></div>
                    <div>🍺 Con Adicciones</div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="filter-bar">
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="busqueda">Buscar por Nombre o Documento</label>
                            <input type="text" 
                                   id="busqueda" 
                                   name="busqueda" 
                                   placeholder="Escribe nombre o documento..."
                                   value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="filtro_riesgo">Filtrar por Factor de Riesgo</label>
                            <select id="filtro_riesgo" name="filtro_riesgo">
                                <option value="">Todos</option>
                                <option value="armas" <?php echo $filtro_riesgo === 'armas' ? 'selected' : ''; ?>>🔫 Con armas</option>
                                <option value="antecedentes" <?php echo $filtro_riesgo === 'antecedentes' ? 'selected' : ''; ?>>📋 Con antecedentes</option>
                                <option value="quebrantamientos" <?php echo $filtro_riesgo === 'quebrantamientos' ? 'selected' : ''; ?>>🚨 Quebrantamientos</option>
                                <option value="adicciones" <?php echo $filtro_riesgo === 'adicciones' ? 'selected' : ''; ?>>🍺 Con adicciones</option>
                                <option value="salud_mental" <?php echo $filtro_riesgo === 'salud_mental' ? 'selected' : ''; ?>>🧠 Problemas salud mental</option>
                                <option value="violencia_previa" <?php echo $filtro_riesgo === 'violencia_previa' ? 'selected' : ''; ?>>💥 Violencia previa</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                    </div>
                </form>
            </div>

            <!-- TABLA DE AGRESORES -->
            <div class="table-container">
                <?php if (empty($lista_agresores)): ?>
                <div class="empty-state">
                    <h3>😊 No hay agresores registrados</h3>
                    <p>Comienza registrando un nuevo agresor</p>
                    <a href="registrar_agresor.php" class="btn btn-danger">➕ Registrar Agresor</a>
                </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Edad</th>
                            <th>Factores de Riesgo</th>
                            <th>Relación</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista_agresores as $agresor): 
                            $edad = calcularEdad($agresor['fecha_nacimiento'] ?? null);
                            $factores = obtenerFactoresRiesgoAgresor($agresor['id']);
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($agresor['id']); ?></strong></td>
                            
                            <td>
                                <strong><?php echo htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']); ?></strong>
                                <?php if ($agresor['fugado'] ?? false): ?>
                                <span class="badge-factor badge-armas">🚨 FUGADO</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><?php echo htmlspecialchars($agresor['num_documento'] ?? 'No registrado'); ?></td>
                            
                            <td><?php echo $edad ? $edad . ' años' : 'N/A'; ?></td>
                            
                            <td>
                                <?php if (empty($factores)): ?>
                                    <span style="color: #9E9E9E;">Sin factores críticos</span>
                                <?php else: ?>
                                    <?php foreach ($factores as $factor): ?>
                                        <?php
                                        $badge_class = 'badge-antecedentes';
                                        $icono = '⚠️';
                                        
                                        if (strpos($factor, 'arma') !== false) {
                                            $badge_class = 'badge-armas';
                                            $icono = '🔫';
                                        } elseif (strpos($factor, 'quebrantamiento') !== false) {
                                            $badge_class = 'badge-quebrantamientos';
                                            $icono = '🚨';
                                        } elseif (strpos($factor, 'adicción') !== false || strpos($factor, 'alcohol') !== false) {
                                            $badge_class = 'badge-adicciones';
                                            $icono = '🍺';
                                        } elseif (strpos($factor, 'mental') !== false || strpos($factor, 'suicidio') !== false) {
                                            $badge_class = 'badge-salud';
                                            $icono = '🧠';
                                        } elseif (strpos($factor, 'violencia') !== false || strpos($factor, 'agresión') !== false) {
                                            $badge_class = 'badge-violencia';
                                            $icono = '💥';
                                        }
                                        ?>
                                        <span class="badge-factor <?php echo $badge_class; ?>">
                                            <?php echo $icono . ' ' . htmlspecialchars($factor); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            
                            <td><?php echo htmlspecialchars($agresor['relacion_con_victima'] ?? 'No especificada'); ?></td>
                            
                            <td><?php echo date('d/m/Y', strtotime($agresor['fecha_registro'])); ?></td>
                            
                            <td>
                                <a href="ver_agresor.php?id=<?php echo $agresor['id']; ?>" 
                                   class="btn-small btn-primary" 
                                   title="Ver detalles">
                                    👁️ Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="padding: 1rem; text-align: center; background: #F5F5F5;">
                    <strong>Total: <?php echo count($lista_agresores); ?> agresores</strong>
                    <?php if ($busqueda || $filtro_riesgo): ?>
                        | <a href="agresores_lista.php">🔄 Limpiar filtros</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
