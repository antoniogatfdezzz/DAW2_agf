<?php
/**
 * MODELO: AGRESORES
 * Base de datos simulada con arrays para gestionar agresores de violencia de género
 * Incluye todos los campos requeridos por VPR/VPER
 */

require_once __DIR__ . '/../config.php';

// =============================================================================
// ALMACENAMIENTO EN MEMORIA (simulación de BD)
// =============================================================================
$AGRESORES = [];

// =============================================================================
// FUNCIONES CRUD
// =============================================================================

/**
 * Obtiene todos los agresores
 */
function obtenerTodosAgresores() {
    global $AGRESORES;
    return $AGRESORES;
}

/**
 * Busca un agresor por ID
 */
function buscarAgresorPorId($id) {
    global $AGRESORES;
    return $AGRESORES[$id] ?? null;
}

/**
 * Busca un agresor por número de documento
 */
function buscarAgresorPorDocumento($num_documento) {
    global $AGRESORES;
    foreach ($AGRESORES as $agresor) {
        if ($agresor['num_documento'] === $num_documento) {
            return $agresor;
        }
    }
    return null;
}

/**
 * Crea un nuevo agresor
 */
function crearAgresor($datos) {
    global $AGRESORES;
    
    // Validar datos obligatorios
    $campos_requeridos = ['nombre', 'apellidos'];
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo])) {
            return ['error' => "El campo $campo es obligatorio"];
        }
    }
    
    // Si existe documento, verificar duplicados
    if (!empty($datos['num_documento'])) {
        if (buscarAgresorPorDocumento($datos['num_documento'])) {
            return ['error' => 'El número de documento ya está registrado'];
        }
    }
    
    // Generar ID
    $id = generarID('A');
    
    // Calcular edad si existe fecha de nacimiento
    $edad = null;
    if (!empty($datos['fecha_nacimiento'])) {
        $edad = calcularEdad($datos['fecha_nacimiento']);
    }
    
    // Crear nuevo agresor con todos los campos
    $nuevo_agresor = [
        'id' => $id,
        
        // Datos personales básicos
        'nombre' => sanitizar($datos['nombre']),
        'apellidos' => sanitizar($datos['apellidos']),
        'tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
        'num_documento' => sanitizar($datos['num_documento'] ?? ''),
        'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
        'edad' => $edad,
        'nacionalidad' => sanitizar($datos['nacionalidad'] ?? ''),
        'sexo' => $datos['sexo'] ?? 'Hombre',
        
        // Contacto
        'domicilio' => sanitizar($datos['domicilio'] ?? ''),
        'domicilio_coincide_victima' => $datos['domicilio_coincide_victima'] ?? false,
        'lugares_frecuentes' => $datos['lugares_frecuentes'] ?? [],
        'telefono' => sanitizar($datos['telefono'] ?? ''),
        'telefonos_adicionales' => $datos['telefonos_adicionales'] ?? [],
        
        // Situación laboral y económica
        'empleo' => $datos['empleo'] ?? 'Desconocido',
        'tipo_empleo' => $datos['tipo_empleo'] ?? '',
        'situacion_empleo' => $datos['situacion_empleo'] ?? 'Desconocida',
        
        // Relación con la víctima
        'relacion_con_victima' => $datos['relacion_con_victima'] ?? 'Pareja',
        'convivencia_actual' => $datos['convivencia_actual'] ?? false,
        
        // Antecedentes y historial (CRÍTICO para valoración)
        'antecedentes_penales' => $datos['antecedentes_penales'] ?? false,
        'detalles_antecedentes' => $datos['detalles_antecedentes'] ?? '',
        'quebrantamientos_previos' => $datos['quebrantamientos_previos'] ?? false,
        'detalles_quebrantamientos' => $datos['detalles_quebrantamientos'] ?? [],
        'violencia_otra_persona' => $datos['violencia_otra_persona'] ?? false,
        'detalles_violencia_otra_persona' => $datos['detalles_violencia_otra_persona'] ?? '',
        
        // Historial de agresiones
        'historia_agresiones_previas' => $datos['historia_agresiones_previas'] ?? false,
        'agresiones_fisicas' => $datos['agresiones_fisicas'] ?? false,
        'agresiones_sexuales' => $datos['agresiones_sexuales'] ?? false,
        'detalles_agresiones' => $datos['detalles_agresiones'] ?? '',
        'violencia_otras_parejas' => $datos['violencia_otras_parejas'] ?? false,
        'detalles_otras_parejas' => $datos['detalles_otras_parejas'] ?? '',
        
        // Adicciones (CRÍTICO)
        'alcohol_drogas' => $datos['alcohol_drogas'] ?? false,
        'tipo_sustancias' => $datos['tipo_sustancias'] ?? [],
        'gravedad_adiccion' => $datos['gravedad_adiccion'] ?? '',
        
        // Salud mental (CRÍTICO)
        'salud_mental' => $datos['salud_mental'] ?? 'Estable',
        'trastorno_diagnosticado' => $datos['trastorno_diagnosticado'] ?? false,
        'tipo_trastorno' => $datos['tipo_trastorno'] ?? '',
        'en_tratamiento' => $datos['en_tratamiento'] ?? false,
        'intentos_suicidio' => $datos['intentos_suicidio'] ?? false,
        'ideas_suicidas' => $datos['ideas_suicidas'] ?? false,
        'detalles_salud_mental' => $datos['detalles_salud_mental'] ?? '',
        
        // Armas (CRÍTICO - LETALIDAD)
        'posesion_armas' => $datos['posesion_armas'] ?? false,
        'tipo_armas' => $datos['tipo_armas'] ?? [],
        'tiene_acceso_armas' => $datos['tiene_acceso_armas'] ?? false,
        'detalles_armas' => $datos['detalles_armas'] ?? '',
        
        // Antecedentes familiares
        'antecedentes_familiares_violencia' => $datos['antecedentes_familiares_violencia'] ?? false,
        'detalles_antecedentes_familiares' => $datos['detalles_antecedentes_familiares'] ?? '',
        
        // Características de comportamiento
        'celos_exagerados' => $datos['celos_exagerados'] ?? false,
        'conductas_control' => $datos['conductas_control'] ?? false,
        'conductas_acoso' => $datos['conductas_acoso'] ?? false,
        'problemas_personales_recientes' => $datos['problemas_personales_recientes'] ?? false,
        'detalles_problemas_personales' => $datos['detalles_problemas_personales'] ?? '',
        
        // Estado actual y paradero
        'paradero_conocido' => $datos['paradero_conocido'] ?? true,
        'fugado' => $datos['fugado'] ?? false,
        'ubicacion_actual' => $datos['ubicacion_actual'] ?? '',
        
        // Medidas judiciales actuales
        'medidas_cautelares_activas' => $datos['medidas_cautelares_activas'] ?? false,
        'tipo_medidas' => $datos['tipo_medidas'] ?? [],
        'cumplimiento_medidas' => $datos['cumplimiento_medidas'] ?? 'No aplica',
        
        // Observaciones generales
        'observaciones' => $datos['observaciones'] ?? '',
        
        // Metadata
        'activo' => true,
        'fecha_registro' => date('Y-m-d H:i:s'),
        'registrado_por' => $datos['registrado_por'] ?? 'SISTEMA',
        'ultima_actualizacion' => date('Y-m-d H:i:s'),
        
        // Histórico de valoraciones asociadas
        'valoraciones' => []
    ];
    
    $AGRESORES[$id] = $nuevo_agresor;
    
    // Registrar auditoría
    registrarAuditoria(
        $datos['registrado_por'] ?? 'SISTEMA',
        'AGRESOR_CREADO',
        "Nuevo agresor registrado: $id - {$nuevo_agresor['nombre']} {$nuevo_agresor['apellidos']}"
    );
    
    return $nuevo_agresor;
}

