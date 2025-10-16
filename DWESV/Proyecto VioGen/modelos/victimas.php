<?php
/**
 * MODELO: VÍCTIMAS
 * Base de datos simulada con arrays para gestionar víctimas de violencia de género
 * Incluye todos los campos requeridos por VPR/VPER
 */

require_once __DIR__ . '/../config.php';

// =============================================================================
// ALMACENAMIENTO EN MEMORIA (simulación de BD)
// =============================================================================
$VICTIMAS = [];

// =============================================================================
// FUNCIONES CRUD
// =============================================================================

/**
 * Obtiene todas las víctimas
 */
function obtenerTodasVictimas() {
    global $VICTIMAS;
    return $VICTIMAS;
}

/**
 * Busca una víctima por ID
 */
function buscarVictimaPorId($id) {
    global $VICTIMAS;
    return $VICTIMAS[$id] ?? null;
}

/**
 * Busca una víctima por número de documento
 */
function buscarVictimaPorDocumento($num_documento) {
    global $VICTIMAS;
    foreach ($VICTIMAS as $victima) {
        if ($victima['num_documento'] === $num_documento) {
            return $victima;
        }
    }
    return null;
}

/**
 * Crea una nueva víctima
 */
function crearVictima($datos) {
    global $VICTIMAS;
    
    // Validar datos obligatorios
    $campos_requeridos = ['nombre', 'apellidos', 'num_documento', 'fecha_nacimiento', 'domicilio', 'telefono'];
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo])) {
            return ['error' => "El campo $campo es obligatorio"];
        }
    }
    
    // Verificar si el documento ya existe
    if (buscarVictimaPorDocumento($datos['num_documento'])) {
        return ['error' => 'El número de documento ya está registrado'];
    }
    
    // Generar ID
    $id = generarID('V');
    
    // Calcular edad
    $edad = calcularEdad($datos['fecha_nacimiento']);
    
    // Crear nueva víctima con todos los campos
    $nueva_victima = [
        'id' => $id,
        
        // Datos personales básicos (OBLIGATORIOS)
        'nombre' => sanitizar($datos['nombre']),
        'apellidos' => sanitizar($datos['apellidos']),
        'tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
        'num_documento' => sanitizar($datos['num_documento']),
        'fecha_nacimiento' => $datos['fecha_nacimiento'],
        'edad' => $edad,
        'nacionalidad' => sanitizar($datos['nacionalidad'] ?? 'Española'),
        'sexo' => $datos['sexo'] ?? 'Mujer',
        
        // Contacto (OBLIGATORIO)
        'domicilio' => sanitizar($datos['domicilio']),
        'domicilio_coincide_agresor' => $datos['domicilio_coincide_agresor'] ?? false,
        'lugares_frecuentes' => $datos['lugares_frecuentes'] ?? [],
        'telefono' => sanitizar($datos['telefono']),
        'telefonos_adicionales' => $datos['telefonos_adicionales'] ?? [],
        'email' => sanitizar($datos['email'] ?? ''),
        'preferencia_contacto' => $datos['preferencia_contacto'] ?? '',
        
        // Idioma y comunicación
        'idioma' => $datos['idioma'] ?? 'Español',
        'necesita_interprete' => $datos['necesita_interprete'] ?? false,
        
        // Salud
        'estado_reproductivo' => $datos['estado_reproductivo'] ?? 'No',
        'embarazada' => $datos['embarazada'] ?? false,
        'fecha_probable_parto' => $datos['fecha_probable_parto'] ?? null,
        'discapacidad' => $datos['discapacidad'] ?? 'No',
        'tipo_discapacidad' => $datos['tipo_discapacidad'] ?? '',
        'enfermedad_cronica' => $datos['enfermedad_cronica'] ?? false,
        'detalles_enfermedad' => $datos['detalles_enfermedad'] ?? '',
        'consumo_toxicos' => $datos['consumo_toxicos'] ?? 'No',
        'tipo_toxicos' => $datos['tipo_toxicos'] ?? '',
        
        // Salud mental
        'salud_mental' => $datos['salud_mental'] ?? 'Estable',
        'ideas_suicidas' => $datos['ideas_suicidas'] ?? false,
        'intentos_suicidas_previos' => $datos['intentos_suicidas_previos'] ?? false,
        'detalles_salud_mental' => $datos['detalles_salud_mental'] ?? '',
        
        // Situación económica y social
        'situacion_economica' => $datos['situacion_economica'] ?? 'Independiente',
        'dependiente_economicamente' => $datos['dependiente_economicamente'] ?? false,
        'red_apoyo' => $datos['red_apoyo'] ?? [],
        'tiene_apoyo_familiar' => $datos['tiene_apoyo_familiar'] ?? false,
        'tiene_apoyo_amigos' => $datos['tiene_apoyo_amigos'] ?? false,
        'servicios_sociales' => $datos['servicios_sociales'] ?? false,
        
        // Vivienda y convivencia
        'vivienda_compartida_con_agresor' => $datos['vivienda_compartida_con_agresor'] ?? false,
        'propiedad_vivienda' => $datos['propiedad_vivienda'] ?? '',
        
        // Situación laboral
        'relacion_laboral' => $datos['relacion_laboral'] ?? 'Desempleo',
        'tipo_empleo' => $datos['tipo_empleo'] ?? '',
        'jornada_laboral' => $datos['jornada_laboral'] ?? '',
        
        // Menores
        'tiene_menores' => $datos['tiene_menores'] ?? false,
        'menores' => $datos['menores'] ?? [], // Array de edades/datos de hijos
        'custodia_hijos' => $datos['custodia_hijos'] ?? 'No aplica',
        'tipo_custodia' => $datos['tipo_custodia'] ?? '',
        
        // Usuario para acceso (si aplica)
        'usuario' => !empty($datos['usuario']) ? sanitizar($datos['usuario']) : null,
        'password' => !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_DEFAULT) : null,
        'rol' => ROL_VICTIMA,
        
        // Observaciones generales
        'observaciones' => $datos['observaciones'] ?? '',
        
        // Metadata
        'activo' => true,
        'fecha_registro' => date('Y-m-d H:i:s'),
        'registrado_por' => $datos['registrado_por'] ?? 'SISTEMA',
        'ultima_actualizacion' => date('Y-m-d H:i:s'),
        
        // Histórico de valoraciones (referencias)
        'valoraciones' => []
    ];
    
    $VICTIMAS[$id] = $nueva_victima;
    
    // Registrar auditoría
    registrarAuditoria(
        $datos['registrado_por'] ?? 'SISTEMA',
        'VICTIMA_CREADA',
        "Nueva víctima registrada: $id - {$nueva_victima['nombre']} {$nueva_victima['apellidos']}"
    );
    
    return $nueva_victima;
}

