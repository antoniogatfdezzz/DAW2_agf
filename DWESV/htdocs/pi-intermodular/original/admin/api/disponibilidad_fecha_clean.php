<?php
// API limpia para disponibilidad por fecha - Sin output buffer
ini_set('display_errors', 0);
error_reporting(0);

// Headers primero
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Función para enviar respuesta y terminar
function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

try {
    // Validar fecha
    if (!isset($_GET['fecha']) || empty($_GET['fecha'])) {
        sendJsonResponse(['success' => false, 'error' => 'Fecha requerida'], 400);
    }

    $fecha = trim($_GET['fecha']);
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        sendJsonResponse(['success' => false, 'error' => 'Formato de fecha inválido'], 400);
    }

    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
        sendJsonResponse(['success' => false, 'error' => 'Fecha inválida'], 400);
    }

    // Incluir archivos
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/functions.php';
    require_once __DIR__ . '/../../config/database.php';

    // Autenticación
    $auth = new Auth();
    
    if (!$auth->isLoggedIn()) {
        sendJsonResponse(['success' => false, 'error' => 'No autenticado'], 401);
    }
    
    if ($auth->getUserType() !== 'administrador') {
        sendJsonResponse(['success' => false, 'error' => 'Permisos insuficientes'], 403);
    }

    // Base de datos
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        sendJsonResponse(['success' => false, 'error' => 'Error de conexión a BD'], 500);
    }

        // Query principal - Mostrar árbitros que tienen alguna franja disponible (mañana o tarde)
        $query = "SELECT 
                                a.id,
                                a.nombre,
                                a.apellidos,
                                a.ciudad,
                                a.licencia,
                                a.numero_licencia,
                                COALESCE(da.manana,0) as manana,
                                COALESCE(da.tarde,0) as tarde,
                                da.observacion_manana,
                                da.observacion_tarde
                            FROM arbitros a
                            INNER JOIN usuarios u ON a.usuario_id = u.id
                            INNER JOIN disponibilidad_arbitros da ON a.id = da.arbitro_id AND da.fecha = ?
                            WHERE u.activo = 1 AND (COALESCE(da.manana,0) = 1 OR COALESCE(da.tarde,0) = 1)
                            ORDER BY 
                                a.nombre, 
                                a.apellidos";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        sendJsonResponse(['success' => false, 'error' => 'Error preparando consulta'], 500);
    }
    
    if (!$stmt->execute([$fecha])) {
        sendJsonResponse(['success' => false, 'error' => 'Error ejecutando consulta'], 500);
    }
    
    $arbitros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estadísticas - Solo árbitros con alguna franja disponible
    $total_arbitros = count($arbitros);
    $disponibles = $total_arbitros; // Todos los que se muestran están disponibles en alguna franja
    $con_observaciones = 0;

    foreach ($arbitros as &$arbitro) {
        // Contar los que tienen observaciones en cualquiera de las franjas
        if (!empty($arbitro['observacion_manana']) || !empty($arbitro['observacion_tarde'])) {
            $con_observaciones++;
        }
        
        // Formatear datos de observaciones
        $arbitro['observaciones_html'] = '';
        if (!empty($arbitro['observacion_manana'])) {
            $arbitro['observaciones_html'] .= '<strong>Mañana:</strong> ' . nl2br(htmlspecialchars($arbitro['observacion_manana'])) . '<br/>';
        }
        if (!empty($arbitro['observacion_tarde'])) {
            $arbitro['observaciones_html'] .= '<strong>Tarde:</strong> ' . nl2br(htmlspecialchars($arbitro['observacion_tarde']));
        }
        
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

    // Formatear fecha
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

    // Respuesta final
    $response = [
        'success' => true,
        'fecha' => $fecha,
        'fecha_formateada' => $fechaFormateada,
        'dia_semana' => $diaSemanaES,
        'arbitros' => $arbitros,
        'estadisticas' => [
            'total_disponibles' => $total_arbitros,
            'con_observaciones' => $con_observaciones,
            'sin_observaciones' => $total_arbitros - $con_observaciones
        ]
    ];

    sendJsonResponse($response);

} catch (Exception $e) {
    sendJsonResponse([
        'success' => false,
        'error' => $e->getMessage(),
        'debug_info' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>