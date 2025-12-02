<?php
require_once __DIR__ . '/../configuracion/config.php';

function agresores_all(): array {
    return [
        [
            'id' => 1,
            'agresor_nombre' => 'Carlos',
            'agresor_apellidos' => 'Pérez Ruiz',
            'agresor_tipo_documento' => 'DNI',
            'agresor_num_documento' => '87654321Z',
            'agresor_fecha_nacimiento' => '1988-10-10',
            'agresor_edad' => calcularEdad('1988-10-10'),
            'agresor_nacionalidad' => 'España',
            'agresor_domicilio' => 'C/ Norte 12, Madrid',
            'agresor_telefonos' => '611222333',
            'agresor_lugares_frecuentes' => 'Bar Los Amigos, Polígono Industrial Sur',
            'agresor_empleo' => 'desempleado',
            'agresor_relacion_con_victima' => 'expareja',
            'agresor_convivencia_actual' => 'no',
            'agresor_antecedentes_penales' => 'Denuncia previa por maltrato en 2020',
            'agresor_alcohol_drogas' => 'Consumo habitual de alcohol',
            'agresor_posesion_armas' => 'No se ha detectado',
            'agresor_intentos_suicidio' => 'no'
        ],
        [
            'id' => 2,
            'agresor_nombre' => 'Miguel',
            'agresor_apellidos' => 'Jiménez Ortiz',
            'agresor_tipo_documento' => 'DNI',
            'agresor_num_documento' => '23456789M',
            'agresor_fecha_nacimiento' => '1982-06-15',
            'agresor_edad' => calcularEdad('1982-06-15'),
            'agresor_nacionalidad' => 'España',
            'agresor_domicilio' => 'Av. Diagonal 89, Barcelona',
            'agresor_telefonos' => '644567890',
            'agresor_lugares_frecuentes' => 'Trabajo: Empresa X, Gimnasio Central',
            'agresor_empleo' => 'empleado',
            'agresor_relacion_con_victima' => 'cónyuge',
            'agresor_convivencia_actual' => 'sí',
            'agresor_antecedentes_penales' => 'Sin antecedentes',
            'agresor_quebrantamientos_previos' => 'Ninguno',
            'agresor_alcohol_drogas' => 'No consume',
            'agresor_salud_mental' => 'Antecedentes de ansiedad',
            'agresor_posesion_armas' => 'No',
            'agresor_intentos_suicidio' => 'no'
        ],
        [
            'id' => 3,
            'agresor_nombre' => 'Roberto',
            'agresor_apellidos' => 'Morales Castro',
            'agresor_tipo_documento' => 'DNI',
            'agresor_num_documento' => '98765432P',
            'agresor_fecha_nacimiento' => '1975-12-03',
            'agresor_edad' => calcularEdad('1975-12-03'),
            'agresor_nacionalidad' => 'España',
            'agresor_domicilio' => 'C/ Valencia 56, Valencia',
            'agresor_telefonos' => '655789123',
            'agresor_lugares_frecuentes' => 'Bares zona centro, Casa de su madre',
            'agresor_empleo' => 'autónomo',
            'agresor_relacion_con_victima' => 'expareja',
            'agresor_convivencia_actual' => 'no',
            'agresor_antecedentes_penales' => 'Múltiples denuncias por violencia de género',
            'agresor_quebrantamientos_previos' => 'Orden de alejamiento quebrantada en 2023',
            'agresor_violencia_otra_persona' => 'Sí, con anterior pareja',
            'agresor_alcohol_drogas' => 'Alcoholismo y consumo de cocaína',
            'agresor_salud_mental' => 'Trastorno de personalidad diagnosticado',
            'agresor_posesion_armas' => 'Posible posesión de arma blanca',
            'agresor_historia_agresiones_previas' => 'Historial de agresiones físicas y psicológicas',
            'agresor_intentos_suicidio' => 'sí',
            'agresor_observaciones' => 'Considerado de alto riesgo'
        ]
    ];
}

function agresores_find_by_name(string $term): array {
    $term = mb_strtolower($term);
    return array_values(array_filter(agresores_all(), function($a) use ($term){
        $nombre = mb_strtolower(($a['agresor_nombre'] ?? '') . ' ' . ($a['agresor_apellidos'] ?? ''));
        $docnum = mb_strtolower(($a['agresor_num_documento'] ?? ''));
        $doctype = mb_strtolower(($a['agresor_tipo_documento'] ?? ''));
        return $term === ''
            || str_contains($nombre, $term)
            || str_contains($docnum, $term)
            || str_contains($doctype, $term);
    }));
}
