<?php
/**
 * MODELO: VALORACIONES VPR/VPER
 * Sistema de Valoración Policial del Riesgo (VPR) y 
 * Valoración Policial de Evolución del Riesgo (VPER)
 * 
 * Implementa el algoritmo actuarial con los 35 indicadores oficiales
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/victimas.php';
require_once __DIR__ . '/agresores.php';

// =============================================================================
// ALMACENAMIENTO EN MEMORIA (simulación de BD)
// =============================================================================
$VALORACIONES = [];

// =============================================================================
// FUNCIONES PRINCIPALES
// =============================================================================

/**
 * Crea una nueva valoración VPR (inicial)
 */
function crearValoracionVPR($datos) {
    global $VALORACIONES;
    
    // Validar datos obligatorios
    $campos_requeridos = [
        'id_victima',
        'id_agresor',
        'evaluador_id',
        'fuente_informacion',
        'tipo_hecho',
        'fecha_hecho',
        'descripcion_hechos'
    ];
    
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo])) {
            return ['error' => "El campo $campo es obligatorio"];
        }
    }
    
    // Generar ID
    $id = generarID('VPR');
    
    // Estructura completa de la valoración
    $valoracion = [
        'id' => $id,
        'tipo' => 'VPR',
        
        // === A. METADATOS (OBLIGATORIOS) ===
        'fecha_hora_valoracion' => date('Y-m-d H:i:s'),
        'evaluador_id' => $datos['evaluador_id'],
        'evaluador_nombre' => $datos['evaluador_nombre'] ?? '',
        'unidad_policial' => $datos['unidad_policial'] ?? '',
        'fuente_informacion' => $datos['fuente_informacion'], // Array
        'nivel_confianza_fuentes' => $datos['nivel_confianza_fuentes'] ?? 'media',
        'limitaciones_recogida_info' => $datos['limitaciones_recogida_info'] ?? '',
        
        // === B. DATOS DE LA VÍCTIMA ===
        'id_victima' => $datos['id_victima'],
        
        // === C. DATOS DEL AGRESOR ===
        'id_agresor' => $datos['id_agresor'],
        
        // === D. RELACIÓN Y CONTEXTO ===
        'fecha_inicio_relacion' => $datos['fecha_inicio_relacion'] ?? null,
        'duracion_relacion' => $datos['duracion_relacion'] ?? '',
        'fecha_ruptura' => $datos['fecha_ruptura'] ?? null,
        'motivo_ruptura' => $datos['motivo_ruptura'] ?? '',
        'reanudacion_convivencia' => $datos['reanudacion_convivencia'] ?? false,
        'problemas_judiciales_separacion' => $datos['problemas_judiciales_separacion'] ?? false,
        'custodia_hijos_disputa' => $datos['custodia_hijos_disputa'] ?? false,
        
        // === E. DATOS DEL HECHO (OBLIGATORIOS) ===
        'tipo_hecho' => $datos['tipo_hecho'],
        'fecha_hecho' => $datos['fecha_hecho'],
        'hora_aproximada' => $datos['hora_aproximada'] ?? '',
        'lugar_hecho' => $datos['lugar_hecho'] ?? '',
        'descripcion_hechos' => $datos['descripcion_hechos'],
        'lesiones_presentes' => $datos['lesiones_presentes'] ?? false,
        'parte_medico' => $datos['parte_medico'] ?? '',
        'urgente_intervencion' => $datos['urgente_intervencion'] ?? false,
        'denuncia_previa_mismo_hecho' => $datos['denuncia_previa_mismo_hecho'] ?? false,
        'testigos' => $datos['testigos'] ?? [],
        'evidencias_fotograficas' => $datos['evidencias_fotograficas'] ?? [],
        'pruebas_tecnologicas' => $datos['pruebas_tecnologicas'] ?? [],
        'observaciones_hecho' => $datos['observaciones_hecho'] ?? '',
        
        // === F. HISTORIA DE VIOLENCIA: 35 INDICADORES VPR ===
        
        // Factor 1: Historia de violencia (I-1 a I-6)
        'I1_violencia_psicologica' => $datos['I1_violencia_psicologica'] ?? false,
        'I1_detalles' => $datos['I1_detalles'] ?? '',
        
        'I2_violencia_fisica' => $datos['I2_violencia_fisica'] ?? false,
        'I2_detalles' => $datos['I2_detalles'] ?? '',
        
        'I3_sexo_forzado' => $datos['I3_sexo_forzado'] ?? false,
        'I3_detalles' => $datos['I3_detalles'] ?? '',
        
        'I4_uso_armas_objetos' => $datos['I4_uso_armas_objetos'] ?? false,
        'I4_detalles' => $datos['I4_detalles'] ?? '',
        
        'I5_amenazas_planes' => $datos['I5_amenazas_planes'] ?? false,
        'I5_detalles' => $datos['I5_detalles'] ?? '',
        
        'I6_escalada_6m' => $datos['I6_escalada_6m'] ?? false,
        'I6_detalles' => $datos['I6_detalles'] ?? '',
        
        // Factor 2: Características del agresor (I-7 a I-23)
        'I7_celos_exagerados' => $datos['I7_celos_exagerados'] ?? false,
        'I8_conductas_control' => $datos['I8_conductas_control'] ?? false,
        'I9_acoso' => $datos['I9_acoso'] ?? false,
        'I10_problemas_personales' => $datos['I10_problemas_personales'] ?? false,
        'I11_danos_materiales' => $datos['I11_danos_materiales'] ?? false,
        'I12_faltas_respeto_autoridad' => $datos['I12_faltas_respeto_autoridad'] ?? false,
        'I13_agresiones_terceros_animales' => $datos['I13_agresiones_terceros_animales'] ?? false,
        'I14_amenazas_terceros' => $datos['I14_amenazas_terceros'] ?? false,
        'I15_antecedentes_penales' => $datos['I15_antecedentes_penales'] ?? false,
        'I16_quebrantamientos_previos' => $datos['I16_quebrantamientos_previos'] ?? false,
        'I17_antecedentes_agresiones_fisicas_sexuales' => $datos['I17_antecedentes_agresiones_fisicas_sexuales'] ?? false,
        'I18_antecedentes_vg_otra_pareja' => $datos['I18_antecedentes_vg_otra_pareja'] ?? false,
        'I19_trastorno_mental_agresor' => $datos['I19_trastorno_mental_agresor'] ?? false,
        'I20_intentos_ideas_suicidio_agresor' => $datos['I20_intentos_ideas_suicidio_agresor'] ?? false,
        'I21_adicciones_agresor' => $datos['I21_adicciones_agresor'] ?? false,
        'I22_antecedentes_familiares_violencia' => $datos['I22_antecedentes_familiares_violencia'] ?? false,
        'I23_agresor_menor_24' => $datos['I23_agresor_menor_24'] ?? false,
        
        // Factor 3: Vulnerabilidades de la víctima (I-24 a I-28)
        'I24_discapacidad_victima' => $datos['I24_discapacidad_victima'] ?? false,
        'I25_intentos_ideas_suicidio_victima' => $datos['I25_intentos_ideas_suicidio_victima'] ?? false,
        'I26_adicciones_victima' => $datos['I26_adicciones_victima'] ?? false,
        'I27_carencia_apoyo_social' => $datos['I27_carencia_apoyo_social'] ?? false,
        'I28_victima_extranjera' => $datos['I28_victima_extranjera'] ?? false,
        
        // Factor 4: Circunstancias con menores (I-29 a I-31)
        'I29_victima_tiene_menores' => $datos['I29_victima_tiene_menores'] ?? false,
        'I29_edades_menores' => $datos['I29_edades_menores'] ?? [],
        'I30_violencia_amenaza_menores' => $datos['I30_violencia_amenaza_menores'] ?? false,
        'I31_victima_teme_por_integridad_menores' => $datos['I31_victima_teme_por_integridad_menores'] ?? false,
        
        // Factor 5: Circunstancias agravantes (I-32 a I-35)
        'I32_victima_ha_denunciado_otros_agresores' => $datos['I32_victima_ha_denunciado_otros_agresores'] ?? false,
        'I33_episodios_violencia_lateral' => $datos['I33_episodios_violencia_lateral'] ?? false,
        'I34_ruptura_reciente' => $datos['I34_ruptura_reciente'] ?? false,
        'I35_victima_piensa_agresor_capaz_matar' => $datos['I35_victima_piensa_agresor_capaz_matar'] ?? false,
        
        // Percepción de riesgo (I-36, I-37)
        'I36_victima_considera_riesgo' => $datos['I36_victima_considera_riesgo'] ?? 'bajo',
        'I37_evaluador_acuerda_con_victima' => $datos['I37_evaluador_acuerda_con_victima'] ?? false,
        
        // Escala-H (letalidad)
        'H_estrangulamiento' => $datos['H_estrangulamiento'] ?? false,
        'H_intento_homicidio_previo' => $datos['H_intento_homicidio_previo'] ?? false,
        'H_amenaza_muerte_explicita' => $datos['H_amenaza_muerte_explicita'] ?? false,
        
        // === H. EVIDENCIAS ===
        'evidencias_adjuntas' => $datos['evidencias_adjuntas'] ?? [],
        
        // === I. RESULTADO (calculado automáticamente) ===
        'puntuacion_total' => 0,
        'nivel_automatico' => NIVEL_NO_APRECIADO,
        'nivel_final' => NIVEL_NO_APRECIADO,
        'ajuste_manual' => false,
        'razon_ajuste' => '',
        'medidas_activadas' => $datos['medidas_activadas'] ?? [],
        'plan_seguridad_personal' => $datos['plan_seguridad_personal'] ?? '',
        'fecha_proxima_vper' => null,
        
        // === METADATA ===
        'activo' => true,
        'fecha_creacion' => date('Y-m-d H:i:s'),
        'ultima_modificacion' => date('Y-m-d H:i:s')
    ];
    
    // CALCULAR PUNTUACIÓN Y NIVEL DE RIESGO
    $resultado = calcularRiesgo($valoracion);
    $valoracion['puntuacion_total'] = $resultado['puntuacion'];
    $valoracion['nivel_automatico'] = $resultado['nivel'];
    $valoracion['nivel_final'] = $resultado['nivel'];
    $valoracion['fecha_proxima_vper'] = calcularProximaValoracion($resultado['nivel']);
    
    $VALORACIONES[$id] = $valoracion;
    
    // Asociar con víctima y agresor
    agregarValoracionVictima($datos['id_victima'], $id);
    agregarValoracionAgresor($datos['id_agresor'], $id);
    
    // Registrar auditoría
    registrarAuditoria(
        $datos['evaluador_id'],
        'VPR_CREADA',
        "Nueva valoración VPR: $id - Nivel: " . obtenerNombreNivel($resultado['nivel'])
    );
    
    return $valoracion;
}

