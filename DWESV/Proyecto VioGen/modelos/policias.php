<?php
/**
 * MODELO: POLICÍAS
 * Base de datos simulada con arrays para gestionar policías y evaluadores
 */

require_once __DIR__ . '/../config.php';

// =============================================================================
// ALMACENAMIENTO EN MEMORIA (simulación de BD)
// =============================================================================
$POLICIAS = [
    'P001' => [
        'id' => 'P001',
        'nombre' => 'María',
        'apellidos' => 'García López',
        'tipo_documento' => 'DNI',
        'num_documento' => '12345678A',
        'email' => 'maria.garcia@policia.es',
        'telefono' => '600111222',
        'usuario' => 'mgarcia',
        'password' => password_hash('policia123', PASSWORD_DEFAULT), // Contraseña: policia123
        'rol' => ROL_POLICIA,
        'unidad_policial' => 'Unidad de Violencia de Género - Madrid Centro',
        'placa' => 'UVG-001',
        'activo' => true,
        'fecha_alta' => '2023-01-15',
        'valoraciones_realizadas' => 0
    ],
    'P002' => [
        'id' => 'P002',
        'nombre' => 'Carlos',
        'apellidos' => 'Martínez Sánchez',
        'tipo_documento' => 'DNI',
        'num_documento' => '87654321B',
        'email' => 'carlos.martinez@policia.es',
        'telefono' => '600333444',
        'usuario' => 'cmartinez',
        'password' => password_hash('policia123', PASSWORD_DEFAULT),
        'rol' => ROL_POLICIA,
        'unidad_policial' => 'Unidad de Violencia de Género - Madrid Norte',
        'placa' => 'UVG-002',
        'activo' => true,
        'fecha_alta' => '2023-03-20',
        'valoraciones_realizadas' => 0
    ],
    'ADMIN' => [
        'id' => 'ADMIN',
        'nombre' => 'Administrador',
        'apellidos' => 'Sistema',
        'tipo_documento' => 'DNI',
        'num_documento' => '00000000A',
        'email' => 'admin@viogen.es',
        'telefono' => '600000000',
        'usuario' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT), // Contraseña: admin123
        'rol' => ROL_ADMIN,
        'unidad_policial' => 'Administración Central',
        'placa' => 'ADMIN-000',
        'activo' => true,
        'fecha_alta' => '2023-01-01',
        'valoraciones_realizadas' => 0
    ]
];

// =============================================================================
// FUNCIONES CRUD
// =============================================================================

/**
 * Obtiene todos los policías
 */
function obtenerTodosPolicias() {
    global $POLICIAS;
    return $POLICIAS;
}

/**
 * Busca un policía por ID
 */
function buscarPoliciaPorId($id) {
    global $POLICIAS;
    return $POLICIAS[$id] ?? null;
}

/**
 * Busca un policía por nombre de usuario
 */
function buscarPoliciaPorUsuario($usuario) {
    global $POLICIAS;
    foreach ($POLICIAS as $policia) {
        if ($policia['usuario'] === $usuario) {
            return $policia;
        }
    }
    return null;
}

/**
 * Busca un policía por número de documento
 */
function buscarPoliciaPorDocumento($num_documento) {
    global $POLICIAS;
    foreach ($POLICIAS as $policia) {
        if ($policia['num_documento'] === $num_documento) {
            return $policia;
        }
    }
    return null;
}

/**
 * Valida las credenciales de un policía
 */
function validarCredencialesPolicia($usuario, $password) {
    $policia = buscarPoliciaPorUsuario($usuario);
    
    if ($policia && password_verify($password, $policia['password'])) {
        if (!$policia['activo']) {
            return ['error' => 'Usuario desactivado'];
        }
        
        // Registrar auditoría
        registrarAuditoria($usuario, 'LOGIN_EXITOSO', 'Inicio de sesión correcto');
        
        // Retornar datos sin la contraseña
        unset($policia['password']);
        return $policia;
    }
    
    // Registrar intento fallido
    registrarAuditoria($usuario, 'LOGIN_FALLIDO', 'Intento de inicio de sesión fallido');
    return ['error' => 'Credenciales incorrectas'];
}

/**
 * Crea un nuevo policía
 */
function crearPolicia($datos) {
    global $POLICIAS;
    
    // Validar datos obligatorios
    $campos_requeridos = ['nombre', 'apellidos', 'num_documento', 'usuario', 'password', 'unidad_policial'];
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo])) {
            return ['error' => "El campo $campo es obligatorio"];
        }
    }
    
    // Verificar si el usuario ya existe
    if (buscarPoliciaPorUsuario($datos['usuario'])) {
        return ['error' => 'El nombre de usuario ya existe'];
    }
    
    // Verificar si el documento ya existe
    if (buscarPoliciaPorDocumento($datos['num_documento'])) {
        return ['error' => 'El número de documento ya está registrado'];
    }
    
    // Generar ID
    $id = generarID('P');
    
    // Crear nuevo policía
    $nuevo_policia = [
        'id' => $id,
        'nombre' => sanitizar($datos['nombre']),
        'apellidos' => sanitizar($datos['apellidos']),
        'tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
        'num_documento' => sanitizar($datos['num_documento']),
        'email' => sanitizar($datos['email'] ?? ''),
        'telefono' => sanitizar($datos['telefono'] ?? ''),
        'usuario' => sanitizar($datos['usuario']),
        'password' => password_hash($datos['password'], PASSWORD_DEFAULT),
        'rol' => $datos['rol'] ?? ROL_POLICIA,
        'unidad_policial' => sanitizar($datos['unidad_policial']),
        'placa' => sanitizar($datos['placa'] ?? ''),
        'activo' => true,
        'fecha_alta' => date('Y-m-d'),
        'valoraciones_realizadas' => 0
    ];
    
    $POLICIAS[$id] = $nuevo_policia;
    
    // Registrar auditoría
    registrarAuditoria($datos['usuario'], 'POLICIA_CREADO', "Nuevo policía creado: $id");
    
    unset($nuevo_policia['password']);
    return $nuevo_policia;
}

