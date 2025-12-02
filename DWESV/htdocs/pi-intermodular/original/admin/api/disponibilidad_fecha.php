<?php
// Desactivar display de errores para evitar output no deseado
ini_set('display_errors', 0);
error_reporting(0);

// Iniciar buffer de salida para capturar cualquier output no deseado
ob_start();

// Configurar headers para JSON primero
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Validar que se proporcionó una fecha primero
    if (!isset($_GET['fecha']) || empty($_GET['fecha'])) {
        throw new Exception('Fecha requerida');
    }

    $fecha = trim($_GET['fecha']);
    
    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Formato de fecha inválido: ' . $fecha);
    }

    // Verificar que la fecha sea válida
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
        throw new Exception('Fecha inválida: ' . $fecha);
    }

    // Incluir archivos necesarios
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/functions.php';
    require_once __DIR__ . '/../../config/database.php';

    // Verificar autenticación
    $auth = new Auth();
    
    if (!$auth->isLoggedIn()) {
        throw new Exception('No autenticado');
    }
    
    if ($auth->getUserType() !== 'administrador') {
        throw new Exception('Permisos insuficientes - Tipo: ' . $auth->getUserType());
    }

    // Conectar a la base de datos
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Consultar disponibilidad de todos los árbitros para la fecha específica
    $query = "SELECT 
                a.id,
                a.nombre,
                a.apellidos,
                a.ciudad,
                a.licencia,
                a.numero_licencia,
                COALESCE(da.manana, NULL) as manana,
                COALESCE(da.tarde, NULL) as tarde,
                da.observacion_manana,
                da.observacion_tarde,
                CASE 
                    WHEN COALESCE(da.manana,0) = 1 OR COALESCE(da.tarde,0) = 1 THEN 'Disponible'
                    WHEN COALESCE(da.manana,0) = 0 AND COALESCE(da.tarde,0) = 0 AND da.fecha IS NOT NULL THEN 'No Disponible'
                    ELSE 'Sin Información'
                END as estado_texto,
                CASE 
                    WHEN COALESCE(da.manana,0) = 1 OR COALESCE(da.tarde,0) = 1 THEN 'success'
                    WHEN COALESCE(da.manana,0) = 0 AND COALESCE(da.tarde,0) = 0 AND da.fecha IS NOT NULL THEN 'danger'
                    ELSE 'secondary'
                END as estado_color
              FROM arbitros a
              INNER JOIN usuarios u ON a.usuario_id = u.id
              LEFT JOIN disponibilidad_arbitros da ON a.id = da.arbitro_id AND da.fecha = ?
              WHERE u.activo = 1
              ORDER BY 
                estado_color DESC,
                a.nombre, 
                a.apellidos";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar consulta: ' . implode(' - ', $conn->errorInfo()));
    }
    
    $result = $stmt->execute([$fecha]);
    if (!$result) {
        throw new Exception('Error al ejecutar consulta: ' . implode(' - ', $stmt->errorInfo()));
    }
    
    $arbitros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar estadísticas
    $total_arbitros = count($arbitros);
    $disponibles = count(array_filter($arbitros, function($a) { return (isset($a['manana']) && $a['manana'] == 1) || (isset($a['tarde']) && $a['tarde'] == 1); }));
    $no_disponibles = count(array_filter($arbitros, function($a) { return (isset($a['manana']) && $a['manana'] == 0) && (isset($a['tarde']) && $a['tarde'] == 0); }));
    $sin_info = count(array_filter($arbitros, function($a) { return (!isset($a['manana']) && !isset($a['tarde'])); }));

    // Formatear fecha para mostrar
    $fechaFormateada = $fechaObj->format('d/m/Y');
    $diaSemana = $fechaObj->format('l');
    $diasSemana = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes', 
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
    $diaSemanaES = $diasSemana[$diaSemana] ?? $diaSemana;

    // Formatear observaciones para HTML y procesar datos
    foreach ($arbitros as &$arbitro) {
        // Normalizar observaciones por franja
        $arbitro['observaciones_html'] = '';
        if (!empty($arbitro['observacion_manana'])) {
            $arbitro['observaciones_html'] .= '<strong>Mañana:</strong> ' . nl2br(htmlspecialchars($arbitro['observacion_manana'])) . '<br/>';
        }
        if (!empty($arbitro['observacion_tarde'])) {
            $arbitro['observaciones_html'] .= '<strong>Tarde:</strong> ' . nl2br(htmlspecialchars($arbitro['observacion_tarde']));
        }
        
        // Formatear nombre de licencia
        $licencias = [
            'n3a' => 'Nacional 3A',
            'n3b' => 'Nacional 3B', 
            'n3c' => 'Nacional 3C',
            'n2' => 'Nacional 2',
            'n1' => 'Nacional 1',
            'anotador' => 'Anotador',
            'colaborador' => 'Colaborador'
        ];
        $arbitro['licencia_texto'] = $licencias[$arbitro['licencia']] ?? ucfirst($arbitro['licencia']);
    }

    $response = [
        'success' => true,
        'fecha' => $fecha,
        'fecha_formateada' => $fechaFormateada,
        'dia_semana' => $diaSemanaES,
        'arbitros' => $arbitros,
        'estadisticas' => [
            'total' => $total_arbitros,
            'disponibles' => $disponibles,
            'no_disponibles' => $no_disponibles,
            'sin_informacion' => $sin_info
        ]
    ];

    // Limpiar cualquier salida previa y enviar solo JSON
    ob_clean();
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    // Finalizar inmediatamente para evitar cualquier output adicional
    ob_end_flush();
    exit();

} catch (Exception $e) {
    // Limpiar salida y enviar error
    ob_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug_info' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'fecha_recibida' => $_GET['fecha'] ?? 'no especificada'
        ]
    ], JSON_UNESCAPED_UNICODE);
    
    // Finalizar inmediatamente
    ob_end_flush();
    exit();
}

// Esta línea no debería ejecutarse nunca debido a los exit() de arriba
ob_end_flush();

error_log("=== FIN disponibilidad_fecha.php ===");

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
