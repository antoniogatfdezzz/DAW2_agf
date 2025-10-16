<?php
/**
 * PROYECTO VIOGEN - CONFIGURACIÓN GLOBAL
 * Sistema de Valoración Policial del Riesgo (VPR) y Valoración Policial de Evolución del Riesgo (VPER)
 * para la protección de víctimas de violencia de género
 */

// =============================================================================
// 1. CONFIGURACIÓN GENERAL
// =============================================================================
define('APP_NAME', 'Proyecto VioGén');
define('APP_VERSION', '1.0');
define('TIMEZONE', 'Europe/Madrid');
date_default_timezone_set(TIMEZONE);

// =============================================================================
// 2. NIVELES DE RIESGO
// =============================================================================
define('NIVEL_NO_APRECIADO', 0);
define('NIVEL_BAJO', 1);
define('NIVEL_MEDIO', 2);
define('NIVEL_ALTO', 3);
define('NIVEL_EXTREMO', 4);

$NIVELES_RIESGO = [
    NIVEL_NO_APRECIADO => ['nombre' => 'No Apreciado', 'color' => '#4CAF50', 'codigo' => 'NA'],
    NIVEL_BAJO => ['nombre' => 'Bajo', 'color' => '#8BC34A', 'codigo' => 'B'],
    NIVEL_MEDIO => ['nombre' => 'Medio', 'color' => '#FFC107', 'codigo' => 'M'],
    NIVEL_ALTO => ['nombre' => 'Alto', 'color' => '#FF5722', 'codigo' => 'A'],
    NIVEL_EXTREMO => ['nombre' => 'Extremo', 'color' => '#D32F2F', 'codigo' => 'E']
];

// Constantes de colores para fácil acceso
define('COLOR_NO_APRECIADO', '#4CAF50');
define('COLOR_BAJO', '#8BC34A');
define('COLOR_MEDIO', '#FFC107');
define('COLOR_ALTO', '#FF5722');
define('COLOR_EXTREMO', '#D32F2F');

// =============================================================================
// 3. UMBRALES DE PUNTUACIÓN (algoritmo actuarial)
// =============================================================================
define('UMBRAL_NO_APRECIADO_MIN', 0);
define('UMBRAL_NO_APRECIADO_MAX', 9);
define('UMBRAL_BAJO_MIN', 10);
define('UMBRAL_BAJO_MAX', 19);
define('UMBRAL_MEDIO_MIN', 20);
define('UMBRAL_MEDIO_MAX', 29);
define('UMBRAL_ALTO_MIN', 30);
define('UMBRAL_ALTO_MAX', 44);
define('UMBRAL_EXTREMO_MIN', 45);

// =============================================================================
// 4. PESOS DE INDICADORES (VPR 5.0-H)
// =============================================================================

// CRÍTICOS / LETALIDAD (8 puntos)
define('PESO_CRITICO', 8);
define('PESO_ALTO', 6);
define('PESO_MEDIO', 4);
define('PESO_BAJO', 2);
define('PESO_POSITIVO', -4);