/**
 * ALGORITMO DE CÁLCULO DE RIESGO
 * Implementa el sistema de puntuación basado en los 35 indicadores
 */
function calcularRiesgo($valoracion) {
    global $PESOS_INDICADORES;
    
    $puntuacion = 0;
    $detalles = [];
    
    // Recorrer todos los indicadores y sumar puntos
    foreach ($PESOS_INDICADORES as $indicador => $peso) {
        if (isset($valoracion[$indicador]) && $valoracion[$indicador] === true) {
            $puntuacion += $peso;
            $detalles[] = [
                'indicador' => $indicador,
                'peso' => $peso,
                'presente' => true
            ];
        }
    }
    
    // Casos especiales: percepción de riesgo de la víctima
    if (isset($valoracion['I36_victima_considera_riesgo'])) {
        if ($valoracion['I36_victima_considera_riesgo'] === 'alto') {
            $puntuacion += 6;
            $detalles[] = ['indicador' => 'I36_victima_considera_riesgo_alto', 'peso' => 6, 'presente' => true];
        }
    }
    
    // Determinar nivel según umbrales
    $nivel = calcularNivelRiesgo($puntuacion);
    
    return [
        'puntuacion' => $puntuacion,
        'nivel' => $nivel,
        'detalles' => $detalles
    ];
}

