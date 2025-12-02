<?php
// Versión simplificada para debugging
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Solo verificar que llegue la petición
    if (!isset($_GET['fecha']) || empty($_GET['fecha'])) {
        throw new Exception('Fecha requerida');
    }

    $fecha = trim($_GET['fecha']);
    
    // Validar formato básico
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Formato de fecha inválido: ' . $fecha);
    }

    // Respuesta básica sin BD
    $response = [
        'success' => true,
        'fecha' => $fecha,
        'fecha_formateada' => date('d/m/Y', strtotime($fecha)),
        'dia_semana' => 'Lunes',
        'arbitros' => [],
        'estadisticas' => [
            'total' => 0,
            'disponibles' => 0,
            'no_disponibles' => 0,
            'sin_informacion' => 0
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