$PESOS_INDICADORES = [
    // Factor 1: Historia de violencia (I-1 a I-6)
    'I1_violencia_psicologica' => PESO_MEDIO,
    'I2_violencia_fisica' => PESO_ALTO,           // CRÍTICO
    'I3_sexo_forzado' => PESO_CRITICO,            // LETALIDAD
    'I4_uso_armas_objetos' => PESO_CRITICO,       // LETALIDAD
    'I5_amenazas_planes' => PESO_CRITICO,         // LETALIDAD
    'I6_escalada_6m' => PESO_ALTO,                // CRÍTICO
    
    // Factor 2: Características del agresor (I-7 a I-23)
    'I7_celos_exagerados' => PESO_MEDIO,
    'I8_conductas_control' => PESO_MEDIO,
    'I9_acoso' => PESO_MEDIO,
    'I10_problemas_personales' => PESO_MEDIO,
    'I11_danos_materiales' => PESO_MEDIO,
    'I12_faltas_respeto_autoridad' => PESO_BAJO,
    'I13_agresiones_terceros_animales' => PESO_ALTO,
    'I14_amenazas_terceros' => PESO_MEDIO,
    'I15_antecedentes_penales' => PESO_ALTO,      // CRÍTICO
    'I16_quebrantamientos_previos' => PESO_ALTO,  // CRÍTICO
    'I17_antecedentes_agresiones_fisicas_sexuales' => PESO_ALTO, // CRÍTICO
    'I18_antecedentes_vg_otra_pareja' => PESO_MEDIO,
    'I19_trastorno_mental_agresor' => PESO_MEDIO,
    'I20_intentos_ideas_suicidio_agresor' => PESO_MEDIO,
    'I21_adicciones_agresor' => PESO_MEDIO,
    'I22_antecedentes_familiares_violencia' => PESO_BAJO,
    'I23_agresor_menor_24' => PESO_BAJO,
    
    // Factor 3: Vulnerabilidades de la víctima (I-24 a I-28)
    'I24_discapacidad_victima' => PESO_MEDIO,
    'I25_intentos_ideas_suicidio_victima' => PESO_MEDIO,
    'I26_adicciones_victima' => PESO_BAJO,
    'I27_carencia_apoyo_social' => PESO_BAJO,
    'I28_victima_extranjera' => PESO_BAJO,
    
    // Factor 4: Circunstancias relacionadas con menores (I-29 a I-31)
    'I29_victima_tiene_menores' => PESO_BAJO,
    'I30_violencia_amenaza_menores' => PESO_ALTO,
    'I31_victima_teme_por_integridad_menores' => PESO_MEDIO,
    
    // Factor 5: Circunstancias agravantes (I-32 a I-35)
    'I32_victima_ha_denunciado_otros_agresores' => PESO_BAJO,
    'I33_episodios_violencia_lateral' => PESO_BAJO,
    'I34_ruptura_reciente' => PESO_MEDIO,
    'I35_victima_piensa_agresor_capaz_matar' => PESO_CRITICO, // LETALIDAD
    
    // Percepción de riesgo
    'I36_victima_considera_riesgo' => PESO_ALTO,
    'I37_evaluador_acuerda_con_victima' => PESO_BAJO,
    
    // Indicadores VPER (evolución)
    'VPER_I12_agresor_fugado_paradero_desconocido' => PESO_ALTO,
    'VPER_I14_quebrantamientos_de_medidas' => PESO_ALTO,
    'VPER_I18_tramites_judiciales_separacion_no_deseados' => PESO_MEDIO,
    'VPER_I27_reanuda_convivencia' => PESO_ALTO,
    'VPER_I28_no_desea_declarar_retira_denuncia' => PESO_MEDIO,
    
    // Indicadores positivos (reducen riesgo)
    'VPER_I19_distanciamiento_agresor' => PESO_POSITIVO,
    'VPER_I20_actitud_pacifica' => PESO_POSITIVO,
    'VPER_I21_colaboracion_agresor' => PESO_POSITIVO,
    'VPER_I22_arrepentimiento' => PESO_POSITIVO,
    'VPER_I23_adhesion_programas' => PESO_POSITIVO,
    'VPER_I24_cumplimiento_regimen_separacion' => PESO_POSITIVO,
    'VPER_I25_estabilidad_laboral_agresor' => PESO_POSITIVO,
    'VPER_I26_apoyo_social_agresor' => PESO_POSITIVO,
    
    // Escala-H (letalidad extrema)
    'H_estrangulamiento' => PESO_CRITICO,
    'H_intento_homicidio_previo' => PESO_CRITICO,
    'H_amenaza_muerte_explicita' => PESO_CRITICO,
];

// =============================================================================
// 5. PLAZOS DE REVALORACIÓN (según nivel de riesgo)
// =============================================================================
$PLAZOS_REVALORIZACION = [
    NIVEL_NO_APRECIADO => 90,  // 90 días
    NIVEL_BAJO => 60,           // 60 días
    NIVEL_MEDIO => 30,          // 30 días
    NIVEL_ALTO => 7,            // 7 días
    NIVEL_EXTREMO => 3          // 72 horas (3 días)
];

// =============================================================================
// 6. TIPOS DE DOCUMENTOS
// =============================================================================
$TIPOS_DOCUMENTO = ['DNI', 'NIE', 'Pasaporte', 'Otro'];

// =============================================================================
// 7. FUENTES DE INFORMACIÓN
// =============================================================================
$FUENTES_INFORMACION = [
    'victima' => 'Víctima',
    'testigo' => 'Testigo',
    'parte_medico' => 'Parte Médico',
    'informe_judicial' => 'Informe Judicial',
    'bases_policiales' => 'Bases Policiales',
    'observacion_propia' => 'Observación Propia',
    'otra' => 'Otra'
];

// =============================================================================
// 8. TIPOS DE HECHOS (violencia)
// =============================================================================
$TIPOS_HECHO = [
    'fisica' => 'Violencia Física',
    'psicologica' => 'Violencia Psicológica',
    'sexual' => 'Violencia Sexual',
    'economica' => 'Violencia Económica',
    'patrimonial' => 'Violencia Patrimonial',
    'control' => 'Control / Coerción',
    'amenazas' => 'Amenazas',
    'stalking' => 'Acoso / Stalking',
    'otros' => 'Otros'
];

// =============================================================================
// 9. RELACIONES CON LA VÍCTIMA
// =============================================================================
$RELACIONES_VICTIMA = [
    'pareja_actual' => 'Pareja actual',
    'expareja' => 'Expareja',
    'conviviente' => 'Conviviente',
    'conyuge' => 'Cónyuge',
    'exconyuge' => 'Excónyuge',
    'otro' => 'Otro'
];