/**
 * Crea una valoración VPER (evolución/seguimiento)
 */
function crearValoracionVPER($datos) {
    global $VALORACIONES;
    
    // Similar a VPR pero incluye indicadores adicionales de evolución
    $id = generarID('VPER');
    
    // Heredar estructura de VPR
    $valoracion = crearValoracionVPR($datos);
    
    if (isset($valoracion['error'])) {
        return $valoracion;
    }
    
    // Cambiar tipo
    $valoracion['tipo'] = 'VPER';
    $valoracion['id'] = $id;
    $valoracion['vpr_origen'] = $datos['vpr_origen'] ?? null;
    
    // Indicadores adicionales VPER
    $valoracion['VPER_I12_agresor_fugado_paradero_desconocido'] = $datos['VPER_I12_agresor_fugado_paradero_desconocido'] ?? false;
    $valoracion['VPER_I14_quebrantamientos_de_medidas'] = $datos['VPER_I14_quebrantamientos_de_medidas'] ?? false;
    $valoracion['VPER_I18_tramites_judiciales_separacion_no_deseados'] = $datos['VPER_I18_tramites_judiciales_separacion_no_deseados'] ?? false;
    $valoracion['VPER_I27_reanuda_convivencia'] = $datos['VPER_I27_reanuda_convivencia'] ?? false;
    $valoracion['VPER_I28_no_desea_declarar_retira_denuncia'] = $datos['VPER_I28_no_desea_declarar_retira_denuncia'] ?? false;
    
    // Indicadores positivos (reducen riesgo)
    $valoracion['VPER_I19_distanciamiento_agresor'] = $datos['VPER_I19_distanciamiento_agresor'] ?? false;
    $valoracion['VPER_I20_actitud_pacifica'] = $datos['VPER_I20_actitud_pacifica'] ?? false;
    $valoracion['VPER_I21_colaboracion_agresor'] = $datos['VPER_I21_colaboracion_agresor'] ?? false;
    $valoracion['VPER_I22_arrepentimiento'] = $datos['VPER_I22_arrepentimiento'] ?? false;
    $valoracion['VPER_I23_adhesion_programas'] = $datos['VPER_I23_adhesion_programas'] ?? false;
    $valoracion['VPER_I24_cumplimiento_regimen_separacion'] = $datos['VPER_I24_cumplimiento_regimen_separacion'] ?? false;
    $valoracion['VPER_I25_estabilidad_laboral_agresor'] = $datos['VPER_I25_estabilidad_laboral_agresor'] ?? false;
    $valoracion['VPER_I26_apoyo_social_agresor'] = $datos['VPER_I26_apoyo_social_agresor'] ?? false;
    
    // Recalcular con indicadores VPER
    $resultado = calcularRiesgo($valoracion);
    $valoracion['puntuacion_total'] = $resultado['puntuacion'];
    $valoracion['nivel_automatico'] = $resultado['nivel'];
    $valoracion['nivel_final'] = $resultado['nivel'];
    $valoracion['fecha_proxima_vper'] = calcularProximaValoracion($resultado['nivel']);
    
    $VALORACIONES[$id] = $valoracion;
    
    // Asociar con víctima y agresor
    agregarValoracionVictima($datos['id_victima'], $id);
    agregarValoracionAgresor($datos['id_agresor'], $id);
    
    // Registrar auditoría
    registrarAuditoria(
        $datos['evaluador_id'],
        'VPER_CREADA',
        "Nueva valoración VPER: $id - Nivel: " . obtenerNombreNivel($resultado['nivel'])
    );
    
    return $valoracion;
}

