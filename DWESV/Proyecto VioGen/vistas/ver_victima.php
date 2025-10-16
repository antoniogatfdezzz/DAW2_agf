<?php
/**
 * VER VÍCTIMA - DETALLE COMPLETO
 * Muestra todos los datos de una víctima y su historial de valoraciones
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/valoraciones.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener ID de víctima
$victima_id = $_GET['id'] ?? null;
if (!$victima_id) {
    header('Location: victimas_lista.php?error=ID de víctima no especificado');
    exit;
}

// Buscar víctima
global $victimas, $valoraciones;
$victima = $victimas[$victima_id] ?? null;

if (!$victima) {
    header('Location: victimas_lista.php?error=Víctima no encontrada');
    exit;
}

// Obtener valoraciones de esta víctima
$valoraciones_victima = array_filter($valoraciones, function($val) use ($victima_id) {
    return $val['victima_id'] === $victima_id;
});

// Ordenar por fecha descendente
usort($valoraciones_victima, function($a, $b) {
    return strtotime($b['fecha_valoracion']) - strtotime($a['fecha_valoracion']);
});

// Calcular edad
$edad = calcularEdad($victima['fecha_nacimiento'] ?? null);

// Nivel de riesgo actual (última valoración)
$nivel_actual = null;
$nombre_nivel_actual = 'Sin valorar';
if (!empty($valoraciones_victima)) {
    $ultima_valoracion = reset($valoraciones_victima);
    $nivel_actual = $ultima_valoracion['nivel_riesgo'];
    $nombre_nivel_actual = obtenerNombreNivel($nivel_actual);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Ficha de Víctima</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .victima-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .victima-header h2 {
            margin: 0 0 0.5rem 0;
        }
        .nivel-badge-large {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.25rem;
            font-weight: bold;
            text-align: center;
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
        .info-row {
            display: grid;
            grid-template-columns: 150px 1fr;
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
        .alert-box {
            background: #FFEBEE;
            border-left: 4px solid #D32F2F;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .historial-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .historial-section h3 {
            margin: 0 0 1.5rem 0;
            color: #D32F2F;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .valoracion-card {
            background: #F5F5F5;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border-left: 4px solid #2196F3;
        }
        .valoracion-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .valoracion-meta {
            color: #757575;
            font-size: 0.875rem;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #757575;
        }
        .si-value {
            color: #4CAF50;
            font-weight: bold;
        }
        .no-value {
            color: #9E9E9E;
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
                    <li><a href="victimas_lista.php" class="active">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
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
                <h1>👤 Ficha de Víctima</h1>
                <div>
                    <a href="nueva_valoracion.php?victima_id=<?php echo $victima_id; ?>" class="btn btn-danger">➕ Nueva Valoración VPR</a>
                    <a href="victimas_lista.php" class="btn btn-secondary">← Volver</a>
                </div>
            </div>

            <!-- CABECERA CON NIVEL ACTUAL -->
            <div class="victima-header">
                <div>
                    <h2><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></h2>
                    <p style="margin: 0;">ID: <?php echo htmlspecialchars($victima_id); ?> | Registrada: <?php echo date('d/m/Y', strtotime($victima['fecha_registro'])); ?></p>
                </div>
                <div class="nivel-badge-large badge-nivel nivel-<?php echo strtolower($nombre_nivel_actual); ?>">
                    RIESGO: <?php echo strtoupper($nombre_nivel_actual); ?>
                </div>
            </div>

            <!-- ALERTA SI HAY MENORES -->
            <?php if ($victima['tiene_menores'] ?? false): ?>
            <div class="alert-box">
                <strong>⚠️ ATENCIÓN:</strong> Hay menores a cargo que pueden requerir medidas de protección adicionales.
            </div>
            <?php endif; ?>

            <!-- DATOS PERSONALES -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>📋 Datos Personales</h3>
                    <div class="info-row">
                        <span class="info-label">Nombre Completo:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['tipo_documento'] . ' ' . $victima['num_documento']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha Nacimiento:</span>
                        <span class="info-value"><?php echo date('d/m/Y', strtotime($victima['fecha_nacimiento'])); ?> (<?php echo $edad; ?> años)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nacionalidad:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['nacionalidad'] ?? 'No especificada'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado Civil:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['estado_civil'] ?? 'No especificado'); ?></span>
                    </div>
                </div>

                <div class="info-section">
                    <h3>📞 Contacto y Domicilio</h3>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['telefono'] ?? 'No registrado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['email'] ?? 'No registrado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Domicilio:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['domicilio'] ?? 'No registrado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo Vivienda:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['tipo_vivienda'] ?? 'No especificado'); ?></span>
                    </div>
                </div>
            </div>

            <!-- SITUACIÓN SOCIAL Y LABORAL -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>💼 Situación Laboral y Económica</h3>
                    <div class="info-row">
                        <span class="info-label">Empleo:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['empleo'] ?? 'No especificado'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dependencia Económica:</span>
                        <span class="info-value <?php echo ($victima['dependencia_economica'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['dependencia_economica'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ingresos Propios:</span>
                        <span class="info-value <?php echo ($victima['ingresos_propios'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['ingresos_propios'] ?? false) ? '✓ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                </div>

                <div class="info-section">
                    <h3>👶 Menores a Cargo</h3>
                    <div class="info-row">
                        <span class="info-label">Tiene Menores:</span>
                        <span class="info-value <?php echo ($victima['tiene_menores'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['tiene_menores'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <?php if ($victima['tiene_menores'] ?? false): ?>
                    <div class="info-row">
                        <span class="info-label">Número de Menores:</span>
                        <span class="info-value"><strong><?php echo $victima['numero_menores'] ?? 0; ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Edades:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['edades_menores'] ?? 'No especificadas'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Custodia:</span>
                        <span class="info-value"><?php echo htmlspecialchars($victima['custodia_menores'] ?? 'No especificada'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SALUD -->
            <div class="info-grid">
                <div class="info-section">
                    <h3>🏥 Salud Física</h3>
                    <div class="info-row">
                        <span class="info-label">Lesiones Visibles:</span>
                        <span class="info-value <?php echo ($victima['lesiones_visibles'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['lesiones_visibles'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Parte de Lesiones:</span>
                        <span class="info-value <?php echo ($victima['parte_lesiones'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['parte_lesiones'] ?? false) ? '✓ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Discapacidad:</span>
                        <span class="info-value <?php echo ($victima['tiene_discapacidad'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['tiene_discapacidad'] ?? false) ? 'SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Enfermedad Crónica:</span>
                        <span class="info-value <?php echo ($victima['enfermedad_cronica'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['enfermedad_cronica'] ?? false) ? 'SÍ' : 'NO'; ?>
                        </span>
                    </div>
                </div>

                <div class="info-section">
                    <h3>🧠 Salud Mental y Apoyo</h3>
                    <div class="info-row">
                        <span class="info-label">Tratamiento Psicológico:</span>
                        <span class="info-value <?php echo ($victima['tratamiento_psicologico'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['tratamiento_psicologico'] ?? false) ? '✓ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Red de Apoyo:</span>
                        <span class="info-value <?php echo ($victima['red_apoyo_familiar'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['red_apoyo_familiar'] ?? false) ? '✓ SÍ - Familia/amigos' : 'NO'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Aislamiento Social:</span>
                        <span class="info-value <?php echo ($victima['aislamiento_social'] ?? false) ? 'si-value' : 'no-value'; ?>">
                            <?php echo ($victima['aislamiento_social'] ?? false) ? '⚠️ SÍ' : 'NO'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <?php if (!empty($victima['observaciones'])): ?>
            <div class="info-section">
                <h3>📝 Observaciones</h3>
                <p style="margin: 0; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($victima['observaciones'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- HISTORIAL DE VALORACIONES -->
            <div class="historial-section">
                <h3>📋 Historial de Valoraciones (<?php echo count($valoraciones_victima); ?>)</h3>
                
                <?php if (empty($valoraciones_victima)): ?>
                <div class="empty-state">
                    <p>No hay valoraciones registradas para esta víctima.</p>
                    <a href="nueva_valoracion.php?victima_id=<?php echo $victima_id; ?>" class="btn btn-danger">
                        ➕ Crear Primera Valoración VPR
                    </a>
                </div>
                <?php else: ?>
                    <?php foreach ($valoraciones_victima as $val): 
                        $nombre_nivel = obtenerNombreNivel($val['nivel_riesgo']);
                    ?>
                    <div class="valoracion-card">
                        <div class="valoracion-header-row">
                            <div>
                                <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($val['tipo']); ?> - ID: <?php echo htmlspecialchars($val['id']); ?></strong>
                                <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel); ?>" style="margin-left: 1rem;">
                                    <?php echo $nombre_nivel; ?>
                                </span>
                            </div>
                            <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>" class="btn btn-primary btn-small">
                                👁️ Ver Detalle
                            </a>
                        </div>
                        
                        <div class="valoracion-meta">
                            📅 Fecha: <?php echo date('d/m/Y H:i', strtotime($val['fecha_valoracion'])); ?>
                            | 📊 Puntuación: <strong><?php echo $val['puntuacion_total']; ?></strong> puntos
                            | 👤 Evaluador: <?php echo htmlspecialchars($val['evaluador_id']); ?>
                        </div>
                        
                        <?php if (!empty($val['observaciones_evaluador'])): ?>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E0E0E0;">
                            <strong>Observaciones:</strong>
                            <p style="margin: 0.5rem 0 0 0;"><?php echo htmlspecialchars(substr($val['observaciones_evaluador'], 0, 200)); ?><?php echo strlen($val['observaciones_evaluador']) > 200 ? '...' : ''; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ACCIONES -->
            <div class="info-section" style="text-align: center; padding: 2rem;">
                <h3>Acciones Disponibles</h3>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
                    <a href="nueva_valoracion.php?victima_id=<?php echo $victima_id; ?>" class="btn btn-danger">
                        ➕ Nueva Valoración
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary" style="border: 2px solid #333; background: white; color: #333;">
                        🖨️ Imprimir Ficha
                    </button>
                    <a href="victimas_lista.php" class="btn btn-secondary">← Volver a Lista</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
