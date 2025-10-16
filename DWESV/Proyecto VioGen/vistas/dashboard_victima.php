<?php
/**
 * DASHBOARD PARA VÍCTIMAS
 * Panel simplificado para que las víctimas puedan ver sus datos y valoraciones
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/valoraciones.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_VICTIMA)) {
    header('Location: login.html?error=Debe iniciar sesión como víctima');
    exit;
}

// Obtener datos de la víctima actual
$victima_id = $_SESSION['usuario_id'];
global $victimas, $valoraciones;
$victima = $victimas[$victima_id] ?? null;

if (!$victima) {
    header('Location: login.html?error=Datos de víctima no encontrados');
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

// Nivel de riesgo actual
$nivel_actual = null;
$nombre_nivel_actual = 'Sin valorar';
$ultima_valoracion = null;

if (!empty($valoraciones_victima)) {
    $ultima_valoracion = reset($valoraciones_victima);
    $nivel_actual = $ultima_valoracion['nivel_riesgo'];
    $nombre_nivel_actual = obtenerNombreNivel($nivel_actual);
}

// Calcular edad
$edad = calcularEdad($victima['fecha_nacimiento'] ?? null);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Mi Panel</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .welcome-banner h2 {
            margin: 0 0 0.5rem 0;
        }
        .nivel-actual-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        .nivel-badge-xlarge {
            display: inline-block;
            padding: 1.5rem 3rem;
            border-radius: 12px;
            font-size: 1.75rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-card h3 {
            margin: 0 0 1rem 0;
            color: #667eea;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
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
        .emergency-box {
            background: linear-gradient(135deg, #D32F2F 0%, #B71C1C 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .emergency-box h3 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }
        .emergency-number {
            font-size: 3rem;
            font-weight: bold;
            margin: 1rem 0;
            letter-spacing: 0.1rem;
        }
        .recursos-card {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .recursos-card h3 {
            margin: 0 0 1rem 0;
            color: #1976D2;
        }
        .recursos-card ul {
            margin: 0;
            padding-left: 1.5rem;
            line-height: 1.8;
        }
        .valoracion-card {
            background: #F5F5F5;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border-left: 4px solid #2196F3;
        }
        .valoracion-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #757575;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <li><a href="dashboard_victima.php" class="active">🏠 Mi Panel</a></li>
                    <li><a href="ayuda.php">❓ Ayuda</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
            
            <div style="padding: 1rem; margin-top: 2rem; background: rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                <p style="margin: 0; font-size: 0.875rem; line-height: 1.6;">
                    <strong>📞 Teléfono de Atención:</strong><br>
                    <span style="font-size: 1.5rem; font-weight: bold;">016</span><br>
                    <small>24 horas, llamada gratuita y sin rastro en factura</small>
                </p>
            </div>
        </aside>

        <main class="main-content">
            <!-- BIENVENIDA -->
            <div class="welcome-banner">
                <h2>👋 Bienvenida, <?php echo htmlspecialchars($victima['nombre']); ?></h2>
                <p style="margin: 0;">Esta es tu área personal donde puedes consultar tu información y valoraciones de riesgo.</p>
            </div>

            <!-- EMERGENCIAS -->
            <div class="emergency-box">
                <h3>🚨 EN CASO DE EMERGENCIA</h3>
                <p style="margin: 0;">Si estás en peligro inminente, llama inmediatamente:</p>
                <div class="emergency-number">112</div>
                <p style="margin: 0; font-size: 1.1rem;">
                    <strong>Policía Nacional: 091 | Guardia Civil: 062</strong>
                </p>
            </div>

            <!-- NIVEL DE RIESGO ACTUAL -->
            <div class="nivel-actual-card">
                <h2 style="margin-top: 0; color: #333;">📊 Tu Nivel de Riesgo Actual</h2>
                
                <?php if ($ultima_valoracion): ?>
                    <div class="nivel-badge-xlarge badge-nivel nivel-<?php echo strtolower($nombre_nivel_actual); ?>">
                        <?php echo strtoupper($nombre_nivel_actual); ?>
                    </div>
                    
                    <p style="color: #757575; margin: 1rem 0;">
                        Última valoración realizada el <?php echo date('d/m/Y', strtotime($ultima_valoracion['fecha_valoracion'])); ?>
                    </p>

                    <?php if ($nivel_actual >= NIVEL_ALTO): ?>
                    <div style="background: #FFEBEE; padding: 1rem; border-radius: 6px; margin-top: 1rem;">
                        <strong style="color: #D32F2F;">⚠️ Recomendación:</strong> 
                        Por favor, mantén contacto regular con las autoridades y sigue todas las medidas de protección establecidas.
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ultima_valoracion['medidas_recomendadas'])): ?>
                    <div style="background: #E3F2FD; padding: 1rem; border-radius: 6px; margin-top: 1rem; text-align: left;">
                        <strong style="color: #1976D2;">🛡️ Medidas de Protección:</strong>
                        <p style="margin: 0.5rem 0 0 0;"><?php echo nl2br(htmlspecialchars($ultima_valoracion['medidas_recomendadas'])); ?></p>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color: #757575; font-size: 1.1rem;">
                        No hay valoraciones registradas todavía.
                    </p>
                <?php endif; ?>
            </div>

            <!-- MIS DATOS -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>👤 Mis Datos Personales</h3>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span><strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span><?php echo htmlspecialchars($victima['tipo_documento'] . ' ' . $victima['num_documento']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Edad:</span>
                        <span><?php echo $edad; ?> años</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span><?php echo htmlspecialchars($victima['telefono'] ?? 'No registrado'); ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h3>📊 Mis Valoraciones</h3>
                    <div class="info-row">
                        <span class="info-label">Total valoraciones:</span>
                        <span><strong><?php echo count($valoraciones_victima); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nivel actual:</span>
                        <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel_actual); ?>">
                            <?php echo $nombre_nivel_actual; ?>
                        </span>
                    </div>
                    <?php if ($ultima_valoracion): ?>
                    <div class="info-row">
                        <span class="info-label">Última valoración:</span>
                        <span><?php echo date('d/m/Y', strtotime($ultima_valoracion['fecha_valoracion'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- HISTORIAL DE VALORACIONES -->
            <?php if (!empty($valoraciones_victima)): ?>
            <div class="info-card">
                <h3>📋 Historial de Valoraciones</h3>
                
                <?php foreach ($valoraciones_victima as $val): 
                    $nombre_nivel = obtenerNombreNivel($val['nivel_riesgo']);
                ?>
                <div class="valoracion-card">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($val['tipo']); ?></strong>
                            <span class="badge-nivel nivel-<?php echo strtolower($nombre_nivel); ?>" style="margin-left: 1rem;">
                                <?php echo $nombre_nivel; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="valoracion-meta">
                        <span>📅 <?php echo date('d/m/Y H:i', strtotime($val['fecha_valoracion'])); ?></span>
                        <span>📊 Puntuación: <strong><?php echo $val['puntuacion_total']; ?></strong></span>
                    </div>
                    
                    <?php if (!empty($val['observaciones_evaluador'])): ?>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E0E0E0;">
                        <strong>Observaciones:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: #555;">
                            <?php echo htmlspecialchars(substr($val['observaciones_evaluador'], 0, 200)); ?>
                            <?php echo strlen($val['observaciones_evaluador']) > 200 ? '...' : ''; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- RECURSOS Y AYUDA -->
            <div class="recursos-card">
                <h3>📚 Recursos y Ayuda Disponible</h3>
                <ul>
                    <li><strong>016 - Teléfono contra la violencia de género</strong> (24h, gratuito, no deja rastro en factura)</li>
                    <li><strong>WhatsApp: 600 000 016</strong> (Atención vía mensaje)</li>
                    <li><strong>Email: 016-online@igualdad.gob.es</strong></li>
                    <li><strong>Oficinas de Atención a las Víctimas</strong> (en juzgados y comisarías)</li>
                    <li><strong>Servicios Sociales</strong> de tu ayuntamiento</li>
                    <li><strong>Casas de Acogida</strong> (alojamiento seguro temporal)</li>
                    <li><strong>Atención Psicológica</strong> especializada</li>
                    <li><strong>Asesoramiento Jurídico</strong> gratuito</li>
                </ul>
            </div>

            <!-- INFORMACIÓN IMPORTANTE -->
            <div class="info-card">
                <h3>ℹ️ Información Importante</h3>
                <ul style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
                    <li>Las valoraciones de riesgo son realizadas por profesionales especializados.</li>
                    <li>El nivel de riesgo puede cambiar con el tiempo según las circunstancias.</li>
                    <li>Si percibes un aumento del peligro, contacta inmediatamente con las autoridades.</li>
                    <li>Todas tus comunicaciones con el sistema son confidenciales.</li>
                    <li>Tienes derecho a solicitar medidas de protección adicionales en cualquier momento.</li>
                    <li>El incumplimiento de medidas cautelares por parte del agresor debe ser denunciado de inmediato.</li>
                </ul>
            </div>

            <!-- DERECHOS -->
            <div class="info-card" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);">
                <h3 style="color: #2E7D32;">✓ Tus Derechos</h3>
                <ul style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
                    <li><strong>Derecho a la información</strong> sobre recursos disponibles y estado de tu caso</li>
                    <li><strong>Derecho a la protección</strong> policial y medidas de seguridad</li>
                    <li><strong>Derecho a la asistencia</strong> social integral</li>
                    <li><strong>Derecho a la asistencia jurídica</strong> gratuita</li>
                    <li><strong>Derechos laborales</strong> (reducción jornada, cambio de centro, excedencia)</li>
                    <li><strong>Ayudas económicas</strong> si cumples los requisitos</li>
                    <li><strong>Prioridad en vivienda</strong> de protección oficial</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