/**
 * Actualiza los datos de una víctima
 */
function actualizarVictima($id, $datos, $usuario = 'SISTEMA') {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    // Campos actualizables
    $campos_actualizables = [
        'nombre', 'apellidos', 'nacionalidad', 'sexo',
        'domicilio', 'domicilio_coincide_agresor', 'lugares_frecuentes',
        'telefono', 'telefonos_adicionales', 'email', 'preferencia_contacto',
        'idioma', 'necesita_interprete',
        'estado_reproductivo', 'embarazada', 'fecha_probable_parto',
        'discapacidad', 'tipo_discapacidad', 'enfermedad_cronica', 'detalles_enfermedad',
        'consumo_toxicos', 'tipo_toxicos',
        'salud_mental', 'ideas_suicidas', 'intentos_suicidas_previos', 'detalles_salud_mental',
        'situacion_economica', 'dependiente_economicamente',
        'red_apoyo', 'tiene_apoyo_familiar', 'tiene_apoyo_amigos', 'servicios_sociales',
        'vivienda_compartida_con_agresor', 'propiedad_vivienda',
        'relacion_laboral', 'tipo_empleo', 'jornada_laboral',
        'tiene_menores', 'menores', 'custodia_hijos', 'tipo_custodia',
        'observaciones'
    ];
    
    foreach ($campos_actualizables as $campo) {
        if (isset($datos[$campo])) {
            $VICTIMAS[$id][$campo] = is_string($datos[$campo]) ? sanitizar($datos[$campo]) : $datos[$campo];
        }
    }
    
    // Recalcular edad si cambia fecha de nacimiento
    if (isset($datos['fecha_nacimiento'])) {
        $VICTIMAS[$id]['fecha_nacimiento'] = $datos['fecha_nacimiento'];
        $VICTIMAS[$id]['edad'] = calcularEdad($datos['fecha_nacimiento']);
    }
    
    // Actualizar contraseña si se proporciona
    if (!empty($datos['password'])) {
        $VICTIMAS[$id]['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
    }
    
    $VICTIMAS[$id]['ultima_actualizacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria(
        $usuario,
        'VICTIMA_ACTUALIZADA',
        "Víctima actualizada: $id"
    );
    
    return $VICTIMAS[$id];
}

/**
 * Añade una valoración al histórico de la víctima
 */
function agregarValoracionVictima($id_victima, $id_valoracion) {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id_victima])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    if (!in_array($id_valoracion, $VICTIMAS[$id_victima]['valoraciones'])) {
        $VICTIMAS[$id_victima]['valoraciones'][] = $id_valoracion;
    }
    
    return true;
}