/**
 * Ajusta manualmente el nivel de riesgo (actuarial ajustado)
 */
function ajustarNivelManual($id_valoracion, $nuevo_nivel, $razon, $evaluador) {
    global $VALORACIONES;
    
    if (!isset($VALORACIONES[$id_valoracion])) {
        return ['error' => 'Valoración no encontrada'];
    }
    
    $nivel_anterior = $VALORACIONES[$id_valoracion]['nivel_final'];
    
    $VALORACIONES[$id_valoracion]['nivel_final'] = $nuevo_nivel;
    $VALORACIONES[$id_valoracion]['ajuste_manual'] = true;
    $VALORACIONES[$id_valoracion]['razon_ajuste'] = $razon;
    $VALORACIONES[$id_valoracion]['ajustado_por'] = $evaluador;
    $VALORACIONES[$id_valoracion]['fecha_ajuste'] = date('Y-m-d H:i:s');
    $VALORACIONES[$id_valoracion]['ultima_modificacion'] = date('Y-m-d H:i:s');
    
    // Recalcular próxima valoración según nuevo nivel
    $VALORACIONES[$id_valoracion]['fecha_proxima_vper'] = calcularProximaValoracion($nuevo_nivel);
    
    // Registrar auditoría
    registrarAuditoria(
        $evaluador,
        'NIVEL_AJUSTADO',
        "Valoración $id_valoracion ajustada de " . obtenerNombreNivel($nivel_anterior) . 
        " a " . obtenerNombreNivel($nuevo_nivel) . ". Razón: $razon"
    );
    
    return $VALORACIONES[$id_valoracion];
}