// =============================================================================
// 10. MEDIDAS DE PROTECCIÓN
// =============================================================================
$MEDIDAS_PROTECCION = [
    'orden_alejamiento' => 'Orden de Alejamiento',
    'orden_proteccion' => 'Orden de Protección',
    'patrullas' => 'Patrullas de vigilancia',
    'atenpro' => 'Sistema ATENPRO',
    'dispositivo_movil' => 'Dispositivo de Protección Móvil',
    'seguimiento_telefonico' => 'Seguimiento Telefónico',
    'servicios_sociales' => 'Derivación a Servicios Sociales',
    'atencion_psicologica' => 'Atención Psicológica',
    'casa_acogida' => 'Casa de Acogida',
    'otras' => 'Otras medidas'
];

// =============================================================================
// 11. ROLES DE USUARIO
// =============================================================================
define('ROL_ADMIN', 'admin');
define('ROL_POLICIA', 'policia');
define('ROL_VICTIMA', 'victima');

// =============================================================================
// 12. RUTAS DE ARCHIVOS
// =============================================================================
define('DIR_MODELOS', __DIR__ . '/modelos/');
define('DIR_VISTAS', __DIR__ . '/vistas/');
define('DIR_UPLOADS', __DIR__ . '/uploads/');
define('DIR_EVIDENCIAS', DIR_UPLOADS . 'evidencias/');
define('DIR_PARTES_MEDICOS', DIR_UPLOADS . 'partes_medicos/');

// Crear directorios si no existen
if (!file_exists(DIR_UPLOADS)) mkdir(DIR_UPLOADS, 0755, true);
if (!file_exists(DIR_EVIDENCIAS)) mkdir(DIR_EVIDENCIAS, 0755, true);
if (!file_exists(DIR_PARTES_MEDICOS)) mkdir(DIR_PARTES_MEDICOS, 0755, true);

// =============================================================================
// 13. FUNCIONES AUXILIARES
// =============================================================================

/**
 * Calcula el nivel de riesgo según la puntuación total
 */
function calcularNivelRiesgo($puntuacion) {
    if ($puntuacion >= UMBRAL_EXTREMO_MIN) return NIVEL_EXTREMO;
    if ($puntuacion >= UMBRAL_ALTO_MIN) return NIVEL_ALTO;
    if ($puntuacion >= UMBRAL_MEDIO_MIN) return NIVEL_MEDIO;
    if ($puntuacion >= UMBRAL_BAJO_MIN) return NIVEL_BAJO;
    return NIVEL_NO_APRECIADO;
}

/**
 * Obtiene el nombre del nivel de riesgo
 */
function obtenerNombreNivel($nivel) {
    global $NIVELES_RIESGO;
    return $NIVELES_RIESGO[$nivel]['nombre'] ?? 'Desconocido';
}

/**
 * Obtiene el color del nivel de riesgo
 */
function obtenerColorNivel($nivel) {
    global $NIVELES_RIESGO;
    return $NIVELES_RIESGO[$nivel]['color'] ?? '#CCCCCC';
}

/**
 * Calcula la fecha de próxima valoración VPER
 */
function calcularProximaValoracion($nivel, $fecha_actual = null) {
    global $PLAZOS_REVALORIZACION;
    if ($fecha_actual === null) $fecha_actual = date('Y-m-d H:i:s');
    $dias = $PLAZOS_REVALORIZACION[$nivel] ?? 90;
    return date('Y-m-d H:i:s', strtotime($fecha_actual . " +{$dias} days"));
}

/**
 * Genera un ID único para registros
 */
function generarID($prefijo = '') {
    return $prefijo . date('YmdHis') . rand(1000, 9999);
}

/**
 * Sanitiza entrada de usuario
 */
function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Registra eventos de auditoría
 */
function registrarAuditoria($usuario, $accion, $detalles = '') {
    $log = [
        'fecha_hora' => date('Y-m-d H:i:s'),
        'usuario' => $usuario,
        'accion' => $accion,
        'detalles' => $detalles,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI'
    ];
    
    $archivo_log = __DIR__ . '/logs/auditoria_' . date('Y-m') . '.log';
    if (!file_exists(__DIR__ . '/logs')) mkdir(__DIR__ . '/logs', 0755, true);
    
    file_put_contents(
        $archivo_log,
        json_encode($log, JSON_UNESCAPED_UNICODE) . PHP_EOL,
        FILE_APPEND
    );
    
    return true;
}

/**
 * Calcula la edad a partir de fecha de nacimiento
 */
function calcularEdad($fecha_nacimiento) {
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

/**
 * Valida formato de fecha
 */
function validarFecha($fecha) {
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d && $d->format('Y-m-d') === $fecha;
}

// =============================================================================
// 14. SESIÓN
// =============================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['rol']);
}

/**
 * Obtiene el rol del usuario actual
 */
function obtenerRolUsuario() {
    return $_SESSION['rol'] ?? null;
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function tieneRol($rol) {
    return obtenerRolUsuario() === $rol;
}

/**
 * Cierra la sesión
 */
function cerrarSesion() {
    session_unset();
    session_destroy();
}

// =============================================================================
// FIN DE CONFIGURACIÓN
// =============================================================================
?>