/**
 * Actualiza los datos de un policía
 */
function actualizarPolicia($id, $datos) {
    global $POLICIAS;
    
    if (!isset($POLICIAS[$id])) {
        return ['error' => 'Policía no encontrado'];
    }
    
    // Actualizar solo los campos proporcionados
    $campos_actualizables = ['nombre', 'apellidos', 'email', 'telefono', 'unidad_policial', 'placa'];
    
    foreach ($campos_actualizables as $campo) {
        if (isset($datos[$campo])) {
            $POLICIAS[$id][$campo] = sanitizar($datos[$campo]);
        }
    }
    
    // Actualizar contraseña si se proporciona
    if (!empty($datos['password'])) {
        $POLICIAS[$id]['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
    }
    
    // Registrar auditoría
    registrarAuditoria($POLICIAS[$id]['usuario'], 'POLICIA_ACTUALIZADO', "Policía actualizado: $id");
    
    $policia_actualizado = $POLICIAS[$id];
    unset($policia_actualizado['password']);
    return $policia_actualizado;
}

/**
 * Desactiva un policía (no se elimina, solo se desactiva)
 */
function desactivarPolicia($id) {
    global $POLICIAS;
    
    if (!isset($POLICIAS[$id])) {
        return ['error' => 'Policía no encontrado'];
    }
    
    $POLICIAS[$id]['activo'] = false;
    
    // Registrar auditoría
    registrarAuditoria($POLICIAS[$id]['usuario'], 'POLICIA_DESACTIVADO', "Policía desactivado: $id");
    
    return ['mensaje' => 'Policía desactivado correctamente'];
}

/**
 * Reactiva un policía
 */
function activarPolicia($id) {
    global $POLICIAS;
    
    if (!isset($POLICIAS[$id])) {
        return ['error' => 'Policía no encontrado'];
    }
    
    $POLICIAS[$id]['activo'] = true;
    
    // Registrar auditoría
    registrarAuditoria($POLICIAS[$id]['usuario'], 'POLICIA_ACTIVADO', "Policía reactivado: $id");
    
    return ['mensaje' => 'Policía reactivado correctamente'];
}

/**
 * Incrementa el contador de valoraciones realizadas
 */
function incrementarValoracionesPolicia($id) {
    global $POLICIAS;
    
    if (isset($POLICIAS[$id])) {
        $POLICIAS[$id]['valoraciones_realizadas']++;
        return true;
    }
    
    return false;
}

/**
 * Obtiene estadísticas de un policía
 */
function obtenerEstadisticasPolicia($id) {
    global $POLICIAS;
    
    if (!isset($POLICIAS[$id])) {
        return ['error' => 'Policía no encontrado'];
    }
    
    $policia = $POLICIAS[$id];
    
    return [
        'id' => $policia['id'],
        'nombre_completo' => $policia['nombre'] . ' ' . $policia['apellidos'],
        'unidad_policial' => $policia['unidad_policial'],
        'valoraciones_realizadas' => $policia['valoraciones_realizadas'],
        'fecha_alta' => $policia['fecha_alta'],
        'activo' => $policia['activo']
    ];
}

/**
 * Lista todos los policías activos
 */
function listarPolicasActivos() {
    global $POLICIAS;
    
    $activos = [];
    foreach ($POLICIAS as $policia) {
        if ($policia['activo'] && $policia['rol'] === ROL_POLICIA) {
            unset($policia['password']);
            $activos[] = $policia;
        }
    }
    
    return $activos;
}

/**
 * Cambia la contraseña de un policía
 */
function cambiarPasswordPolicia($id, $password_actual, $password_nueva) {
    global $POLICIAS;
    
    if (!isset($POLICIAS[$id])) {
        return ['error' => 'Policía no encontrado'];
    }
    
    // Verificar contraseña actual
    if (!password_verify($password_actual, $POLICIAS[$id]['password'])) {
        registrarAuditoria($POLICIAS[$id]['usuario'], 'CAMBIO_PASSWORD_FALLIDO', 'Contraseña actual incorrecta');
        return ['error' => 'La contraseña actual es incorrecta'];
    }
    
    // Actualizar contraseña
    $POLICIAS[$id]['password'] = password_hash($password_nueva, PASSWORD_DEFAULT);
    
    // Registrar auditoría
    registrarAuditoria($POLICIAS[$id]['usuario'], 'CAMBIO_PASSWORD_EXITOSO', 'Contraseña cambiada correctamente');
    
    return ['mensaje' => 'Contraseña actualizada correctamente'];
}

// =============================================================================
// EXPORTAR/IMPORTAR (para persistencia)
// =============================================================================

/**
 * Guarda los datos en un archivo JSON (persistencia simulada)
 */
function guardarPolicias() {
    global $POLICIAS;
    $archivo = __DIR__ . '/data_policias.json';
    return file_put_contents($archivo, json_encode($POLICIAS, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Carga los datos desde un archivo JSON
 */
function cargarPolicias() {
    global $POLICIAS;
    $archivo = __DIR__ . '/data_policias.json';
    if (file_exists($archivo)) {
        $data = file_get_contents($archivo);
        $POLICIAS = json_decode($data, true);
    }
}

// Cargar datos al incluir el archivo
cargarPolicias();

?>
