<?php
/**
 * VER VALORACIÓN - DETALLE COMPLETO
 * Muestra todos los detalles de una valoración VPR/VPER específica
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

// Obtener ID de valoración
$valoracion_id = $_GET['id'] ?? null;
if (!$valoracion_id) {
    header('Location: valoraciones_lista.php?error=ID de valoración no especificado');
    exit;
}

// Buscar valoración
global $valoraciones, $victimas, $agresores, $INDICADORES_VPR;
$valoracion = $valoraciones[$valoracion_id] ?? null;

if (!$valoracion) {
    header('Location: valoraciones_lista.php?error=Valoración no encontrada');
    exit;
}

// Obtener datos relacionados
$victima = $victimas[$valoracion['victima_id']] ?? null;
$agresor = $agresores[$valoracion['agresor_id']] ?? null;
$nombre_nivel = obtenerNombreNivel($valoracion['nivel_riesgo']);

// Contar indicadores marcados por peso
$contadores = [
    'CRITICO' => 0,
    'ALTO' => 0,
    'MEDIO' => 0,
    'BAJO' => 0,
    'POSITIVO' => 0
];

foreach ($valoracion['indicadores'] as $key => $valor) {
    if ($valor === 'SI' && isset($INDICADORES_VPR[$key])) {
        $peso = $INDICADORES_VPR[$key]['peso'];
        $contadores[$peso]++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Detalle Valoración <?php echo htmlspecialchars($valoracion_id); ?></title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .valoracion-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .valoracion-header h2 {
            margin: 0 0 0.5rem 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            color: #D32F2F;
            font-size: 1rem;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .info-label {
            font-weight: bold;
            color: #757575;
        }
        .resultado-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
            text-align: center;
        }
        .puntuacion-display {
            font-size: 4rem;
            font-weight: bold;
            color: #333;
            margin: 1rem 0;
        }
        .nivel-display {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .indicadores-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .indicadores-section h3 {
            color: #D32F2F;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .indicador-item {
            background: #F5F5F5;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .indicador-item.marcado {
            background: #E8F5E9;
            border-left: 4px solid #4CAF50;
        }
        .indicador-item.marcado.critico {
            background: #FFEBEE;
            border-left-color: #D32F2F;
        }
        .indicador-item.marcado.alto {
            background: #FBE9E7;
            border-left-color: #FF5722;
        }
        .indicador-item.marcado.medio {
            background: #FFF3E0;
            border-left-color: #FF9800;
        }
        .indicador-item.marcado.bajo {
            background: #FFFDE7;
            border-left-color: #FFC107;
        }
        .indicador-check {
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .indicador-texto {
            flex: 1;
        }
        .indicador-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            white-space: nowrap;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .stat-box {
            text-align: center;
            padding: 1rem;
            background: #F5F5F5;
            border-radius: 6px;
        }
        .stat-num {
            font-size: 2rem;
            font-weight: bold;
        }
        .observaciones-box {
            background: #FFF9C4;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #FFC107;
            margin-bottom: 1.5rem;
        }
        .medidas-box {
            background: #E3F2FD;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #2196F3;
            margin-bottom: 1.5rem;
        }
        .print-btn {
            background: white;
            border: 2px solid #333;
            color: #333;
        }
        .print-btn:hover {
            background: #333;
            color: white;
        }
        @media print {
            .sidebar, .header-bar, .print-btn { display: none; }
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
                <h1>📋 Detalle de Valoración</h1>
                <div>
                    <button onclick="window.print()" class="btn print-btn">🖨️ Imprimir</button>
                    <a href="valoraciones_lista.php" class="btn btn-secondary">← Volver</a>
                </div>
            </div>

            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
            <?php endif; ?>

            <!-- CABECERA -->
            <div class="valoracion-header">
                <h2>Valoración <?php echo htmlspecialchars($valoracion['tipo']); ?> - ID: <?php echo htmlspecialchars($valoracion_id); ?></h2>
                <p style="margin: 0;">Fecha: <?php echo date('d/m/Y H:i', strtotime($valoracion['fecha_valoracion'])); ?></p>
                <p style="margin: 0;">Evaluador: <?php echo htmlspecialchars($valoracion['evaluador_id']); ?></p>
            </div>

            <!-- RESULTADO PRINCIPAL -->
            <div class="resultado-card">
                <h2 style="margin-top: 0;">📊 Resultado de la Valoración</h2>
                <div class="puntuacion-display"><?php echo $valoracion['puntuacion_total']; ?></div>
                <p style="font-size: 1.25rem; color: #757575; margin: 0;">puntos totales</p>
                
                <div class="nivel-display nivel-<?php echo strtolower($nombre_nivel); ?>">
                    NIVEL DE RIESGO: <?php echo strtoupper($nombre_nivel); ?>
                </div>

                <!-- ESTADÍSTICAS DE INDICADORES -->
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-num" style="color: #D32F2F;"><?php echo $contadores['CRITICO']; ?></div>
                        <div>CRÍTICO</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #FF5722;"><?php echo $contadores['ALTO']; ?></div>
                        <div>ALTO</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #FF9800;"><?php echo $contadores['MEDIO']; ?></div>
                        <div>MEDIO</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #FFC107;"><?php echo $contadores['BAJO']; ?></div>
                        <div>BAJO</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #4CAF50;"><?php echo $contadores['POSITIVO']; ?></div>
                        <div>POSITIVO</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num"><?php echo array_sum($contadores); ?></div>
                        <div>TOTAL</div>
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN GENERAL -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>👤 Víctima</h3>
                    <?php if ($victima): ?>
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span><strong><?php echo htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']); ?></strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Documento:</span>
                            <span><?php echo htmlspecialchars($victima['num_documento']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Edad:</span>
                            <span><?php echo calcularEdad($victima['fecha_nacimiento']); ?> años</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span><?php echo htmlspecialchars($victima['telefono'] ?? 'No registrado'); ?></span>
                        </div>
                        <a href="ver_victima.php?id=<?php echo $victima['id']; ?>" class="btn btn-primary" style="margin-top: 1rem; display: block; text-align: center;">
                            Ver Ficha Completa
                        </a>
                    <?php else: ?>
                        <p><em>Información de víctima no disponible</em></p>
                    <?php endif; ?>
                </div>

                <div class="info-card">
                    <h3>⚠️ Agresor</h3>
                    <?php if ($agresor): ?>
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span><strong><?php echo htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']); ?></strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Documento:</span>
                            <span><?php echo htmlspecialchars($agresor['num_documento'] ?? 'No registrado'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Edad:</span>
                            <span><?php echo calcularEdad($agresor['fecha_nacimiento'] ?? null); ?> años</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Relación:</span>
                            <span><?php echo htmlspecialchars($agresor['relacion_con_victima'] ?? 'No especificada'); ?></span>
                        </div>
                        <a href="ver_agresor.php?id=<?php echo $agresor['id']; ?>" class="btn btn-danger" style="margin-top: 1rem; display: block; text-align: center;">
                            Ver Ficha Completa
                        </a>
                    <?php else: ?>
                        <p><em>Información de agresor no disponible</em></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <?php if (!empty($valoracion['observaciones_evaluador'])): ?>
            <div class="observaciones-box">
                <h3 style="margin-top: 0;">📝 Observaciones del Evaluador</h3>
                <p><?php echo nl2br(htmlspecialchars($valoracion['observaciones_evaluador'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- MEDIDAS RECOMENDADAS -->
            <?php if (!empty($valoracion['medidas_recomendadas'])): ?>
            <div class="medidas-box">
                <h3 style="margin-top: 0;">🛡️ Medidas de Protección Recomendadas</h3>
                <p><?php echo nl2br(htmlspecialchars($valoracion['medidas_recomendadas'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- DETALLE DE INDICADORES -->
            <?php 
            // Organizar indicadores por factores
            $factores = [
                'FACTOR 1: Gravedad del Episodio Actual' => range(1, 8),
                'FACTOR 2: Historial de Violencia' => range(9, 14),
                'FACTOR 3: Perfil del Agresor' => range(15, 23),
                'FACTOR 4: Vulnerabilidad de la Víctima' => range(24, 30),
                'FACTOR 5: Factores Protectores' => range(31, 35)
            ];

            foreach ($factores as $nombre_factor => $numeros): ?>
            <div class="indicadores-section">
                <h3><?php echo htmlspecialchars($nombre_factor); ?></h3>
                
                <?php foreach ($numeros as $num): 
                    $key = "indicador_$num";
                    $marcado = ($valoracion['indicadores'][$key] ?? 'NO') === 'SI';
                    $indicador = $INDICADORES_VPR[$key] ?? null;
                    
                    if (!$indicador) continue;
                    
                    $peso = $indicador['peso'];
                    $clase_peso = strtolower($peso);
                ?>
                <div class="indicador-item <?php echo $marcado ? "marcado $clase_peso" : ''; ?>">
                    <div class="indicador-check">
                        <?php echo $marcado ? '✅' : '⬜'; ?>
                    </div>
                    <div class="indicador-texto">
                        <strong><?php echo $num; ?>.</strong>
                        <?php echo htmlspecialchars($indicador['texto']); ?>
                        <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>

            <!-- ACCIONES -->
            <div class="info-card" style="text-align: center; padding: 2rem;">
                <h3>Acciones Disponibles</h3>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
                    <button onclick="window.print()" class="btn print-btn">🖨️ Imprimir Informe</button>
                    <a href="nueva_valoracion.php?victima_id=<?php echo $valoracion['victima_id']; ?>" class="btn btn-primary">
                        ➕ Nueva Valoración (VPER)
                    </a>
                    <a href="valoraciones_lista.php" class="btn btn-secondary">← Volver a Lista</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
