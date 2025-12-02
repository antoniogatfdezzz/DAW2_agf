<?php
// API simple sin autenticación para testing
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

ini_set('display_errors', 0);
error_reporting(0);

try {
    require_once __DIR__ . '/../../config/database.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $liquidacion_id = (int)$_GET['id'];
        
        if ($liquidacion_id <= 0) {
            echo json_encode(['error' => 'ID de liquidación inválido']);
            exit;
        }
        
        // Obtener liquidación
        $query = "SELECT l.*, 
                         DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y') as fecha_inicio,
                         DATE_FORMAT(l.fecha_fin, '%d/%m/%Y') as fecha_fin,
                         CONCAT(COALESCE(a.nombre, ''), ' ', COALESCE(a.apellidos, '')) as arbitro_nombre
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
        
        // Obtener partidos
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
    } else {
        echo json_encode(['error' => 'Parámetros inválidos']);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