/**
 * Obtiene el historial de valoraciones de una víctima
 */
function obtenerValoracionesVictima($id_victima) {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id_victima])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    return $VICTIMAS[$id_victima]['valoraciones'];
}

/**
 * Desactiva una víctima (no se elimina)
 */
function desactivarVictima($id, $usuario = 'SISTEMA') {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    $VICTIMAS[$id]['activo'] = false;
    $VICTIMAS[$id]['fecha_desactivacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria($usuario, 'VICTIMA_DESACTIVADA', "Víctima desactivada: $id");
    
    return ['mensaje' => 'Víctima desactivada correctamente'];
}

/**
 * Reactiva una víctima
 */
function activarVictima($id, $usuario = 'SISTEMA') {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    $VICTIMAS[$id]['activo'] = true;
    $VICTIMAS[$id]['fecha_reactivacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria($usuario, 'VICTIMA_REACTIVADA', "Víctima reactivada: $id");
    
    return ['mensaje' => 'Víctima reactivada correctamente'];
}

/**
 * Busca víctimas por nombre o apellidos
 */
function buscarVictimasPorNombre($termino) {
    global $VICTIMAS;
    
    $termino = strtolower($termino);
    $resultados = [];
    
    foreach ($VICTIMAS as $victima) {
        $nombre_completo = strtolower($victima['nombre'] . ' ' . $victima['apellidos']);
        if (strpos($nombre_completo, $termino) !== false) {
            $resultados[] = $victima;
        }
    }
    
    return $resultados;
}

/**
 * Lista víctimas activas
 */
function listarVictimasActivas() {
    global $VICTIMAS;
    
    return array_filter($VICTIMAS, function($v) {
        return $v['activo'] === true;
    });
}

/**
 * Obtiene estadísticas de una víctima
 */
function obtenerEstadisticasVictima($id) {
    global $VICTIMAS;
    
    if (!isset($VICTIMAS[$id])) {
        return ['error' => 'Víctima no encontrada'];
    }
    
    $victima = $VICTIMAS[$id];
    
    return [
        'id' => $victima['id'],
        'nombre_completo' => $victima['nombre'] . ' ' . $victima['apellidos'],
        'edad' => $victima['edad'],
        'num_valoraciones' => count($victima['valoraciones']),
        'fecha_registro' => $victima['fecha_registro'],
        'ultima_actualizacion' => $victima['ultima_actualizacion'],
        'activo' => $victima['activo']
    ];
}

/**
 * Valida credenciales de víctima (para acceso al sistema)
 */
function validarCredencialesVictima($usuario, $password) {
    global $VICTIMAS;
    
    foreach ($VICTIMAS as $victima) {
        if ($victima['usuario'] === $usuario && $victima['password']) {
            if (password_verify($password, $victima['password'])) {
                if (!$victima['activo']) {
                    return ['error' => 'Usuario desactivado'];
                }
                
                registrarAuditoria($usuario, 'LOGIN_VICTIMA_EXITOSO', 'Inicio de sesión correcto');
                
                // Retornar datos sin la contraseña
                $datos = $victima;
                unset($datos['password']);
                return $datos;
            }
        }
    }
    
    registrarAuditoria($usuario, 'LOGIN_VICTIMA_FALLIDO', 'Intento de inicio de sesión fallido');
    return ['error' => 'Credenciales incorrectas'];
}

// =============================================================================
// EXPORTAR/IMPORTAR (para persistencia)
// =============================================================================

/**
 * Guarda los datos en un archivo JSON
 */
function guardarVictimas() {
    global $VICTIMAS;
    $archivo = __DIR__ . '/data_victimas.json';
    return file_put_contents($archivo, json_encode($VICTIMAS, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Carga los datos desde un archivo JSON
 */
function cargarVictimas() {
    global $VICTIMAS;
    $archivo = __DIR__ . '/data_victimas.json';
    if (file_exists($archivo)) {
        $data = file_get_contents($archivo);
        $VICTIMAS = json_decode($data, true);
    }
}

// Cargar datos al incluir el archivo
cargarVictimas();

?>
