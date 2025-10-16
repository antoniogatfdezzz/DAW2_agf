<?php
/**
 * VER AGRESOR - DETALLE COMPLETO
 * Muestra todos los datos de un agresor y análisis de factores de riesgo
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/agresores.php';
require_once __DIR__ . '/../modelos/valoraciones.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener ID de agresor
$agresor_id = $_GET['id'] ?? null;
if (!$agresor_id) {
    header('Location: agresores_lista.php?error=ID de agresor no especificado');
    exit;
}

// Buscar agresor
global $agresores, $valoraciones;
$agresor = $agresores[$agresor_id] ?? null;

if (!$agresor) {
    header('Location: agresores_lista.php?error=Agresor no encontrado');
    exit;
}

// Obtener valoraciones relacionadas con este agresor
$valoraciones_agresor = array_filter($valoraciones, function($val) use ($agresor_id) {
    return $val['agresor_id'] === $agresor_id;
});

// Calcular edad
$edad = calcularEdad($agresor['fecha_nacimiento'] ?? null);

// Obtener factores de riesgo
$factores_riesgo = obtenerFactoresRiesgoAgresor($agresor_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Ficha de Agresor</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .agresor-header {
            background: linear-gradient(135deg, #D32F2F 0%, #B71C1C 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .agresor-header h2 {
            margin: 0 0 0.5rem 0;
        }
        .factores-riesgo-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            border-left: 6px solid #D32F2F;
        }
        .factores-riesgo-box h3 {
            margin: 0 0 1.5rem 0;
            color: #D32F2F;
            font-size: 1.3rem;
        }
        .factor-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .factor-critico {
            background: #D32F2F;
            color: white;
        }
        .factor-alto {
            background: #FF5722;
            color: white;
        }
        .factor-medio {
            background: #FF9800;
            color: white;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-section h3 {
            margin: 0 0 1rem 0;
            color: #D32F2F;
            font-size: 1.1rem;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .info-section.critical {
            border-left: 4px solid #D32F2F;
        }
        .info-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 1rem;
            margin-bottom: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #F0F0F0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #757575;
        }
        .info-value {
            color: #333;
        }
        .si-critical {
            color: #D32F2F;
            font-weight: bold;
        }
        .si-warning {
            color: #FF9800;
            font-weight: bold;
        }
        .si-positive {
            color: #4CAF50;
            font-weight: bold;
        }
        .no-value {
            color: #9E9E9E;
        }
        .alert-box {
            background: #FFEBEE;
            border-left: 4px solid #D32F2F;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .warning-box {
            background: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .empty-factors {
            background: #E8F5E9;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            color: #4CAF50;
        }
        @media print {
            .sidebar, .header-bar, .btn { display: none; }
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
                <h1>⚠️ Ficha de Agresor</h1>
                <a href="agresores_lista.php" class="btn btn-secondary">← Volver</a>
            </div>

            <!-- CABECERA -->
            <div class="agresor-header">
                <h2>⚠️ <?php echo htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']); ?></h2>
                <p style="margin: 0;">ID: <?php echo htmlspecialchars($agresor_id); ?> | Registrado: <?php echo date('d/m/Y', strtotime($agresor['fecha_registro'])); ?></p>
                <?php if ($agresor['fugado'] ?? false): ?>
                <p style="margin: 0.5rem 0 0 0; font-weight: bold; font-size: 1.2rem;">🚨 FUGADO / PARADERO DESCONOCIDO</p>
                <?php endif; ?>
            </div>

            <!-- ALERTAS CRÍTICAS -->
            <?php if ($agresor['posesion_armas'] ?? false): ?>
            <div class="alert-box">
                <strong>🔫 ALERTA CRÍTICA:</strong> Posee armas o tiene acceso a ellas. 
                <?php if (!empty($agresor['detalles_armas'])): ?>
                    - <?php echo htmlspecialchars($agresor['detalles_armas']); ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($agresor['quebrantamientos_previos'] ?? false): ?>
            <div class="alert-box">
                <strong>🚨 ALERTA:</strong> Historial de quebrantamientos de medidas.
            </div>
            <?php endif; ?>

            <?php if ($agresor['intentos_suicidio'] ?? false): ?>
            <div class="warning-box">
                <strong>⚠️ ATENCIÓN:</strong> Historial de intentos de suicidio o ideas suicidas.
            </div>
            <?php endif; ?>

            <!-- FACTORES DE RIESGO -->
            <div class="factores-riesgo-box">
                <h3>🚨 Factores de Riesgo Identificados</h3>
                
                <?php if (empty($factores_riesgo)): ?>
                <div class="empty-factors">
                    <strong>✓ No se han identificado factores de riesgo críticos en el registro actual.</strong>
                </div>
                <?php else: ?>
                <div>
                    <?php foreach ($factores_riesgo as $factor): 
                        // Determinar clase según el factor
                        $clase = 'factor-medio';
                        if (stripos($factor, 'arma') !== false || stripos($factor, 'quebrantamiento') !== false) {
                            $clase = 'factor-critico';
                        } elseif (stripos($factor, 'antecedente') !== false || stripos($factor, 'agresión') !== false) {
                            $clase = 'factor-alto';
                        }
                    ?>
                    <span class="factor-badge <?php echo $clase; ?>">
                        <?php echo htmlspecialchars($factor); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- DATOS PERSONALES -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>📋 Datos Personales</h3>
                    <div class="info-row">
                        <span class="info-label">Nombre Completo:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['tipo_documento'] . ' ' . ($agresor['num_documento'] ?? 'No registrado')); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha Nacimiento:</span>
                        <span class="info-value">
                            <?php echo $agresor['fecha_nacimiento'] ? date('d/m/Y', strtotime($agresor['fecha_nacimiento'])) . " ($edad años)" : 'No registrada'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nacionalidad:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['nacionalidad'] ?? 'No especificada'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['telefono'] ?? 'No registrado'); ?></span>
                    </div>
                </div>

                <div class="info-section">
                    <h3>📍 Domicilio y Paradero</h3>
                    <div class="info-row">
                        <span class="info-label">Domicilio:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['domicilio'] ?? 'No registrado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Coincide con víctima:</span>
                        <span class="info-value <?php echo ($agresor['domicilio_coincide_victima'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['domicilio_coincide_victima'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Paradero conocido:</span>
                        <span class="info-value <?php echo ($agresor['paradero_conocido'] ?? false) ? 'si-positive' : 'si-critical'; ?>">
                            <?php echo ($agresor['paradero_conocido'] ?? false) ? '✓ SÍ' : '🚨 NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ubicación actual:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['ubicacion_actual'] ?? 'Desconocida'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fugado:</span>
                        <span class="info-value <?php echo ($agresor['fugado'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['fugado'] ?? false) ? '🚨 SÍ' : 'NO'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- RELACIÓN Y CONVIVENCIA -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>💔 Relación con Víctima</h3>
                    <div class="info-row">
                        <span class="info-label">Tipo de Relación:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($agresor['relacion_con_victima'] ?? 'No especificada'); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Convivencia actual:</span>
                        <span class="info-value <?php echo ($agresor['convivencia_actual'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['convivencia_actual'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Situación laboral:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['situacion_empleo'] ?? 'Desconocida'); ?></span>
                    </div>
                </div>

                <div class="info-section critical">
                    <h3>📋 Antecedentes Penales</h3>
                    <div class="info-row">
                        <span class="info-label">Antecedentes penales:</span>
                        <span class="info-value <?php echo ($agresor['antecedentes_penales'] ?? false) ? 'si-critical' : 'si-positive'; ?>">
                            <?php echo ($agresor['antecedentes_penales'] ?? false) ? '⚠️ SÍ' : '✓ NO'; ?>
                        </span>
                    </div>
                    <?php if ($agresor['antecedentes_penales'] ?? false): ?>
                    <div class="info-row">
                        <span class="info-label">Detalles:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['detalles_antecedentes'] ?? 'No especificados'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Quebrantamientos:</span>
                        <span class="info-value <?php echo ($agresor['quebrantamientos_previos'] ?? false) ? 'si-critical' : 'si-positive'; ?>">
                            <?php echo ($agresor['quebrantamientos_previos'] ?? false) ? '🚨 SÍ' : '✓ NO'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE VIOLENCIA -->
            <div class="info-section critical">
                <h3>💥 Historial de Violencia y Agresiones</h3>
                <div class="info-grid" style="grid-template-columns: 1fr;">
                    <div class="info-row">
                        <span class="info-label">Agresiones previas:</span>
                        <span class="info-value <?php echo ($agresor['historia_agresiones_previas'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['historia_agresiones_previas'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Agresiones físicas:</span>
                        <span class="info-value <?php echo ($agresor['agresiones_fisicas'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['agresiones_fisicas'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Agresiones sexuales:</span>
                        <span class="info-value <?php echo ($agresor['agresiones_sexuales'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['agresiones_sexuales'] ?? false) ? '🚨 SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Violencia a terceros:</span>
                        <span class="info-value <?php echo ($agresor['violencia_otra_persona'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                            <?php echo ($agresor['violencia_otra_persona'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Violencia otras parejas:</span>
                        <span class="info-value <?php echo ($agresor['violencia_otras_parejas'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                            <?php echo ($agresor['violencia_otras_parejas'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <?php if (!empty($agresor['detalles_agresiones'])): ?>
                    <div style="grid-column: 1 / -1; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #E0E0E0;">
                        <strong>Detalles:</strong>
                        <p style="margin: 0.5rem 0 0 0;"><?php echo nl2br(htmlspecialchars($agresor['detalles_agresiones'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ADICCIONES Y SALUD MENTAL -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>🍺 Adicciones</h3>
                    <div class="info-row">
                        <span class="info-label">Consumo alcohol/drogas:</span>
                        <span class="info-value <?php echo ($agresor['alcohol_drogas'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                            <?php echo ($agresor['alcohol_drogas'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <?php if ($agresor['alcohol_drogas'] ?? false): ?>
                    <div class="info-row">
                        <span class="info-label">Gravedad:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($agresor['gravedad_adiccion'] ?? 'No especificada'); ?></strong></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="info-section">
                    <h3>🧠 Salud Mental</h3>
                    <div class="info-row">
                        <span class="info-label">Trastorno diagnosticado:</span>
                        <span class="info-value <?php echo ($agresor['trastorno_diagnosticado'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                            <?php echo ($agresor['trastorno_diagnosticado'] ?? false) ? 'SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <?php if ($agresor['trastorno_diagnosticado'] ?? false): ?>
                    <div class="info-row">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value"><?php echo htmlspecialchars($agresor['tipo_trastorno'] ?? 'No especificado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">En tratamiento:</span>
                        <span class="info-value <?php echo ($agresor['en_tratamiento'] ?? false) ? 'si-positive' : 'si-warning'; ?>">
                            <?php echo ($agresor['en_tratamiento'] ?? false) ? '✓ SÍ' : '⚠️ NO'; ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Intentos de suicidio:</span>
                        <span class="info-value <?php echo ($agresor['intentos_suicidio'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                            <?php echo ($agresor['intentos_suicidio'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ideas suicidas:</span>
                        <span class="info-value <?php echo ($agresor['ideas_suicidas'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                            <?php echo ($agresor['ideas_suicidas'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ARMAS -->
            <div class="info-section critical">
                <h3>🔫 Posesión de Armas</h3>
                <div class="info-row">
                    <span class="info-label">Posee armas:</span>
                    <span class="info-value <?php echo ($agresor['posesion_armas'] ?? false) ? 'si-critical' : 'si-positive'; ?>">
                        <?php echo ($agresor['posesion_armas'] ?? false) ? '🚨 SÍ' : '✓ NO'; ?>
                    </span>
                </div>
                <?php if ($agresor['posesion_armas'] ?? false): ?>
                <div class="info-row">
                    <span class="info-label">Acceso fácil:</span>
                    <span class="info-value <?php echo ($agresor['tiene_acceso_armas'] ?? false) ? 'si-critical' : 'no-value'; ?>">
                        <?php echo ($agresor['tiene_acceso_armas'] ?? false) ? '🚨 SÍ' : 'NO'; ?>
                    </span>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E0E0E0;">
                    <strong>Detalles de armas:</strong>
                    <p style="margin: 0.5rem 0 0 0;"><?php echo nl2br(htmlspecialchars($agresor['detalles_armas'] ?? 'No especificado')); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- COMPORTAMIENTO -->
            <div class="info-section">
                <h3>😡 Comportamiento y Conductas</h3>
                <div class="info-row">
                    <span class="info-label">Celos exagerados:</span>
                    <span class="info-value <?php echo ($agresor['celos_exagerados'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                        <?php echo ($agresor['celos_exagerados'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Conductas de control:</span>
                    <span class="info-value <?php echo ($agresor['conductas_control'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                        <?php echo ($agresor['conductas_control'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Conductas de acoso:</span>
                    <span class="info-value <?php echo ($agresor['conductas_acoso'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                        <?php echo ($agresor['conductas_acoso'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Problemas recientes:</span>
                    <span class="info-value <?php echo ($agresor['problemas_personales_recientes'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                        <?php echo ($agresor['problemas_personales_recientes'] ?? false) ? 'SÍ' : 'NO'; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Antecedentes familiares violencia:</span>
                    <span class="info-value <?php echo ($agresor['antecedentes_familiares_violencia'] ?? false) ? 'si-warning' : 'no-value'; ?>">
                        <?php echo ($agresor['antecedentes_familiares_violencia'] ?? false) ? 'SÍ' : 'NO'; ?>
                    </span>
                </div>
            </div>

            <!-- MEDIDAS JUDICIALES -->
            <div class="info-section">
                <h3>⚖️ Medidas Judiciales</h3>
                <div class="info-row">
                    <span class="info-label">Medidas cautelares activas:</span>
                    <span class="info-value <?php echo ($agresor['medidas_cautelares_activas'] ?? false) ? 'si-positive' : 'no-value'; ?>">
                        <?php echo ($agresor['medidas_cautelares_activas'] ?? false) ? '✓ SÍ' : 'NO'; ?>
                    </span>
                </div>
                <?php if ($agresor['medidas_cautelares_activas'] ?? false): ?>
                <div class="info-row">
                    <span class="info-label">Cumplimiento:</span>
                    <span class="info-value">
                        <strong><?php echo htmlspecialchars($agresor['cumplimiento_medidas'] ?? 'No especificado'); ?></strong>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- OBSERVACIONES -->
            <?php if (!empty($agresor['observaciones'])): ?>
            <div class="info-section">
                <h3>📝 Observaciones</h3>
                <p style="margin: 0; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($agresor['observaciones'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- VALORACIONES RELACIONADAS -->
            <?php if (!empty($valoraciones_agresor)): ?>
            <div class="info-section">
                <h3>📋 Valoraciones Relacionadas (<?php echo count($valoraciones_agresor); ?>)</h3>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($valoraciones_agresor as $val): ?>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>">
                            <?php echo htmlspecialchars($val['tipo']); ?> - <?php echo date('d/m/Y', strtotime($val['fecha_valoracion'])); ?>
                            - Puntuación: <?php echo $val['puntuacion_total']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- ACCIONES -->
            <div class="info-section" style="text-align: center; padding: 2rem;">
                <h3>Acciones Disponibles</h3>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
                    <button onclick="window.print()" class="btn btn-secondary" style="border: 2px solid #333; background: white; color: #333;">
                        🖨️ Imprimir Ficha
                    </button>
                    <a href="agresores_lista.php" class="btn btn-secondary">← Volver a Lista</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
