<?php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_user':
        getUserData($conn, $_GET['id']);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

function getUserData($conn, $user_id) {
    try {
        $query = "SELECT u.*, 
                         a.nombre as admin_nombre, a.apellidos as admin_apellidos,
                         ar.nombre as arbitro_nombre, ar.apellidos as arbitro_apellidos, 
                         ar.dni, ar.telefono, ar.numero_matricula, ar.ciudad, ar.iban as arbitro_iban, ar.licencia
                  FROM usuarios u
                  LEFT JOIN administradores a ON u.id = a.usuario_id
                  LEFT JOIN arbitros ar ON u.id = ar.usuario_id
                  WHERE u.id = ? AND u.activo = 1";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Organizar datos según tipo de usuario
            $userData = [
                'id' => $user['id'],
                'usuario' => $user['usuario'],
                'email' => $user['email'],
                'tipo_usuario' => $user['tipo_usuario']
            ];
            
            switch ($user['tipo_usuario']) {
                case 'administrador':
                    $userData['nombre'] = $user['admin_nombre'];
                    $userData['apellidos'] = $user['admin_apellidos'];
                    break;
                    
                case 'arbitro':
                    $userData['nombre'] = $user['arbitro_nombre'];
                    $userData['apellidos'] = $user['arbitro_apellidos'];
                    $userData['dni'] = $user['dni'];
                    $userData['telefono'] = $user['telefono'];
                    $userData['numero_matricula'] = $user['numero_matricula'];
                    $userData['ciudad'] = $user['ciudad'];
                    $userData['iban'] = $user['arbitro_iban'];
                    $userData['licencia'] = $user['licencia'];
                    break;
            }
            
            echo json_encode(['success' => true, 'user' => $userData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener los datos del usuario']);
    }
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
