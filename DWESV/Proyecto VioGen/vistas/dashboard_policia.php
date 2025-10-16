<?php
/**
 * DASHBOARD POLICÍA
 * Panel de control para evaluadores policiales
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/policias.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/agresores.php';
require_once __DIR__ . '/../modelos/valoraciones.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener estadísticas
$victimas = obtenerTodasVictimas();
$agresores = obtenerTodosAgresores();
$valoraciones = obtenerTodasValoraciones();

$total_victimas = count($victimas);
$total_agresores = count($agresores);
$total_valoraciones = count($valoraciones);

// Contar por niveles de riesgo
$niveles = [
    NIVEL_NO_APRECIADO => 0,
    NIVEL_BAJO => 0,
    NIVEL_MEDIO => 0,
    NIVEL_ALTO => 0,
    NIVEL_EXTREMO => 0
];

foreach ($valoraciones as $val) {
    if (isset($niveles[$val['nivel_final']])) {
        $niveles[$val['nivel_final']]++;
    }
}

// Valoraciones pendientes de revisión
$pendientes = obtenerValoracionesPendientesRevision();
$total_pendientes = count($pendientes);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Dashboard Policía</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="dashboard">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <div class="user-info" style="text-align: center; margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.1); border-radius: 8px;">
                <p><strong><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellidos']); ?></strong></p>
                <p style="font-size: 0.85rem; opacity: 0.9;"><?php echo htmlspecialchars($_SESSION['unidad_policial']); ?></p>
            </div>
            
            <nav>
                <ul>
                    <li><a href="dashboard_policia.php" class="active">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="pendientes.php">⏰ Casos Pendientes</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <li><a href="ayuda.php">❓ Ayuda</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="header-bar">
                <h1>Dashboard - Sistema VioGén</h1>
                <div>
                    <span style="color: #666;"><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>

            <!-- Alertas de casos pendientes -->
            <?php if ($total_pendientes > 0): ?>
            <div class="alert alert-danger">
                <strong>⚠️ Atención: Valoraciones pendientes de revisión</strong>
                <p>Hay <?php echo $total_pendientes; ?> caso(s) que requieren nueva valoración VPER.</p>
                <a href="pendientes.php" class="btn btn-danger" style="margin-top: 0.5rem;">Ver casos pendientes</a>
            </div>
            <?php endif; ?>

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-4">
                <div class="card" style="border-left: 4px solid #2196F3;">
                    <h3 style="color: #2196F3;">👥 Víctimas</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_victimas; ?></p>
                    <a href="victimas_lista.php">Ver todas →</a>
                </div>

                <div class="card" style="border-left: 4px solid #FF5722;">
                    <h3 style="color: #FF5722;">⚠️ Agresores</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_agresores; ?></p>
                    <a href="agresores_lista.php">Ver todos →</a>
                </div>

                <div class="card" style="border-left: 4px solid #4CAF50;">
                    <h3 style="color: #4CAF50;">📋 Valoraciones</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_valoraciones; ?></p>
                    <a href="valoraciones_lista.php">Ver todas →</a>
                </div>

                <div class="card" style="border-left: 4px solid #D32F2F;">
                    <h3 style="color: #D32F2F;">🚨 Pendientes</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_pendientes; ?></p>
                    <a href="pendientes.php">Revisar →</a>
                </div>
            </div>

            <!-- Distribución por niveles de riesgo -->
            <div class="card">
                <div class="card-header">
                    <h3>📊 Distribución por Nivel de Riesgo</h3>
                </div>

                <div class="grid grid-5">
                    <?php
                    global $NIVELES_RIESGO;
                    foreach ($NIVELES_RIESGO as $nivel => $info):
                        $cantidad = $niveles[$nivel];
                        $porcentaje = $total_valoraciones > 0 ? round(($cantidad / $total_valoraciones) * 100, 1) : 0;
                    ?>
                    <div class="card" style="text-align: center; background: <?php echo $info['color']; ?>; color: <?php echo $nivel == NIVEL_MEDIO ? '#333' : 'white'; ?>;">
                        <h4><?php echo $info['nombre']; ?></h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;"><?php echo $cantidad; ?></p>
                        <p style="font-size: 0.9rem; opacity: 0.9;"><?php echo $porcentaje; ?>%</p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Casos de alto riesgo -->
            <?php
            $casos_alto_riesgo = array_filter($valoraciones, function($v) {
                return $v['nivel_final'] >= NIVEL_ALTO && $v['activo'];
            });
            ?>

            <?php if (count($casos_alto_riesgo) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h3>🚨 Casos de Alto Riesgo / Extremo</h3>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Víctima</th>
                            <th>Agresor</th>
                            <th>Nivel</th>
                            <th>Fecha Valoración</th>
                            <th>Próxima VPER</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($casos_alto_riesgo, 0, 5) as $val): 
                            $victima = buscarVictimaPorId($val['id_victima']);
                            $agresor = buscarAgresorPorId($val['id_agresor']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($val['id']); ?></td>
                            <td><?php echo $victima ? htmlspecialchars($victima['nombre'] . ' ' . $victima['apellidos']) : 'N/A'; ?></td>
                            <td><?php echo $agresor ? htmlspecialchars($agresor['nombre'] . ' ' . $agresor['apellidos']) : 'N/A'; ?></td>
                            <td>
                                <span class="badge-nivel nivel-<?php echo $val['nivel_final'] == NIVEL_ALTO ? 'alto' : 'extremo'; ?>">
                                    <?php echo obtenerNombreNivel($val['nivel_final']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($val['fecha_hora_valoracion'])); ?></td>
                            <td><?php echo $val['fecha_proxima_vper'] ? date('d/m/Y', strtotime($val['fecha_proxima_vper'])) : 'N/A'; ?></td>
                            <td>
                                <a href="ver_valoracion.php?id=<?php echo $val['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Ver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header">
                    <h3>⚡ Acciones Rápidas</h3>
                </div>

                <div class="grid grid-3">
                    <a href="nueva_valoracion.php" class="btn btn-primary">
                        ➕ Nueva Valoración VPR
                    </a>
                    <a href="registrar_victima.php" class="btn btn-success">
                        ✏️ Registrar Víctima
                    </a>
                    <a href="registrar_agresor.php" class="btn btn-warning">
                        ⚠️ Registrar Agresor
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
