<?php
/**
 * PÁGINA DE CASOS PENDIENTES
 * Muestra casos que requieren revisión VPER o seguimiento
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/valoraciones.php';
require_once __DIR__ . '/../modelos/victimas.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

global $valoraciones, $victimas;

// Casos que requieren VPER (valoraciones VPR antiguas > 30 días)
$fecha_limite = date('Y-m-d', strtotime('-30 days'));
$casos_vper_pendiente = [];

foreach ($valoraciones as $val) {
    if ($val['tipo'] === 'VPR' && $val['fecha_valoracion'] < $fecha_limite) {
        // Verificar si ya tiene VPER posterior
        $tiene_vper = false;
        foreach ($valoraciones as $v) {
            if ($v['victima_id'] === $val['victima_id'] && 
                $v['tipo'] === 'VPER' && 
                $v['fecha_valoracion'] > $val['fecha_valoracion']) {
                $tiene_vper = true;
                break;
            }
        }
        
        if (!$tiene_vper && $val['nivel_riesgo'] >= NIVEL_MEDIO) {
            $casos_vper_pendiente[] = $val;
        }
    }
}

// Casos de riesgo alto/extremo para seguimiento
$casos_riesgo_alto = array_filter($valoraciones, function($val) {
    return $val['nivel_riesgo'] >= NIVEL_ALTO;
});

// Ordenar por nivel de riesgo descendente
usort($casos_riesgo_alto, function($a, $b) {
    return $b['nivel_riesgo'] - $a['nivel_riesgo'];
});

// Víctimas sin valoración reciente (registradas hace > 7 días sin VPR)
$fecha_limite_registro = date('Y-m-d', strtotime('-7 days'));
$victimas_sin_valorar = [];

foreach ($victimas as $victima) {
    if ($victima['fecha_registro'] < $fecha_limite_registro) {
        $tiene_valoracion = false;
        foreach ($valoraciones as $val) {
            if ($val['victima_id'] === $victima['id']) {
                $tiene_valoracion = true;
                break;
            }
        }
        
        if (!$tiene_valoracion && $victima['activa']) {
            $victimas_sin_valorar[] = $victima;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Casos Pendientes</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .stats-row {
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
        .stat-card.urgent {
            border-left: 4px solid #D32F2F;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #D32F2F;
        }
        .section-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .section-card h3 {
            margin: 0 0 1rem 0;
            color: #D32F2F;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .caso-item {
            background: #F5F5F5;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border-left: 4px solid #FF9800;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .caso-item.critico {
            border-left-color: #D32F2F;
            background: #FFEBEE;
        }
        .caso-info {
            flex: 1;
        }
        .caso-actions {
            display: flex;
            gap: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #4CAF50;
            background: #E8F5E9;
            border-radius: 6px;
        }
        .dias-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            background: #FF9800;
            color: white;
        }
        .dias-badge.urgente {
            background: #D32F2F;
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
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="pendientes.php" class="active">⏰ Casos Pendientes</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>⏰ Casos Pendientes de Seguimiento</h1>
                <a href="dashboard_policia.php" class="btn btn-secondary">← Dashboard</a>
            </div>

            <!-- ESTADÍSTICAS -->
            <div class="stats-row">
                <div class="stat-card urgent">
                    <div class="stat-number"><?php echo count($casos_vper_pendiente); ?></div>
                    <div>VPER Pendientes</div>
                </div>
                <div class="stat-card urgent">
                    <div class="stat-number"><?php echo count($casos_riesgo_alto); ?></div>
                    <div>Casos Riesgo Alto/Extremo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #FF9800;"><?php echo count($victimas_sin_valorar); ?></div>
                    <div>Sin Valoración</div>
                </div>
            </div>

            <!-- VPER PENDIENTES -->
            <div class="section-card">
                <h3>🔄 Valoraciones VPER Pendientes (VPR > 30 días)</h3>
                
                <?php if (empty($casos_vper_pendiente)): ?>
                <div class="empty-state">
                    <strong>✓ No hay valoraciones VPER pendientes</strong>
                    <p style="margin: 0.5rem 0 0 0;">Todos los casos de riesgo medio o superior tienen seguimiento actualizado.</p>
                </div>
                <?php else: ?>
                <p style="color: #757575; margin-bottom: 1rem;">
                    Los siguientes casos requieren una Valoración Policial de Evolución del Riesgo (VPER):
                </p>
                
                <?php foreach ($casos_vper_pendiente as $val): 
                    $victima = $victimas[$val['victima_id']] ?? null;
                    if (!$victima) continue;
                    
                    $dias_desde_vpr = (strtotime('now') - strtotime($val['fecha_valoracion'])) / 86400;
                    $dias_desde_vpr = floor($dias_desde_vpr);
                    $nombre_nivel = obtenerNombreNivel($val['nivel_riesgo']);
                ?>
                <div class="caso-item <?php echo $val['nivel_riesgo'] >= NIVEL_ALTO ? 'critico' : ''; ?>">
                    <div class="caso-info">
                        <strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong>
                        <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel); ?>" style="margin-left: 0.5rem;">
                            <?php echo $nombre_nivel; ?>
                        </span>
                        <span class="dias-badge <?php echo $dias_desde_vpr > 60 ? 'urgente' : ''; ?>">
                            <?php echo $dias_desde_vpr; ?> días desde VPR
                        </span>
                        <br>
                        <small style="color: #757575;">
                            VPR realizada: <?php echo date('d/m/Y', strtotime($val['fecha_valoracion'])); ?> 
                            | ID Valoración: <?php echo $val['id']; ?>
                        </small>
                    </div>
                    <div class="caso-actions">
                        <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>" class="btn btn-primary btn-small">
                            👁️ Ver VPR
                        </a>
                        <a href="nueva_valoracion.php?victima_id=<?php echo $val['victima_id']; ?>" class="btn btn-danger btn-small">
                            ➕ Crear VPER
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- CASOS DE RIESGO ALTO -->
            <div class="section-card">
                <h3>🚨 Casos de Riesgo Alto/Extremo (Seguimiento Prioritario)</h3>
                
                <?php if (empty($casos_riesgo_alto)): ?>
                <div class="empty-state">
                    <strong>✓ No hay casos de riesgo alto o extremo</strong>
                </div>
                <?php else: ?>
                <p style="color: #757575; margin-bottom: 1rem;">
                    Casos que requieren seguimiento prioritario por su nivel de riesgo:
                </p>
                
                <?php foreach ($casos_riesgo_alto as $val): 
                    $victima = $victimas[$val['victima_id']] ?? null;
                    if (!$victima) continue;
                    
                    $dias_desde_valoracion = (strtotime('now') - strtotime($val['fecha_valoracion'])) / 86400;
                    $dias_desde_valoracion = floor($dias_desde_valoracion);
                    $nombre_nivel = obtenerNombreNivel($val['nivel_riesgo']);
                ?>
                <div class="caso-item critico">
                    <div class="caso-info">
                        <strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong>
                        <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel); ?>" style="margin-left: 0.5rem;">
                            <?php echo $nombre_nivel; ?>
                        </span>
                        <span class="badge-nivel" style="background: #333; margin-left: 0.5rem;">
                            📊 <?php echo $val['puntuacion_total']; ?> puntos
                        </span>
                        <br>
                        <small style="color: #757575;">
                            <?php echo htmlspecialchars($val['tipo']); ?> realizada hace <?php echo $dias_desde_valoracion; ?> días 
                            (<?php echo date('d/m/Y', strtotime($val['fecha_valoracion'])); ?>)
                        </small>
                        <?php if (!empty($val['medidas_recomendadas'])): ?>
                        <br>
                        <small style="color: #D32F2F;">
                            <strong>Medidas:</strong> <?php echo htmlspecialchars(substr($val['medidas_recomendadas'], 0, 100)); ?>...
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="caso-actions">
                        <a href="ver_victima.php?id=<?php echo $val['victima_id']; ?>" class="btn btn-primary btn-small">
                            👁️ Ver Víctima
                        </a>
                        <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>" class="btn btn-secondary btn-small">
                            📋 Ver Valoración
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- VÍCTIMAS SIN VALORACIÓN -->
            <div class="section-card">
                <h3>⚠️ Víctimas sin Valoración VPR</h3>
                
                <?php if (empty($victimas_sin_valorar)): ?>
                <div class="empty-state">
                    <strong>✓ Todas las víctimas registradas tienen valoración VPR</strong>
                </div>
                <?php else: ?>
                <p style="color: #757575; margin-bottom: 1rem;">
                    Víctimas registradas hace más de 7 días que aún no tienen valoración VPR:
                </p>
                
                <?php foreach ($victimas_sin_valorar as $victima): 
                    $dias_desde_registro = (strtotime('now') - strtotime($victima['fecha_registro'])) / 86400;
                    $dias_desde_registro = floor($dias_desde_registro);
                ?>
                <div class="caso-item">
                    <div class="caso-info">
                        <strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong>
                        <span class="dias-badge <?php echo $dias_desde_registro > 14 ? 'urgente' : ''; ?>">
                            <?php echo $dias_desde_registro; ?> días sin valorar
                        </span>
                        <br>
                        <small style="color: #757575;">
                            Documento: <?php echo htmlspecialchars($victima['num_documento']); ?> 
                            | Registrada: <?php echo date('d/m/Y', strtotime($victima['fecha_registro'])); ?>
                        </small>
                    </div>
                    <div class="caso-actions">
                        <a href="ver_victima.php?id=<?php echo $victima['id']; ?>" class="btn btn-primary btn-small">
                            👁️ Ver Víctima
                        </a>
                        <a href="nueva_valoracion.php?victima_id=<?php echo $victima['id']; ?>" class="btn btn-danger btn-small">
                            ➕ Crear VPR
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- RESUMEN Y RECOMENDACIONES -->
            <div class="section-card" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);">
                <h3 style="color: #1976D2;">📋 Resumen y Recomendaciones</h3>
                <ul style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
                    <li><strong>Total casos pendientes:</strong> <?php echo count($casos_vper_pendiente) + count($victimas_sin_valorar); ?></li>
                    <li><strong>Casos de riesgo alto/extremo activos:</strong> <?php echo count($casos_riesgo_alto); ?></li>
                    <li>Las valoraciones VPER deben realizarse cada 30-60 días según el nivel de riesgo</li>
                    <li>Los casos de riesgo alto y extremo requieren seguimiento quincenal o mensual</li>
                    <li>Todas las víctimas deben tener una valoración VPR inicial en los primeros 7 días</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
