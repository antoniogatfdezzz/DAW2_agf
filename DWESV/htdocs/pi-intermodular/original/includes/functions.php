<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function format_datetime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

function generate_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

function upload_file($file, $destination_path, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'heic', 'heif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error en la subida del archivo'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }
    
    $file_name = uniqid() . '.' . $file_extension;
    $full_path = $destination_path . '/' . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => true, 'filename' => $file_name, 'path' => $full_path];
    }
    
    return ['success' => false, 'message' => 'Error al mover el archivo'];
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_dni($dni) {
    $dni = strtoupper(str_replace([' ', '-'], '', $dni));
    
    if (strlen($dni) !== 9) {
        return false;
    }
    
    $number = substr($dni, 0, 8);
    $letter = substr($dni, 8, 1);
    
    if (!is_numeric($number)) {
        return false;
    }
    
    $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $calculated_letter = $letters[intval($number) % 23];
    
    return $letter === $calculated_letter;
}

function get_user_avatar($user_type) {
    switch ($user_type) {
        case 'administrador':
            return 'fas fa-user-shield';
        case 'arbitro':
            return 'fas fa-whistle';
        default:
            return 'fas fa-user';
    }
}

function success_message($message) {
    return '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $message . '</div>';
}

function error_message($message) {
    return '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . $message . '</div>';
}

function warning_message($message) {
    return '<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i> ' . $message . '</div>';
}

function info_message($message) {
    return '<div class="alert alert-info"><i class="fas fa-info-circle"></i> ' . $message . '</div>';
}

/**
 * Función eliminada - ya no es necesaria
 * Las licencias ya no tienen fechas de vencimiento
 */
function desactivar_licencias_vencidas($conn) {
    // Función eliminada - las licencias ya no vencen
    return [
        'success' => true,
        'licencias_desactivadas' => 0,
        'message' => "Las licencias ya no tienen vencimiento"
    ];
}

/**
 * Obtiene la licencia de mayor nivel de un árbitro
 * Útil para determinar qué licencia mostrar como principal
 */
function obtener_licencia_principal_arbitro($conn, $arbitro_id) {
    try {
        // Orden de prioridad de licencias (de mayor a menor nivel)
        $orden_prioridad = ['n3a', 'n3b', 'n3c', 'n2', 'n1', 'anotador', 'colaborador'];
        
        $query = "SELECT * FROM licencias_arbitros 
                  WHERE arbitro_id = ? 
                  ORDER BY FIELD(nivel_licencia, '" . implode("','", $orden_prioridad) . "')
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$arbitro_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("FEDEXVB: Error al obtener licencia principal: " . $e->getMessage());
        return null;
    }
}

/**
 * Formatea el nivel de licencia para mostrar
 */
function formatLicenciaNivel($nivel) {
    switch ($nivel) {
        case 'colaborador':
            return 'Colaborador/a';
        case 'anotador':
            return 'Anotador';
        case 'n1':
            return 'Nivel I (N1)';
        case 'n2':
            return 'Nivel II (N2)';
        case 'n3c':
            return 'Nivel III C (N3 C)';
        case 'n3b':
            return 'Nivel III B (N3 B)';
        case 'n3a':
            return 'Nivel III A (N3 A)';
        default:
            return ucfirst($nivel);
    }
}

/**
 * Obtiene el color del badge para el nivel de licencia
 */
function getBadgeColorForNivel($nivel) {
    switch ($nivel) {
        case 'n3a':
            return 'var(--success)';
        case 'n3b':
            return '#28a745';
        case 'n3c':
            return '#20c997';
        case 'n2':
            return 'var(--info)';
        case 'n1':
            return 'var(--warning)';
        case 'anotador':
            return '#fd7e14';
        case 'colaborador':
        default:
            return 'var(--medium-gray)';
    }
}
