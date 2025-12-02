<?php
// Versión de debug para ver qué está enviando el servidor
ob_start(); // Capturar cualquier salida no deseada

// Configurar headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Test básico
    $response = [
        'success' => true,
        'debug' => 'API funcionando',
        'timestamp' => date('Y-m-d H:i:s'),
        'get_params' => $_GET
    ];
    
    // Limpiar cualquier salida previa
    ob_clean();
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

// Terminar el buffer
ob_end_flush();

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>