/**
 * Actualiza los datos de un agresor
 */
function actualizarAgresor($id, $datos, $usuario = 'SISTEMA') {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    // Campos actualizables
    $campos_actualizables = [
        'nombre', 'apellidos', 'tipo_documento', 'num_documento', 'nacionalidad', 'sexo',
        'domicilio', 'domicilio_coincide_victima', 'lugares_frecuentes',
        'telefono', 'telefonos_adicionales',
        'empleo', 'tipo_empleo', 'situacion_empleo',
        'relacion_con_victima', 'convivencia_actual',
        'antecedentes_penales', 'detalles_antecedentes',
        'quebrantamientos_previos', 'detalles_quebrantamientos',
        'violencia_otra_persona', 'detalles_violencia_otra_persona',
        'historia_agresiones_previas', 'agresiones_fisicas', 'agresiones_sexuales',
        'detalles_agresiones', 'violencia_otras_parejas', 'detalles_otras_parejas',
        'alcohol_drogas', 'tipo_sustancias', 'gravedad_adiccion',
        'salud_mental', 'trastorno_diagnosticado', 'tipo_trastorno',
        'en_tratamiento', 'intentos_suicidio', 'ideas_suicidas', 'detalles_salud_mental',
        'posesion_armas', 'tipo_armas', 'tiene_acceso_armas', 'detalles_armas',
        'antecedentes_familiares_violencia', 'detalles_antecedentes_familiares',
        'celos_exagerados', 'conductas_control', 'conductas_acoso',
        'problemas_personales_recientes', 'detalles_problemas_personales',
        'paradero_conocido', 'fugado', 'ubicacion_actual',
        'medidas_cautelares_activas', 'tipo_medidas', 'cumplimiento_medidas',
        'observaciones'
    ];
    
    foreach ($campos_actualizables as $campo) {
        if (isset($datos[$campo])) {
            $AGRESORES[$id][$campo] = is_string($datos[$campo]) ? sanitizar($datos[$campo]) : $datos[$campo];
        }
    }
    
    // Recalcular edad si cambia fecha de nacimiento
    if (isset($datos['fecha_nacimiento'])) {
        $AGRESORES[$id]['fecha_nacimiento'] = $datos['fecha_nacimiento'];
        $AGRESORES[$id]['edad'] = calcularEdad($datos['fecha_nacimiento']);
    }
    
    $AGRESORES[$id]['ultima_actualizacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria(
        $usuario,
        'AGRESOR_ACTUALIZADO',
        "Agresor actualizado: $id"
    );
    
    return $AGRESORES[$id];
}

