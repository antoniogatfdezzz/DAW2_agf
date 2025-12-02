<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';

/**
 * Enviar resultado a la API externa vScorer
 * No lanza excepciones fatales en caso de fallo de conexión; en su lugar lanza excepciones controladas
 */
function sendResultToExternal($conn, $partido_id, $sets_local, $sets_visitante, $sets_detail = []) {
    // Obtener campo 'jo' del partido (id requerido por la API externa)
    $stmt = $conn->prepare("SELECT jo FROM partidos WHERE id = ?");
    $stmt->execute([$partido_id]);
    $jo = $stmt->fetchColumn();
    if (!$jo) {
        throw new Exception('Campo jo no encontrado para el partido');
    }

    // Construir payload con hasta 5 sets
    $payload = [
        'idPartido' => (string)$jo,
        'setsLocal' => (string)$sets_local,
        'setsVisitante' => (string)$sets_visitante,
    ];

    for ($i = 1; $i <= 5; $i++) {
        $l = isset($sets_detail[$i]) ? (string)$sets_detail[$i]['local'] : '0';
        $v = isset($sets_detail[$i]) ? (string)$sets_detail[$i]['visitante'] : '0';
        $payload["ptsSet{$i}Local"] = $l;
        $payload["ptsSet{$i}Visitante"] = $v;
    }

    $url = rtrim(EXTERNAL_API_BASEURL, '/') . '/vScorer/updateResultMach';

    if (!function_exists('curl_init')) {
        throw new Exception('cURL no disponible en el servidor');
    }

    $ch = curl_init($url);
    $json = json_encode($payload);
    $startTime = microtime(true);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);

    // Basic auth
    curl_setopt($ch, CURLOPT_USERPWD, EXTERNAL_API_USER . ':' . EXTERNAL_API_PASS);

    // Timeouts
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2); // ms

    // Registrar respuesta (humano legible)
    $logEntry = sprintf("[%s] POST %s payload=%s http=%s resp=%s curlErr=%s duration=%sms\n", date('c'), $url, $json, $httpCode, $response, $curlErr, $duration);
    @file_put_contents(LOG_PATH . '/external_api.log', $logEntry, FILE_APPEND | LOCK_EX);

    // Registrar entrada estructurada (JSON lines)
    $logStructured = [
        'timestamp' => date('c'),
        'user_id' => isset(
            
            
            $_SESSION['user_id']
        ) ? $_SESSION['user_id'] : null,
        'partido_id' => $partido_id,
        'jo' => $jo,
        'url' => $url,
        'payload' => $payload,
        'http_code' => $httpCode,
        'response' => $response,
        'curl_error' => $curlErr,
        'duration_ms' => $duration
    ];
    @file_put_contents(LOG_PATH . '/external_api_requests.log', json_encode($logStructured, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

    if ($httpCode !== 200) {
        throw new Exception('External API returned HTTP ' . $httpCode);
    }

    return true;
}

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Obtener detalles de un partido
    $partido_id = sanitize_input($_GET['id']);
    
    $query = "
        SELECT p.*, 
               DATE(p.fecha) as fecha_solo,
               TIME(p.fecha) as hora_solo,
               p.equipo_local,
               p.equipo_visitante,
               p.observacion_partido,
               c.nombre as categoria,
               p.pabellon_nombre as pabellon,
               CONCAT(a1.nombre, ' ', a1.apellidos) as arbitro1_nombre,
               CONCAT(a2.nombre, ' ', a2.apellidos) as arbitro2_nombre,
               CONCAT(an.nombre, ' ', an.apellidos) as anotador_nombre
        FROM partidos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN arbitros a1 ON p.arbitro_principal_id = a1.id
        LEFT JOIN arbitros a2 ON p.arbitro_segundo_id = a2.id
        LEFT JOIN arbitros an ON p.anotador_id = an.id
        WHERE p.id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$partido_id]);
    $partido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($partido) {
        // Formatear la fecha y hora
        $partido['fecha'] = format_date($partido['fecha_solo']);
        $partido['hora'] = substr($partido['hora_solo'], 0, 5);
        
        // Mantener formato original para formulario
        $partido['fecha_original'] = $partido['fecha_solo'];
        $partido['hora_original'] = substr($partido['hora_solo'], 0, 5);
        
        // Obtener detalles de los sets si el partido está finalizado
        if ($partido['sets_local'] !== null && $partido['sets_visitante'] !== null) {
            $query = "SELECT numero_set, puntos_local, puntos_visitante 
                     FROM sets_partidos 
                     WHERE partido_id = ? 
                     ORDER BY numero_set";
            $stmt = $conn->prepare($query);
            $stmt->execute([$partido_id]);
            $partido['sets_detalle'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $partido['sets_detalle'] = [];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($partido);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'guardar_resultado') {
        // Guardar/modificar resultado del partido (solo admin)
        try {
            $partido_id = sanitize_input($_POST['partido_id']);
            $sets_local = intval($_POST['sets_local']);
            $sets_visitante = intval($_POST['sets_visitante']);
            
            // Validar sets
            if ($sets_local < 0 || $sets_visitante < 0 || $sets_local > 5 || $sets_visitante > 5) {
                throw new Exception('Número de sets inválido');
            }
            
            if ($sets_local == $sets_visitante) {
                throw new Exception('No puede haber empate en voleibol');
            }
            
            $conn->beginTransaction();
            
            // Manejar subida de foto si se proporciona
            $foto_nombre = null;
            if (isset($_FILES['foto_resultado']) && $_FILES['foto_resultado']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['foto_resultado']['error'] === UPLOAD_ERR_OK) {
                $archivo = $_FILES['foto_resultado'];
                
                // Validar tipo de archivo
                $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/heic', 'image/heif'];
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'heic', 'heif'];
                
                if (!in_array($archivo['type'], $tipos_permitidos) && !in_array($extension, $extensiones_permitidas)) {
                    throw new Exception('Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, GIF, HEIC');
                }
                
                // Validar tamaño (5MB máximo)
                if ($archivo['size'] > 5 * 1024 * 1024) {
                    throw new Exception('El archivo es demasiado grande (máximo 5MB)');
                }
                
                // Generar nombre único
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $foto_nombre = 'resultado_' . $partido_id . '_' . time() . '.' . $extension;
                $ruta_destino = __DIR__ . '/../../assets/uploads/' . $foto_nombre;
                
                // Crear directorio si no existe
                $directorio = dirname($ruta_destino);
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0755, true);
                }
                
                    // Mover archivo
                    if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                        throw new Exception('Error al guardar la foto');
                    }
                } else {
                    throw new Exception('Error al subir el archivo: ' . $_FILES['foto_resultado']['error']);
                }
            }
            
            // Actualizar resultado principal con foto
            $query = "UPDATE partidos SET 
                        sets_local = ?, 
                        sets_visitante = ?,
                        foto_resultado = ?,
                        estado = 'finalizado',
                        fecha_actualizacion = NOW()
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$sets_local, $sets_visitante, $foto_nombre, $partido_id]);
            
            // Guardar detalles de sets
            // Primero eliminar registros existentes
            $query = "DELETE FROM sets_partidos WHERE partido_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$partido_id]);

            // Recolectar puntos de los sets desde el POST para luego enviar a la API externa
            $postSets = [];
            $totalSets = ($sets_local + $sets_visitante);
            for ($i = 1; $i <= $totalSets; $i++) {
                if (isset($_POST["set{$i}_local"]) && isset($_POST["set{$i}_visitante"])) {
                    $puntos_local = intval($_POST["set{$i}_local"]);
                    $puntos_visitante = intval($_POST["set{$i}_visitante"]);
                    // Validar puntos del set
                    if ($puntos_local < 0 || $puntos_visitante < 0) {
                        throw new Exception("Puntos inválidos en set $i");
                    }

                    // Insertar registro del set
                    $query = "INSERT INTO sets_partidos (partido_id, numero_set, puntos_local, puntos_visitante) 
                             VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$partido_id, $i, $puntos_local, $puntos_visitante]);

                    $postSets[$i] = [
                        'local' => $puntos_local,
                        'visitante' => $puntos_visitante
                    ];
                }
            }
            
            $conn->commit();
            // Intentar enviar resultado a API externa (no bloqueante)
            try {
                sendResultToExternal($conn, $partido_id, $sets_local, $sets_visitante, $postSets);
            } catch (Exception $e) {
                // Registrar error en log pero no fallar la respuesta local
                $logMsg = sprintf("[external_api] Error sending result for partido_id=%s : %s\n", $partido_id, $e->getMessage());
                @file_put_contents(LOG_PATH . '/external_api.log', $logMsg, FILE_APPEND | LOCK_EX);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Resultado guardado correctamente'
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acción no válida']);
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>
