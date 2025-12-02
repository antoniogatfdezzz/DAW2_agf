<?php
// Configurar el manejo de errores ANTES de incluir cualquier archivo
ini_set('display_errors', 0);
error_reporting(0);

// Limpiar cualquier salida previa
ob_start();

// Configurar header JSON
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../config/database.php';

    $auth = new Auth();
    $auth->requireUserType('administrador');

    $database = new Database();
    $conn = $database->getConnection();
    
    // Limpiar buffer de salida
    ob_clean();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    // Obtener detalles de liquidación
    $liquidacion_id = (int)$_GET['id'];
    
    if ($liquidacion_id <= 0) {
        echo json_encode(['error' => 'ID de liquidación inválido']);
        exit;
    }
    
    $query = "SELECT l.*, 
                     DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y') as fecha_inicio,
                     DATE_FORMAT(l.fecha_fin, '%d/%m/%Y') as fecha_fin,
                     CONCAT(a.nombre, ' ', a.apellidos) as arbitro_nombre
              FROM liquidaciones l
              LEFT JOIN arbitros a ON l.arbitro_id = a.id
              WHERE l.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$liquidacion_id]);
    $liquidacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$liquidacion) {
        echo json_encode(['error' => 'Liquidación no encontrada']);
        exit;
    }
    
    // Obtener partidos de la liquidación
    $query = "SELECT lp.*, 
                     DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i') as fecha,
                     CONCAT(COALESCE(p.equipo_local, 'Equipo Local'), ' vs ', COALESCE(p.equipo_visitante, 'Equipo Visitante')) as equipos,
                     c.nombre as categoria_nombre
              FROM liquidaciones_partidos lp
              LEFT JOIN partidos p ON lp.partido_id = p.id
              LEFT JOIN categorias c ON p.categoria_id = c.id
              WHERE lp.liquidacion_id = ?
              ORDER BY p.fecha";
    $stmt = $conn->prepare($query);
    $stmt->execute([$liquidacion_id]);
    $liquidacion['partidos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total
    $total = 0;
    if (!empty($liquidacion['partidos'])) {
        foreach ($liquidacion['partidos'] as $partido) {
            $total += (float)($partido['importe_partido'] ?? 0) + 
                     (float)($partido['importe_dieta'] ?? 0) + 
                     (float)($partido['importe_kilometraje'] ?? 0);
        }
    }
    $liquidacion['total_importe'] = number_format($total, 2);
    
    echo json_encode($liquidacion);

} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'rectificaciones') {
        // Obtener todas las rectificaciones
        $query = "SELECT r.*, 
                         CONCAT(COALESCE(a.nombre, ''), ' ', COALESCE(a.apellidos, '')) as arbitro_nombre,
                         DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                         DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                         CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'), ' al ', DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
                  FROM rectificaciones_liquidaciones r
                  LEFT JOIN arbitros a ON r.arbitro_id = a.id
                  LEFT JOIN liquidaciones l ON r.liquidacion_id = l.id
                  ORDER BY r.fecha_solicitud DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rectificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($rectificaciones ?: []);
        
    } elseif ($action == 'rectificacion_detalle' && isset($_GET['id'])) {
        // Obtener detalle de una rectificación específica
        $rectificacion_id = (int)$_GET['id'];
        
        if ($rectificacion_id <= 0) {
            echo json_encode(['error' => 'ID de rectificación inválido']);
            exit;
        }
        
        $query = "SELECT r.*, 
                         CONCAT(COALESCE(a.nombre, ''), ' ', COALESCE(a.apellidos, '')) as arbitro_nombre,
                         DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                         DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                         CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'), ' al ', DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
                  FROM rectificaciones_liquidaciones r
                  LEFT JOIN arbitros a ON r.arbitro_id = a.id
                  LEFT JOIN liquidaciones l ON r.liquidacion_id = l.id
                  WHERE r.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$rectificacion_id]);
        $rectificacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rectificacion) {
            echo json_encode(['error' => 'Rectificación no encontrada']);
            exit;
        }
        
        echo json_encode($rectificacion);
    } else {
        echo json_encode(['error' => 'Acción no válida']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar importes
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        echo json_encode(['success' => false, 'error' => 'Datos de entrada inválidos']);
        exit;
    }
    
    if ($input['action'] == 'actualizar_importes') {
        try {
            $conn->beginTransaction();
            
            $liquidacion_id = (int)$input['liquidacion_id'];
            
            if ($liquidacion_id <= 0) {
                throw new Exception('ID de liquidación inválido');
            }
            
            // Obtener partidos de la liquidación
            $query = "SELECT id FROM liquidaciones_partidos WHERE liquidacion_id = ? ORDER BY id";
            $stmt = $conn->prepare($query);
            $stmt->execute([$liquidacion_id]);
            $partidos_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Actualizar cada partido
            if (!empty($input['importes'])) {
                foreach ($input['importes'] as $index => $importes) {
                    if (isset($partidos_ids[$index])) {
                        $query = "UPDATE liquidaciones_partidos SET 
                                    importe_partido = ?, 
                                    importe_dieta = ?, 
                                    importe_kilometraje = ?
                                  WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([
                            (float)($importes['partido'] ?? 0),
                            (float)($importes['dieta'] ?? 0),
                            (float)($importes['kilometraje'] ?? 0),
                            $partidos_ids[$index]
                        ]);
                    }
                }
            }
            
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}

} catch (Exception $e) {
    // Limpiar buffer de salida en caso de error
    ob_clean();
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    // Finalizar el buffer de salida
    ob_end_flush();
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