/**
 * Añade una valoración al histórico del agresor
 */
function agregarValoracionAgresor($id_agresor, $id_valoracion) {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id_agresor])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    if (!in_array($id_valoracion, $AGRESORES[$id_agresor]['valoraciones'])) {
        $AGRESORES[$id_agresor]['valoraciones'][] = $id_valoracion;
    }
    
    return true;
}

/**
 * Obtiene el historial de valoraciones de un agresor
 */
function obtenerValoracionesAgresor($id_agresor) {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id_agresor])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    return $AGRESORES[$id_agresor]['valoraciones'];
}

/**
 * Desactiva un agresor (no se elimina)
 */
function desactivarAgresor($id, $usuario = 'SISTEMA') {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    $AGRESORES[$id]['activo'] = false;
    $AGRESORES[$id]['fecha_desactivacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria($usuario, 'AGRESOR_DESACTIVADO', "Agresor desactivado: $id");
    
    return ['mensaje' => 'Agresor desactivado correctamente'];
}

/**
 * Reactiva un agresor
 */
function activarAgresor($id, $usuario = 'SISTEMA') {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    $AGRESORES[$id]['activo'] = true;
    $AGRESORES[$id]['fecha_reactivacion'] = date('Y-m-d H:i:s');
    
    // Registrar auditoría
    registrarAuditoria($usuario, 'AGRESOR_REACTIVADO', "Agresor reactivado: $id");
    
    return ['mensaje' => 'Agresor reactivado correctamente'];
}

/**
 * Busca agresores por nombre o apellidos
 */
function buscarAgresoresPorNombre($termino) {
    global $AGRESORES;
    
    $termino = strtolower($termino);
    $resultados = [];
    
    foreach ($AGRESORES as $agresor) {
        $nombre_completo = strtolower($agresor['nombre'] . ' ' . $agresor['apellidos']);
        if (strpos($nombre_completo, $termino) !== false) {
            $resultados[] = $agresor;
        }
    }
    
    return $resultados;
}

/**
 * Lista agresores activos
 */
function listarAgresoresActivos() {
    global $AGRESORES;
    
    return array_filter($AGRESORES, function($a) {
        return $a['activo'] === true;
    });
}

/**
 * Lista agresores con órdenes de alejamiento activas
 */
function listarAgresoresConOrdenesActivas() {
    global $AGRESORES;
    
    return array_filter($AGRESORES, function($a) {
        return $a['activo'] === true && $a['medidas_cautelares_activas'] === true;
    });
}

/**
 * Lista agresores con antecedentes de quebrantamientos
 */
function listarAgresoresConQuebrantamientos() {
    global $AGRESORES;
    
    return array_filter($AGRESORES, function($a) {
        return $a['quebrantamientos_previos'] === true;
    });
}

/**
 * Obtiene estadísticas de un agresor
 */
function obtenerEstadisticasAgresor($id) {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    $agresor = $AGRESORES[$id];
    
    return [
        'id' => $agresor['id'],
        'nombre_completo' => $agresor['nombre'] . ' ' . $agresor['apellidos'],
        'edad' => $agresor['edad'],
        'antecedentes_penales' => $agresor['antecedentes_penales'],
        'quebrantamientos_previos' => $agresor['quebrantamientos_previos'],
        'posesion_armas' => $agresor['posesion_armas'],
        'num_valoraciones' => count($agresor['valoraciones']),
        'medidas_activas' => $agresor['medidas_cautelares_activas'],
        'fecha_registro' => $agresor['fecha_registro'],
        'activo' => $agresor['activo']
    ];
}

/**
 * Obtiene factores de riesgo de un agresor
 */
function obtenerFactoresRiesgoAgresor($id) {
    global $AGRESORES;
    
    if (!isset($AGRESORES[$id])) {
        return ['error' => 'Agresor no encontrado'];
    }
    
    $agresor = $AGRESORES[$id];
    $factores = [];
    
    // Identificar factores de riesgo críticos
    if ($agresor['antecedentes_penales']) {
        $factores[] = ['nivel' => 'ALTO', 'factor' => 'Antecedentes penales'];
    }
    
    if ($agresor['quebrantamientos_previos']) {
        $factores[] = ['nivel' => 'CRÍTICO', 'factor' => 'Quebrantamientos previos'];
    }
    
    if ($agresor['posesion_armas']) {
        $factores[] = ['nivel' => 'CRÍTICO', 'factor' => 'Posesión de armas'];
    }
    
    if ($agresor['agresiones_fisicas']) {
        $factores[] = ['nivel' => 'ALTO', 'factor' => 'Historial de agresiones físicas'];
    }
    
    if ($agresor['agresiones_sexuales']) {
        $factores[] = ['nivel' => 'CRÍTICO', 'factor' => 'Historial de agresiones sexuales'];
    }
    
    if ($agresor['alcohol_drogas']) {
        $factores[] = ['nivel' => 'MEDIO', 'factor' => 'Consumo de alcohol/drogas'];
    }
    
    if ($agresor['trastorno_diagnosticado']) {
        $factores[] = ['nivel' => 'MEDIO', 'factor' => 'Trastorno mental diagnosticado'];
    }
    
    if ($agresor['intentos_suicidio']) {
        $factores[] = ['nivel' => 'ALTO', 'factor' => 'Intentos de suicidio'];
    }
    
    if ($agresor['fugado'] || !$agresor['paradero_conocido']) {
        $factores[] = ['nivel' => 'CRÍTICO', 'factor' => 'Paradero desconocido'];
    }
    
    if ($agresor['celos_exagerados']) {
        $factores[] = ['nivel' => 'MEDIO', 'factor' => 'Celos exagerados'];
    }
    
    if ($agresor['conductas_control']) {
        $factores[] = ['nivel' => 'MEDIO', 'factor' => 'Conductas de control'];
    }
    
    return $factores;
}

// =============================================================================
// EXPORTAR/IMPORTAR (para persistencia)
// =============================================================================

/**
 * Guarda los datos en un archivo JSON
 */
function guardarAgresores() {
    global $AGRESORES;
    $archivo = __DIR__ . '/data_agresores.json';
    return file_put_contents($archivo, json_encode($AGRESORES, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Carga los datos desde un archivo JSON
 */
function cargarAgresores() {
    global $AGRESORES;
    $archivo = __DIR__ . '/data_agresores.json';
    if (file_exists($archivo)) {
        $data = file_get_contents($archivo);
        $AGRESORES = json_decode($data, true);
    }
}

// Cargar datos al incluir el archivo
cargarAgresores();

?>
