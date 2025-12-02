<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireUserType('administrador');

try {
    // Verificar que se subió un archivo
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se ha subido ningún archivo o hubo un error en la carga');
    }

    $file = $_FILES['excel_file'];
    $filePath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validar extensión
    if (!in_array($fileExtension, ['csv'])) {
        throw new Exception('Formato de archivo no válido. Solo se permite CSV. Por favor, convierta su Excel a CSV primero.');
    }

    // Leer el archivo CSV
    $data = [];
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {

        $primeraLinea = fgetcsv($handle, 10000, ",");
        

        $headers = fgetcsv($handle, 10000, ",");
        

        $headers = array_map(function($header) {
            return trim(str_replace("\xEF\xBB\xBF", '', $header));
        }, $headers);
        
        $joCount = 0;
        foreach ($headers as $index => $header) {
            if ($header === 'Jo') {
                $joCount++;
                if ($joCount === 2) {
                    $headers[$index] = 'Jornada';
                }
            }
        }
        
        // Leer todas las filas
        while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
    }

    if (empty($data)) {
        throw new Exception('El archivo está vacío o no tiene el formato correcto');
    }

    // Conectar a la base de datos
    $database = new Database();
    $conn = $database->getConnection();

    // Iniciar transacción
    $conn->beginTransaction();

    $partidosCreados = 0;
    $errores = [];

    foreach ($data as $index => $row) {
        $lineNumber = $index + 2; // +2 porque empezamos en 1 y la primera línea son headers
        
        try {
            // Extraer datos del CSV
            $jo = isset($row['Jo']) ? sanitize_input($row['Jo']) : null;
            $jornada = isset($row['Jornada']) ? sanitize_input($row['Jornada']) : null; // Segunda columna "Jo" es jornada
            $equipoLocal = isset($row['Equipo_local']) ? sanitize_input($row['Equipo_local']) : '';
            $equipoVisitante = isset($row['Equipo_visitante']) ? sanitize_input($row['Equipo_visitante']) : '';
            
            // Validar campos requeridos
            if (empty($equipoLocal) || empty($equipoVisitante)) {
                throw new Exception("Equipo local o visitante vacío");
            }

            // Procesar fecha - puede venir con día de semana: "2025-10-03 (Viernes)"
            $fechaRaw = isset($row['Fecha']) ? sanitize_input($row['Fecha']) : '';
            // Extraer solo la fecha YYYY-MM-DD
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', $fechaRaw, $matches)) {
                $fecha = $matches[1];
            } else {
                throw new Exception("Formato de fecha inválido: $fechaRaw");
            }

            $hora = isset($row['Hora']) ? sanitize_input($row['Hora']) : '';
            if (empty($hora)) {
                throw new Exception("Hora vacía");
            }

            // Combinar fecha y hora
            $fechaHora = $fecha . ' ' . $hora;
            
            // Validar formato de fecha/hora
            $dateTime = DateTime::createFromFormat('Y-m-d H:i', $fechaHora);
            if (!$dateTime) {
                throw new Exception("Formato de fecha/hora inválido: $fechaHora");
            }

            $pabellon = isset($row['Pabellon']) ? sanitize_input($row['Pabellon']) : '';
            $categoria = isset($row['Categoria']) ? sanitize_input($row['Categoria']) : '';
            $sexo = isset($row['Sexo']) ? sanitize_input($row['Sexo']) : '';
            $grupo = isset($row['Grupo']) ? sanitize_input($row['Grupo']) : null;

            // Buscar categoría por nombre usando la función buscarCategoria
            $categoriaId = buscarCategoria($conn, $categoria, $sexo);
            
            if (!$categoriaId) {
                $categoriaCompleta = $categoria . ' + ' . $sexo;
                throw new Exception("Categoría '$categoriaCompleta' no encontrada");
            }

            // Buscar árbitros por alias
            $arbitro1Id = null;
            $arbitro2Id = null;
            $anotadorId = null;

            if (isset($row['1er Arbitro']) && trim($row['1er Arbitro']) !== '') {
                $arbitro1Alias = sanitize_input($row['1er Arbitro']);
                $arbitro1Id = buscarArbitroPorAlias($conn, $arbitro1Alias);
                if (!$arbitro1Id) {
                    $errores[] = "Línea $lineNumber: Advertencia - 1er Árbitro '$arbitro1Alias' no encontrado, partido creado sin árbitro principal";
                }
            }

            if (isset($row['2º Árbitro']) && trim($row['2º Árbitro']) !== '') {
                $arbitro2Alias = sanitize_input($row['2º Árbitro']);
                $arbitro2Id = buscarArbitroPorAlias($conn, $arbitro2Alias);
                if (!$arbitro2Id) {
                    $errores[] = "Línea $lineNumber: Advertencia - 2º Árbitro '$arbitro2Alias' no encontrado, partido creado sin segundo árbitro";
                }
            }

            if (isset($row['Anotador/a']) && trim($row['Anotador/a']) !== '') {
                $anotadorAlias = sanitize_input($row['Anotador/a']);
                $anotadorId = buscarArbitroPorAlias($conn, $anotadorAlias);
                if (!$anotadorId) {
                    $errores[] = "Línea $lineNumber: Advertencia - Anotador '$anotadorAlias' no encontrado, partido creado sin anotador";
                }
            }

            // Insertar partido
            $stmtPartido = $conn->prepare("
                INSERT INTO partidos (
                    jo, jornada,
                    equipo_local, equipo_visitante, categoria_id, grupo, 
                    fecha, pabellon_nombre, 
                    arbitro_principal_id, arbitro_segundo_id, anotador_id,
                    estado, finalizado
                ) VALUES (
                    :jo, :jornada,
                    :equipo_local, :equipo_visitante, :categoria_id, :grupo,
                    :fecha, :pabellon_nombre,
                    :arbitro_principal_id, :arbitro_segundo_id, :anotador_id,
                    'programado', 0
                )
            ");

            $stmtPartido->execute([
                ':jo' => $jo,
                ':jornada' => $jornada,
                ':equipo_local' => $equipoLocal,
                ':equipo_visitante' => $equipoVisitante,
                ':categoria_id' => $categoriaId,
                ':grupo' => $grupo,
                ':fecha' => $fechaHora,
                ':pabellon_nombre' => $pabellon,
                ':arbitro_principal_id' => $arbitro1Id,
                ':arbitro_segundo_id' => $arbitro2Id,
                ':anotador_id' => $anotadorId
            ]);

            $partidosCreados++;

        } catch (Exception $e) {
            $errores[] = "Línea $lineNumber: " . $e->getMessage();
        }
    }

    // Confirmar transacción
    $conn->commit();

    $response = [
        'success' => true,
        'message' => "Se han procesado " . count($data) . " líneas. Se crearon $partidosCreados partidos correctamente",
        'partidos_creados' => $partidosCreados,
        'total_lineas' => count($data),
        'errores' => $errores
    ];

    echo json_encode($response);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Busca una categoría por nombre de categoría y sexo
 * Mapeo específico para categorías nacionales según formato Excel:
 * - SM2 + Masculino = SuperLiga 2 - Masc
 * - SF2 + Femenino = SuperLiga 2 - Fem
 * - 1ª Nacional + Masculino = 1ª Nacional - Masc
 * - 1ª Nacional + Femenino = 1ª Nacional - Fem
 */
