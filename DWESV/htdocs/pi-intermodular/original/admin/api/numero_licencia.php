<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos JSON inválidos']);
    exit;
}

$arbitro_id = $data['arbitro_id'] ?? null;
$numero_licencia = $data['numero_licencia'] ?? null;

if (!$arbitro_id || !$numero_licencia) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit;
}

// Validar formato del número de licencia (solo números y letras, 3-20 caracteres)
if (!preg_match('/^[A-Za-z0-9]{3,20}$/', $numero_licencia)) {
    http_response_code(400);
    echo json_encode(['error' => 'El número de licencia debe contener solo letras y números (3-20 caracteres)']);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Verificar que el árbitro existe
    $checkQuery = "SELECT id, nombre, apellidos, numero_licencia FROM arbitros WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([$arbitro_id]);
    $arbitro = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$arbitro) {
        http_response_code(404);
        echo json_encode(['error' => 'Árbitro no encontrado']);
        exit;
    }
    
    // Verificar si ya tiene número de licencia asignado
    if ($arbitro['numero_licencia']) {
        http_response_code(400);
        echo json_encode(['error' => 'Este árbitro ya tiene un número de licencia asignado: ' . $arbitro['numero_licencia']]);
        exit;
    }
    
    // Verificar que el número de licencia no esté en uso
    $uniqueQuery = "SELECT id, nombre, apellidos FROM arbitros WHERE numero_licencia = ? AND id != ?";
    $uniqueStmt = $conn->prepare($uniqueQuery);
    $uniqueStmt->execute([$numero_licencia, $arbitro_id]);
    $existingArbitro = $uniqueStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingArbitro) {
        http_response_code(400);
        echo json_encode([
            'error' => 'El número de licencia ya está asignado a: ' . $existingArbitro['nombre'] . ' ' . $existingArbitro['apellidos']
        ]);
        exit;
    }
    
    // Asignar el número de licencia
    $updateQuery = "UPDATE arbitros SET numero_licencia = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateResult = $updateStmt->execute([$numero_licencia, $arbitro_id]);
    
    if (!$updateResult || $updateStmt->rowCount() === 0) {
        throw new Exception("No se pudo actualizar el número de licencia");
    }
    
    $conn->commit();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Número de licencia asignado correctamente',
        'data' => [
            'arbitro_id' => $arbitro_id,
            'numero_licencia' => $numero_licencia,
            'arbitro_nombre' => $arbitro['nombre'] . ' ' . $arbitro['apellidos']
        ]
    ]);
    
} catch (PDOException $e) {
    $conn->rollback();
    
    // Manejar error de clave duplicada específicamente
    if ($e->getCode() == 23000) {
        http_response_code(400);
        echo json_encode(['error' => 'El número de licencia ya está asignado a otro árbitro']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
