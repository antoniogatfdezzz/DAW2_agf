<?php
// Archivo de debugging para verificar el API de liquidaciones
// Eliminar cuando se solucione el problema

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

echo "<h2>Debug: API Liquidaciones Árbitro</h2>";
echo "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";

try {
    $auth = new Auth();
    echo "<p>✓ Auth class loaded</p>";
    
    if (!isset($_SESSION['user_id'])) {
        echo "<p>❌ No hay sesión de usuario</p>";
        exit();
    }
    
    echo "<p>✓ User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>✓ User Type: " . ($_SESSION['user_type'] ?? 'No definido') . "</p>";
    
    if ($_SESSION['user_type'] !== 'arbitro') {
        echo "<p>❌ Usuario no es árbitro</p>";
        exit();
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    echo "<p>✓ Database connection established</p>";
    
    // Obtener ID del árbitro logueado
    $query = "SELECT id FROM arbitros WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $arbitro_id = $stmt->fetchColumn();
    
    if (!$arbitro_id) {
        echo "<p>❌ Árbitro no encontrado para user_id: " . $_SESSION['user_id'] . "</p>";
        exit();
    }
    
    echo "<p>✓ Arbitro ID: " . $arbitro_id . "</p>";
    
    // Verificar si hay liquidaciones
    $query = "SELECT COUNT(*) FROM liquidaciones WHERE arbitro_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$arbitro_id]);
    $count = $stmt->fetchColumn();
    
    echo "<p>✓ Liquidaciones encontradas: " . $count . "</p>";
    
    if (isset($_GET['id'])) {
        $liquidacion_id = (int)$_GET['id'];
        echo "<p>Probando liquidación ID: " . $liquidacion_id . "</p>";
        
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
        
        if ($liquidacion) {
            echo "<p>✓ Liquidación encontrada</p>";
            echo "<pre>" . print_r($liquidacion, true) . "</pre>";
            
            // Obtener partidos
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
            $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>✓ Partidos encontrados: " . count($partidos) . "</p>";
            
            if (!empty($partidos)) {
                echo "<h3>Partidos:</h3>";
                echo "<pre>" . print_r($partidos, true) . "</pre>";
            }
            
        } else {
            echo "<p>❌ Liquidación no encontrada o no pertenece al árbitro</p>";
        }
    } else {
        echo "<p>Para probar una liquidación específica, añade ?id=X a la URL</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>