function buscarCategoria($conn, $categoria, $sexo) {
    $categoria = trim($categoria);
    $sexo = trim($sexo);
    
    // Normalizar sexo
    $sexoLower = strtolower($sexo);
    $esFemenino = (stripos($sexoLower, 'fem') !== false || 
                   stripos($sexoLower, 'mujer') !== false || 
                   stripos($sexoLower, 'f') === 0);
    $sexoSuffix = $esFemenino ? 'Fem' : 'Masc';
    
    // Mapeo específico para categorías nacionales
    $categoriaNormalizada = strtoupper(trim($categoria));
    
    // SuperLiga 2: SM2 (Masculino) o SF2 (Femenino)
    if ($categoriaNormalizada === 'SM2' || 
        $categoriaNormalizada === 'SF2' ||
        stripos($categoria, 'superliga 2') !== false || 
        stripos($categoria, 'superliga2') !== false ||
        stripos($categoria, 'super liga 2') !== false) {
        
        $nombreCategoria = 'SuperLiga 2 - ' . $sexoSuffix;
        
    // 1ª Nacional
    } elseif (stripos($categoriaNormalizada, '1') !== false && 
              (stripos($categoriaNormalizada, 'NACIONAL') !== false || 
               stripos($categoriaNormalizada, 'NAC') !== false)) {
        
        $nombreCategoria = '1ª Nacional - ' . $sexoSuffix;
        
    } else {
        // Categorías estándar: Alevín, Infantil, Cadete, Juvenil, Senior
        // Normalizar nombre de categoría
        $categoriaNombre = ucfirst(strtolower($categoria));
        
        // Normalizar sexo completo
        if ($esFemenino) {
            $sexoNombre = 'Femenino';
        } else {
            $sexoNombre = 'Masculino';
        }
        
        $nombreCategoria = $categoriaNombre . ' ' . $sexoNombre;
    }
    
    // Buscar categoría por nombre exacto
    $stmt = $conn->prepare("SELECT id FROM categorias WHERE nombre = :nombre LIMIT 1");
    $stmt->execute([':nombre' => $nombreCategoria]);
    $result = $stmt->fetchColumn();
    
    if ($result) {
        return $result;
    }
    
    // Si no se encuentra, intentar búsqueda flexible
    $stmt = $conn->prepare("
        SELECT id FROM categorias 
        WHERE LOWER(nombre) LIKE LOWER(:categoria) 
        AND LOWER(nombre) LIKE LOWER(:sexo)
        LIMIT 1
    ");
    $stmt->execute([
        ':categoria' => '%' . $categoria . '%',
        ':sexo' => '%' . $sexo . '%'
    ]);
    
    return $stmt->fetchColumn();
}

/**
 * Busca un árbitro por su alias
 */
function buscarArbitroPorAlias($conn, $alias) {
    $alias = trim($alias);
    
    if (empty($alias)) {
        return null;
    }
    
    // Primero buscar coincidencia exacta
    $stmt = $conn->prepare("
        SELECT arbitro_id 
        FROM arbitro_alias 
        WHERE alias = :alias
        LIMIT 1
    ");
    $stmt->execute([':alias' => $alias]);
    $result = $stmt->fetchColumn();
    
    if ($result) {
        return $result;
    }
    
    // Si no hay coincidencia exacta, buscar coincidencia parcial (case insensitive)
    $stmt = $conn->prepare("
        SELECT arbitro_id 
        FROM arbitro_alias 
        WHERE LOWER(TRIM(alias)) = LOWER(:alias)
        LIMIT 1
    ");
    $stmt->execute([':alias' => $alias]);
    $result = $stmt->fetchColumn();
    
    if ($result) {
        return $result;
    }
    
    // Buscar por similitud (contiene el texto)
    $stmt = $conn->prepare("
        SELECT arbitro_id 
        FROM arbitro_alias 
        WHERE LOWER(alias) LIKE LOWER(:alias)
        LIMIT 1
    ");
    $stmt->execute([':alias' => '%' . $alias . '%']);
    
    return $stmt->fetchColumn();
}

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>