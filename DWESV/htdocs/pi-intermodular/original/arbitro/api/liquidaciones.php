<?php
// Configurar el manejo de errores ANTES de incluir cualquier archivo
ini_set('display_errors', 0);
error_reporting(0);

// Limpiar cualquier salida previa
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Configurar header JSON
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../config/database.php';

    $auth = new Auth();
    $auth->requireUserType('arbitro');

    $database = new Database();
    $conn = $database->getConnection();
    
    // Obtener ID del árbitro logueado
    $query = "SELECT id FROM arbitros WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $arbitro_id = $stmt->fetchColumn();
    
    if (!$arbitro_id) {
        ob_clean();
        echo json_encode(['error' => 'Árbitro no encontrado']);
        exit();
    }
    
    // Limpiar buffer de salida
    ob_clean();

    // Test básico de conexión
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['test'])) {
        echo json_encode([
            'success' => true,
            'message' => 'API funcionando correctamente',
            'arbitro_id' => $arbitro_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
        // Obtener detalles de liquidación
        $liquidacion_id = (int)$_GET['id'];
        
        if ($liquidacion_id <= 0) {
            echo json_encode(['error' => 'ID de liquidación inválido']);
            exit();
        }
        
        // Verificar que la liquidación pertenece al árbitro logueado
        $query = "SELECT l.*, 
                         DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y') as fecha_inicio,
                         DATE_FORMAT(l.fecha_fin, '%d/%m/%Y') as fecha_fin,
                         CONCAT(a.nombre, ' ', a.apellidos) as arbitro_nombre
                  FROM liquidaciones l
                  LEFT JOIN arbitros a ON l.arbitro_id = a.id
                  WHERE l.id = ? AND l.arbitro_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$liquidacion_id, $arbitro_id]);
        $liquidacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$liquidacion) {
            echo json_encode(['error' => 'Liquidación no encontrada o no tienes permisos para verla']);
            exit();
        }
        
        // Obtener partidos de la liquidación
        $query = "SELECT lp.*, 
                         DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i') as fecha,
                         CONCAT(COALESCE(p.equipo_local, 'Equipo Local'), ' vs ', COALESCE(p.equipo_visitante, 'Equipo Visitante')) as equipos,
                         c.nombre as categoria_nombre,
                         lp.rol_arbitro
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
        
        if ($action == 'mis_rectificaciones') {
            // Obtener rectificaciones del árbitro
            $query = "SELECT r.*, 
                             CONCAT(COALESCE(a.nombre, ''), ' ', COALESCE(a.apellidos, '')) as arbitro_nombre,
                             DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                             DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                             CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'), ' al ', DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
                      FROM rectificaciones_liquidaciones r
                      LEFT JOIN arbitros a ON r.arbitro_id = a.id
                      LEFT JOIN liquidaciones l ON r.liquidacion_id = l.id
                      WHERE r.arbitro_id = ?
                      ORDER BY r.fecha_solicitud DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute([$arbitro_id]);
            $rectificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($rectificaciones ?: []);
            
        } elseif ($action == 'rectificacion_detalle' && isset($_GET['id'])) {
            // Obtener detalle de una rectificación específica (solo las propias)
            $rectificacion_id = (int)$_GET['id'];
            
            if ($rectificacion_id <= 0) {
                echo json_encode(['error' => 'ID de rectificación inválido']);
                exit();
            }
            
            $query = "SELECT r.*, 
                             CONCAT(COALESCE(a.nombre, ''), ' ', COALESCE(a.apellidos, '')) as arbitro_nombre,
                             DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                             DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                             CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'), ' al ', DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
                      FROM rectificaciones_liquidaciones r
                      LEFT JOIN arbitros a ON r.arbitro_id = a.id
                      LEFT JOIN liquidaciones l ON r.liquidacion_id = l.id
                      WHERE r.id = ? AND r.arbitro_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$rectificacion_id, $arbitro_id]);
            $rectificacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rectificacion) {
                echo json_encode(['error' => 'Rectificación no encontrada o no tienes permisos para verla']);
                exit();
            }
            
            echo json_encode($rectificacion);
        } else {
            echo json_encode(['error' => 'Acción no válida']);
        }
        

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar_opciones_partido') {
        // Actualizar dieta y kilometraje de un partido en liquidación
        $partido_id = isset($_POST['partido_id']) ? (int)$_POST['partido_id'] : 0;
        $dieta = isset($_POST['dieta']) && $_POST['dieta'] === 'true';
        $kilometraje = isset($_POST['kilometraje']) && $_POST['kilometraje'] === 'true';
        $kilometros = isset($_POST['kilometros']) ? floatval($_POST['kilometros']) : 0;

        if ($partido_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de partido inválido']);
            exit();
        }

        // Calcular importes
        $importe_dieta = $dieta ? 14.0 : 0.0;
        $importe_kilometraje = ($kilometraje && $kilometros > 0) ? round($kilometros * 0.22, 2) : 0.0;

        // Verificar si el partido está en alguna liquidación
        $query = "SELECT lp.id, lp.liquidacion_id 
                  FROM liquidaciones_partidos lp
                  INNER JOIN liquidaciones l ON lp.liquidacion_id = l.id
                  WHERE lp.partido_id = ? AND l.arbitro_id = ?
                  LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$partido_id, $arbitro_id]);
        $liquidacion_partido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($liquidacion_partido) {
            // Actualizar en liquidaciones_partidos
            $query = "UPDATE liquidaciones_partidos 
                      SET importe_dieta = ?, 
                          importe_kilometraje = ?, 
                          kilometros = ? 
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$importe_dieta, $importe_kilometraje, $kilometros, $liquidacion_partido['id']]);
            
            echo json_encode([
                'success' => true, 
                'importe_dieta' => $importe_dieta, 
                'importe_kilometraje' => $importe_kilometraje,
                'destino' => 'liquidaciones_partidos'
            ]);
            exit();
        }

        // Si no existe en liquidaciones, guardar en la tabla partidos según el rol
        $query = "SELECT arbitro_principal_id, arbitro_segundo_id, anotador_id FROM partidos WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$partido_id]);
        $partido_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$partido_info) {
            echo json_encode(['success' => false, 'error' => 'Partido no encontrado']);
            exit();
        }
        
        // Determinar el rol del árbitro y actualizar campos correspondientes
        $col_dieta = null;
        $col_kilometraje = null;
        $col_km = null;
        
        if ($partido_info['arbitro_principal_id'] == $arbitro_id) {
            $col_dieta = 'dieta_arbitro1';
            $col_kilometraje = 'kilometraje_arbitro1';
            $col_km = 'km_arbitro1';
        } elseif ($partido_info['arbitro_segundo_id'] == $arbitro_id) {
            $col_dieta = 'dieta_arbitro2';
            $col_kilometraje = 'kilometraje_arbitro2';
            $col_km = 'km_arbitro2';
        } elseif ($partido_info['anotador_id'] == $arbitro_id) {
            $col_dieta = 'dieta_anotador';
            $col_kilometraje = 'kilometraje_anotador';
            $col_km = 'km_anotador';
        }
        
        if ($col_dieta && $col_kilometraje && $col_km) {
            $query = "UPDATE partidos 
                      SET $col_dieta = ?, 
                          $col_kilometraje = ?, 
                          $col_km = ? 
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$dieta ? 1 : 0, $importe_kilometraje, $kilometros, $partido_id]);
            
            echo json_encode([
                'success' => true, 
                'importe_dieta' => $importe_dieta, 
                'importe_kilometraje' => $importe_kilometraje,
                'destino' => 'partidos',
                'rol' => $col_dieta
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No eres árbitro asignado a este partido']);
        }
    } else {
        echo json_encode(['error' => 'Parámetros inválidos']);
    }

} catch (Exception $e) {
    // Limpiar buffer de salida en caso de error
    if (ob_get_level()) {
        ob_clean();
    }
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    exit();
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
