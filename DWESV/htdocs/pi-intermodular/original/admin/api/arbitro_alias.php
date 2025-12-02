<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Obtener alias de un árbitro específico
            if (isset($_GET['arbitro_id'])) {
                $arbitroId = (int)$_GET['arbitro_id'];
                
                $stmt = $conn->prepare("
                    SELECT aa.id, aa.alias, aa.fecha_creacion
                    FROM arbitro_alias aa
                    WHERE aa.arbitro_id = :arbitro_id
                    ORDER BY aa.alias
                ");
                $stmt->execute([':arbitro_id' => $arbitroId]);
                $alias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'alias' => $alias
                ]);
            } else {
                // Listar todos los árbitros con sus alias
                $stmt = $conn->query("
                    SELECT 
                        a.id,
                        a.nombre,
                        a.apellidos,
                        GROUP_CONCAT(aa.alias ORDER BY aa.alias SEPARATOR '|') as alias_list,
                        COUNT(aa.id) as total_alias
                    FROM arbitros a
                    LEFT JOIN arbitro_alias aa ON a.id = aa.arbitro_id
                    WHERE a.nombre != '' OR a.apellidos != ''
                    GROUP BY a.id, a.nombre, a.apellidos
                    ORDER BY a.apellidos, a.nombre
                ");
                $arbitros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'arbitros' => $arbitros
                ]);
            }
            break;

        case 'POST':
            // Crear nuevo alias
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['arbitro_id']) || !isset($data['alias'])) {
                throw new Exception('Faltan datos requeridos');
            }
            
            $arbitroId = (int)$data['arbitro_id'];
            $alias = sanitize_input($data['alias']);
            
            if (empty($alias)) {
                throw new Exception('El alias no puede estar vacío');
            }
            
            // Verificar que el árbitro existe
            $stmt = $conn->prepare("SELECT id FROM arbitros WHERE id = :id");
            $stmt->execute([':id' => $arbitroId]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('Árbitro no encontrado');
            }
            
            // Verificar que el alias no existe ya
            $stmt = $conn->prepare("SELECT id FROM arbitro_alias WHERE alias = :alias");
            $stmt->execute([':alias' => $alias]);
            if ($stmt->fetchColumn()) {
                throw new Exception('Este alias ya está siendo usado por otro árbitro');
            }
            
            // Insertar alias
            $stmt = $conn->prepare("
                INSERT INTO arbitro_alias (arbitro_id, alias)
                VALUES (:arbitro_id, :alias)
            ");
            $stmt->execute([
                ':arbitro_id' => $arbitroId,
                ':alias' => $alias
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Alias creado correctamente',
                'id' => $conn->lastInsertId()
            ]);
            break;

        case 'DELETE':
            // Eliminar alias
            if (!isset($_GET['id'])) {
                throw new Exception('ID de alias no proporcionado');
            }
            
            $aliasId = (int)$_GET['id'];
            
            // Verificar que existe
            $stmt = $conn->prepare("SELECT id FROM arbitro_alias WHERE id = :id");
            $stmt->execute([':id' => $aliasId]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('Alias no encontrado');
            }
            
            // Eliminar
            $stmt = $conn->prepare("DELETE FROM arbitro_alias WHERE id = :id");
            $stmt->execute([':id' => $aliasId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Alias eliminado correctamente'
            ]);
            break;

        default:
            throw new Exception('Método no permitido');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>