/**
 * Obtiene una valoración por ID
 */
function buscarValoracionPorId($id) {
    global $VALORACIONES;
    return $VALORACIONES[$id] ?? null;
}

/**
 * Obtiene todas las valoraciones
 */
function obtenerTodasValoraciones() {
    global $VALORACIONES;
    return $VALORACIONES;
}

/**
 * Obtiene valoraciones de una víctima
 */
function obtenerValoracionesPorVictima($id_victima) {
    global $VALORACIONES;
    
    return array_filter($VALORACIONES, function($v) use ($id_victima) {
        return $v['id_victima'] === $id_victima;
    });
}

/**
 * Obtiene valoraciones por nivel de riesgo
 */
function obtenerValoracionesPorNivel($nivel) {
    global $VALORACIONES;
    
    return array_filter($VALORACIONES, function($v) use ($nivel) {
        return $v['nivel_final'] === $nivel && $v['activo'];
    });
}

/**
 * Obtiene valoraciones pendientes de revisión (próxima VPER vencida)
 */
function obtenerValoracionesPendientesRevision() {
    global $VALORACIONES;
    
    $ahora = date('Y-m-d H:i:s');
    
    return array_filter($VALORACIONES, function($v) use ($ahora) {
        return $v['activo'] && 
               !empty($v['fecha_proxima_vper']) && 
               $v['fecha_proxima_vper'] <= $ahora;
    });
}

/**
 * Genera resumen estadístico de una valoración
 */
function generarResumenValoracion($id) {
    $valoracion = buscarValoracionPorId($id);
    
    if (!$valoracion) {
        return ['error' => 'Valoración no encontrada'];
    }
    
    $victima = buscarVictimaPorId($valoracion['id_victima']);
    $agresor = buscarAgresorPorId($valoracion['id_agresor']);
    
    return [
        'id' => $valoracion['id'],
        'tipo' => $valoracion['tipo'],
        'fecha' => $valoracion['fecha_hora_valoracion'],
        'victima' => $victima ? $victima['nombre'] . ' ' . $victima['apellidos'] : 'Desconocida',
        'agresor' => $agresor ? $agresor['nombre'] . ' ' . $agresor['apellidos'] : 'Desconocido',
        'puntuacion' => $valoracion['puntuacion_total'],
        'nivel_automatico' => obtenerNombreNivel($valoracion['nivel_automatico']),
        'nivel_final' => obtenerNombreNivel($valoracion['nivel_final']),
        'ajuste_manual' => $valoracion['ajuste_manual'],
        'proxima_vper' => $valoracion['fecha_proxima_vper'],
        'medidas_activas' => $valoracion['medidas_activadas']
    ];
}

// =============================================================================
// EXPORTAR/IMPORTAR (para persistencia)
// =============================================================================

function guardarValoraciones() {
    global $VALORACIONES;
    $archivo = __DIR__ . '/data_valoraciones.json';
    return file_put_contents($archivo, json_encode($VALORACIONES, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function cargarValoraciones() {
    global $VALORACIONES;
    $archivo = __DIR__ . '/data_valoraciones.json';
    if (file_exists($archivo)) {
        $data = file_get_contents($archivo);
        $VALORACIONES = json_decode($data, true);
    }
}

cargarValoraciones();

?>
