<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

try {
    if (!isset($_GET['arbitro_id'])) {
        throw new Exception('ID de árbitro no proporcionado');
    }

    $arbitro_id = intval($_GET['arbitro_id']);
    
    // === INFORMACIÓN BÁSICA DEL ÁRBITRO ===
    $query = "SELECT a.*, u.email, u.fecha_creacion as fecha_registro
              FROM arbitros a 
              JOIN usuarios u ON a.usuario_id = u.id
              WHERE a.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $arbitro_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$arbitro_info) {
        throw new Exception('Árbitro no encontrado');
    }

    // === ESTADÍSTICAS DE PARTIDOS ===
    $query = "SELECT 
                COUNT(CASE WHEN p.arbitro_principal_id = ? THEN 1 END) as como_principal,
                COUNT(CASE WHEN p.arbitro_segundo_id = ? THEN 1 END) as como_segundo,
                COUNT(CASE WHEN p.anotador_id = ? THEN 1 END) as como_anotador,
                COUNT(CASE WHEN (p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?) 
                           AND p.estado = 'finalizado' THEN 1 END) as partidos_finalizados,
                COUNT(CASE WHEN (p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?) 
                           AND p.estado = 'programado' THEN 1 END) as partidos_pendientes,
                COUNT(CASE WHEN (p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?) 
                           AND p.estado = 'cancelado' THEN 1 END) as partidos_cancelados,
                COUNT(CASE WHEN (p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?) 
                           AND YEAR(p.fecha) = YEAR(CURDATE()) THEN 1 END) as partidos_temporada_actual,
                MIN(p.fecha) as primer_partido,
                MAX(p.fecha) as ultimo_partido
              FROM partidos p
              WHERE p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $arbitro_id, $arbitro_id, $arbitro_id, // como_principal, como_segundo, como_anotador
        $arbitro_id, $arbitro_id, $arbitro_id, // partidos_finalizados
        $arbitro_id, $arbitro_id, $arbitro_id, // partidos_pendientes
        $arbitro_id, $arbitro_id, $arbitro_id, // partidos_cancelados
        $arbitro_id, $arbitro_id, $arbitro_id, // partidos_temporada_actual
        $arbitro_id, $arbitro_id, $arbitro_id  // MIN/MAX fechas
    ]);
    $stats_partidos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stats_partidos['total_partidos'] = $stats_partidos['como_principal'] + 
                                       $stats_partidos['como_segundo'] + 
                                       $stats_partidos['como_anotador'];

    // === HISTÓRICO DE LICENCIAS ===
    $query = "SELECT l.*, 
                'activa' as estado_licencia,
                0 as dias_hasta_vencimiento
              FROM licencias_arbitros l
              WHERE l.arbitro_id = ?
              ORDER BY l.fecha_creacion DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $licencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // === LIQUIDACIONES TOTALES ===
    $query = "SELECT 
                COUNT(*) as total_liquidaciones,
                COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
                COUNT(CASE WHEN estado = 'aprobada' THEN 1 END) as aprobadas,
                COUNT(CASE WHEN estado = 'pagada' THEN 1 END) as pagadas,
                COUNT(CASE WHEN estado = 'rectificacion' THEN 1 END) as en_rectificacion,
                SUM(numero_partidos) as total_partidos_liquidados,
                COALESCE(SUM(CASE WHEN estado = 'pagada' THEN 
                    (SELECT COALESCE(SUM(lp.importe_partido + lp.importe_dieta + lp.importe_kilometraje), 0) 
                     FROM liquidaciones_partidos lp WHERE lp.liquidacion_id = l.id)
                END), 0) as total_cobrado
              FROM liquidaciones l
              WHERE l.arbitro_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $stats_liquidaciones = $stmt->fetch(PDO::FETCH_ASSOC);

    // === ÚLTIMOS PARTIDOS DETALLADOS ===
    $query = "SELECT p.id, p.fecha, p.estado,
                     p.equipo_local,
                     p.equipo_visitante,
                     c.nombre as categoria,
                     p.pabellon_nombre as pabellon,
                     CASE 
                         WHEN p.arbitro_principal_id = ? THEN '1º Árbitro'
                         WHEN p.arbitro_segundo_id = ? THEN '2º Árbitro'
                         WHEN p.anotador_id = ? THEN 'Anotador'
                     END as rol,
                     p.sets_local,
                     p.sets_visitante
              FROM partidos p
              LEFT JOIN categorias c ON p.categoria_id = c.id
              WHERE p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?
              ORDER BY p.fecha DESC
              LIMIT 15";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id]);
    $ultimos_partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // === ESTADÍSTICAS DE DISPONIBILIDAD ===
        $query = "SELECT 
                                COUNT(*) as total_dias_configurados,
                                SUM(CASE WHEN (COALESCE(manana,0)=1 OR COALESCE(tarde,0)=1) THEN 1 ELSE 0 END) as dias_disponibles,
                                SUM(CASE WHEN (COALESCE(manana,0)=0 AND COALESCE(tarde,0)=0) THEN 1 ELSE 0 END) as dias_no_disponibles,
                                SUM(CASE WHEN (COALESCE(manana,0)=1 OR COALESCE(tarde,0)=1) AND fecha >= CURDATE() THEN 1 ELSE 0 END) as dias_disponibles_futuros,
                                SUM(CASE WHEN ( (observacion_manana IS NOT NULL AND observacion_manana != '') OR (observacion_tarde IS NOT NULL AND observacion_tarde != '') ) THEN 1 ELSE 0 END) as dias_con_observaciones
                            FROM disponibilidad_arbitros
                            WHERE arbitro_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $stats_disponibilidad = $stmt->fetch(PDO::FETCH_ASSOC);

    // === ÚLTIMAS LIQUIDACIONES ===
    $query = "SELECT l.*, 
                COALESCE(SUM(lp.importe_partido + lp.importe_dieta + lp.importe_kilometraje), 0) as importe_total
              FROM liquidaciones l
              LEFT JOIN liquidaciones_partidos lp ON l.id = lp.liquidacion_id
              WHERE l.arbitro_id = ?
              GROUP BY l.id
              ORDER BY l.fecha_creacion DESC
              LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $ultimas_liquidaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // === ESTADÍSTICAS POR CATEGORÍA ===
    $query = "SELECT c.nombre as categoria,
                COUNT(*) as partidos,
                COUNT(CASE WHEN p.arbitro_principal_id = ? THEN 1 END) as como_principal,
                COUNT(CASE WHEN p.arbitro_segundo_id = ? THEN 1 END) as como_segundo,
                COUNT(CASE WHEN p.anotador_id = ? THEN 1 END) as como_anotador
              FROM partidos p
              JOIN categorias c ON p.categoria_id = c.id
              WHERE p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?
              GROUP BY c.id, c.nombre
              ORDER BY partidos DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id]);
    $stats_por_categoria = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // === ESTADÍSTICAS POR AÑO ===
    $query = "SELECT YEAR(p.fecha) as año,
                COUNT(*) as partidos,
                COUNT(CASE WHEN p.estado = 'finalizado' THEN 1 END) as finalizados,
                COUNT(CASE WHEN p.arbitro_principal_id = ? THEN 1 END) as como_principal,
                COUNT(CASE WHEN p.arbitro_segundo_id = ? THEN 1 END) as como_segundo,
                COUNT(CASE WHEN p.anotador_id = ? THEN 1 END) as como_anotador
              FROM partidos p
              WHERE p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?
              GROUP BY YEAR(p.fecha)
              ORDER BY año DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id, $arbitro_id]);
    $stats_por_año = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // === CONSTRUIR RESPUESTA COMPLETA ===
    $response = [
        'arbitro_info' => $arbitro_info,
        'estadisticas_partidos' => $stats_partidos,
        'licencias' => $licencias,
        'estadisticas_liquidaciones' => $stats_liquidaciones,
        'estadisticas_disponibilidad' => $stats_disponibilidad,
        'ultimos_partidos' => $ultimos_partidos,
        'ultimas_liquidaciones' => $ultimas_liquidaciones,
        'estadisticas_por_categoria' => $stats_por_categoria,
        'estadisticas_por_año' => $stats_por_año,
        'resumen' => [
            'total_partidos' => $stats_partidos['total_partidos'],
            'partidos_temporada_actual' => $stats_partidos['partidos_temporada_actual'],
            'total_liquidaciones' => $stats_liquidaciones['total_liquidaciones'],
            'total_cobrado' => number_format($stats_liquidaciones['total_cobrado'], 2),
            'licencias_activas' => count(array_filter($licencias, function($l) { return $l['estado_licencia'] === 'activa'; })),
            'licencias_vencidas' => count(array_filter($licencias, function($l) { return $l['estado_licencia'] === 'vencida'; })),
            'dias_disponibles_futuros' => $stats_disponibilidad['dias_disponibles_futuros'],
            'años_arbitrando' => count($stats_por_año)
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error en estadísticas árbitro: " . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => 'STATS_ERROR'
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
