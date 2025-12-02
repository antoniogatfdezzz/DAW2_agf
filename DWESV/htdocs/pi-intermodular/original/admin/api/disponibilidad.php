<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // CONSULTAS (GET)
        if (isset($_GET['fecha'])) {
        // Obtener árbitros disponibles para una fecha específica
        $fecha = sanitize_input($_GET['fecha']);
        
        // Debug
        error_log("=== ADMIN API: Consulta por fecha ===");
        error_log("Fecha solicitada: $fecha");
        error_log("Fecha de hoy: " . date('Y-m-d'));
        error_log("=====================================");
        
        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
        }
        
        // Verificar que la fecha no sea anterior a hoy
        if ($fecha < date('Y-m-d')) {
            throw new Exception('No se puede consultar disponibilidad para fechas pasadas');
        }
        
     $query = "SELECT a.id, a.nombre, a.apellidos, 
                CONCAT(a.nombre, ' ', a.apellidos) as nombre_completo,
                COALESCE(da.manana, 0) as manana,
                COALESCE(da.tarde, 0) as tarde,
                da.observacion_manana,
                da.observacion_tarde,
                a.licencia,
                a.ciudad
            FROM arbitros a
            INNER JOIN usuarios u ON a.usuario_id = u.id
            LEFT JOIN disponibilidad_arbitros da ON a.id = da.arbitro_id AND da.fecha = ?
            WHERE u.activo = 1
            ORDER BY a.nombre, a.apellidos";
        $stmt = $conn->prepare($query);
        $stmt->execute([$fecha]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar información adicional para debug
        $response = [
            'fecha_consultada' => $fecha,
            'total_arbitros' => count($result),
            'disponibles' => array_filter($result, function($a) { return (isset($a['manana']) && $a['manana']==1) || (isset($a['tarde']) && $a['tarde']==1); }),
            'no_disponibles' => array_filter($result, function($a) { return (isset($a['manana']) && $a['manana']==0) && (isset($a['tarde']) && $a['tarde']==0); }),
            'arbitros' => $result
        ];
        
        echo json_encode($result); // Mantener compatibilidad con el código existente
        
    } elseif (isset($_GET['arbitro_id'])) {
        // Obtener disponibilidad de un árbitro específico
        $arbitro_id = (int)$_GET['arbitro_id'];
        
        if (isset($_GET['month'])) {
            // Obtener disponibilidad para un mes específico
            $month = sanitize_input($_GET['month']); // Formato: YYYY-MM
            
            // Debug
            error_log("=== ADMIN API: Consulta por mes ===");
            error_log("Arbitro ID: $arbitro_id");
            error_log("Mes solicitado: $month");
            
            // Validar formato del mes
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                throw new Exception('Formato de mes inválido. Use YYYY-MM');
            }
            
            list($year, $monthNum) = explode('-', $month);
            $firstDay = $month . '-01';
            $lastDay = date('Y-m-t', strtotime($firstDay));
            
            error_log("Rango de fechas: $firstDay a $lastDay");
            error_log("==================================");
            
            // Obtener disponibilidad del árbitro
            $query = "SELECT fecha, 
                             COALESCE(manana, 0) as manana, 
                             COALESCE(tarde, 0) as tarde, 
                             COALESCE(observacion_manana, '') as observacion_manana,
                             COALESCE(observacion_tarde, '') as observacion_tarde
                      FROM disponibilidad_arbitros 
                      WHERE arbitro_id = ? AND fecha BETWEEN ? AND ?
                      ORDER BY fecha";
            $stmt = $conn->prepare($query);
            $stmt->execute([$arbitro_id, $firstDay, $lastDay]);
            $disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Crear un mapa de disponibilidad por fecha
            $disponibilidadMap = [];
            foreach ($disponibilidad as $disp) {
                $disponibilidadMap[$disp['fecha']] = $disp;
            }
            
            error_log("Registros de disponibilidad encontrados: " . count($disponibilidad));
            
            // Generar calendario del mes
            $calendar = [];
            $firstDayOfMonth = new DateTime($firstDay);
            $lastDayOfMonth = new DateTime($lastDay);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            // Obtener el día de la semana del primer día (1=Lun, 7=Dom)
            $startDayOfWeek = (int)$firstDayOfMonth->format('N');
            
            // Agregar días del mes anterior si es necesario
            if ($startDayOfWeek > 1) {
                $prevMonthDate = clone $firstDayOfMonth;
                $prevMonthDate->modify('-' . ($startDayOfWeek - 1) . ' days');
                
                for ($i = 0; $i < $startDayOfWeek - 1; $i++) {
                    $dateStr = $prevMonthDate->format('Y-m-d');
                    $calendar[] = [
                        'date' => $dateStr,
                        'day' => (int)$prevMonthDate->format('d'),
                        'isOtherMonth' => true,
                        'isToday' => false,
                        'manana' => 0,
                        'tarde' => 0,
                        'observaciones' => ''
                    ];
                    $prevMonthDate->modify('+1 day');
                }
            }
            
            // Agregar días del mes actual
            $currentDate = clone $firstDayOfMonth;
            while ($currentDate <= $lastDayOfMonth) {
                $dateStr = $currentDate->format('Y-m-d');
                $isToday = $currentDate->format('Y-m-d') === $today->format('Y-m-d');
                
                $disp = isset($disponibilidadMap[$dateStr]) ? $disponibilidadMap[$dateStr] : null;
                
                // Combinar observaciones de mañana y tarde
                $observaciones = '';
                if ($disp) {
                    $obs = [];
                    if (!empty($disp['observacion_manana'])) {
                        $obs[] = 'M: ' . $disp['observacion_manana'];
                    }
                    if (!empty($disp['observacion_tarde'])) {
                        $obs[] = 'T: ' . $disp['observacion_tarde'];
                    }
                    $observaciones = implode(' | ', $obs);
                }
                
                $calendar[] = [
                    'date' => $dateStr,
                    'day' => (int)$currentDate->format('d'),
                    'isOtherMonth' => false,
                    'isToday' => $isToday,
                    'manana' => $disp ? (int)$disp['manana'] : 0,
                    'tarde' => $disp ? (int)$disp['tarde'] : 0,
                    'observaciones' => $observaciones
                ];
                
                $currentDate->modify('+1 day');
            }
            
            // Agregar días del mes siguiente si es necesario para completar la última semana
            $remainingDays = 7 - (count($calendar) % 7);
            if ($remainingDays < 7) {
                $nextMonthDate = clone $lastDayOfMonth;
                $nextMonthDate->modify('+1 day');
                
                for ($i = 0; $i < $remainingDays; $i++) {
                    $dateStr = $nextMonthDate->format('Y-m-d');
                    $calendar[] = [
                        'date' => $dateStr,
                        'day' => (int)$nextMonthDate->format('d'),
                        'isOtherMonth' => true,
                        'isToday' => false,
                        'manana' => 0,
                        'tarde' => 0,
                        'observaciones' => ''
                    ];
                    $nextMonthDate->modify('+1 day');
                }
            }
            
            // Devolver respuesta estructurada
            echo json_encode([
                'success' => true,
                'month' => $month,
                'calendar' => $calendar,
                'totalDays' => count($calendar),
                'disponibilidadCount' => count($disponibilidad)
            ]);
            
        } else {
            // Obtener disponibilidad desde hoy hacia adelante (próximos 3 meses)
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-t', strtotime('+3 months'));
            
            $query = "SELECT fecha, 
                             COALESCE(manana, 0) as manana, 
                             COALESCE(tarde, 0) as tarde, 
                             COALESCE(observacion_manana, '') as observacion_manana,
                             COALESCE(observacion_tarde, '') as observacion_tarde
                      FROM disponibilidad_arbitros 
                      WHERE arbitro_id = ? AND fecha BETWEEN ? AND ?
                      ORDER BY fecha";
            $stmt = $conn->prepare($query);
            $stmt->execute([$arbitro_id, $startDate, $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($result);
        }
        
    } else {
        throw new Exception('Parámetros insuficientes. Se requiere fecha o arbitro_id.');
    }
    
} elseif ($method === 'POST') {
    // GUARDAR/ACTUALIZAR DISPONIBILIDAD (POST)
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['arbitro_id']) || !isset($input['fecha'])) {
        throw new Exception('Datos incompletos. Se requiere arbitro_id y fecha.');
    }
    
    $arbitro_id = (int)$input['arbitro_id'];
    $fecha = sanitize_input($input['fecha']);
    $manana = isset($input['manana']) ? (int)$input['manana'] : 0;
    $tarde = isset($input['tarde']) ? (int)$input['tarde'] : 0;
    $observaciones = isset($input['observaciones']) ? sanitize_input($input['observaciones']) : '';
    
    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
    }
    
    // No permitir modificar fechas pasadas
    if ($fecha < date('Y-m-d')) {
        throw new Exception('No se puede modificar disponibilidad de fechas pasadas');
    }
    
    // Verificar si ya existe un registro para esta fecha
    $query = "SELECT id FROM disponibilidad_arbitros WHERE arbitro_id = ? AND fecha = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id, $fecha]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existe) {
        // Actualizar registro existente
        $query = "UPDATE disponibilidad_arbitros 
                  SET manana = ?, tarde = ?, 
                      observacion_manana = ?, 
                      observacion_tarde = ?
                  WHERE arbitro_id = ? AND fecha = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $manana, 
            $tarde, 
            $manana ? $observaciones : '', 
            $tarde ? $observaciones : '', 
            $arbitro_id, 
            $fecha
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Disponibilidad actualizada correctamente',
            'action' => 'update'
        ]);
    } else {
        // Insertar nuevo registro
        $query = "INSERT INTO disponibilidad_arbitros 
                  (arbitro_id, fecha, manana, tarde, observacion_manana, observacion_tarde) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $arbitro_id, 
            $fecha, 
            $manana, 
            $tarde, 
            $manana ? $observaciones : '', 
            $tarde ? $observaciones : ''
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Disponibilidad guardada correctamente',
            'action' => 'insert'
        ]);
    }
    
} elseif ($method === 'DELETE') {
    // ELIMINAR DISPONIBILIDAD (DELETE)
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['arbitro_id']) || !isset($input['fecha'])) {
        throw new Exception('Datos incompletos. Se requiere arbitro_id y fecha.');
    }
    
    $arbitro_id = (int)$input['arbitro_id'];
    $fecha = sanitize_input($input['fecha']);
    
    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
    }
    
    // No permitir eliminar fechas pasadas
    if ($fecha < date('Y-m-d')) {
        throw new Exception('No se puede eliminar disponibilidad de fechas pasadas');
    }
    
    // Eliminar registro
    $query = "DELETE FROM disponibilidad_arbitros WHERE arbitro_id = ? AND fecha = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id, $fecha]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Disponibilidad eliminada correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró disponibilidad para eliminar'
        ]);
    }
    
} else {
    throw new Exception('Método HTTP no soportado');
}
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
