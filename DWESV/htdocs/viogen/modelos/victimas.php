<?php
require_once __DIR__ . '/../configuración/config.php';

function victimas_all(): array {
    return [
        [
            'id' => 1,
            'victima_nombre' => 'Ana',
            'victima_apellidos' => 'García López',
            'victima_tipo_documento' => 'DNI',
            'victima_num_documento' => '12345678A',
            'victima_fecha_nacimiento' => '1990-05-15',
            'victima_edad' => calcularEdad('1990-05-15'),
            'victima_nacionalidad' => 'España',
            'victima_sexo' => 'Femenino',
            'victima_domicilio' => 'C/ Mayor 1, Madrid',
            'victima_lugares_frecuentes' => 'Trabajo: Calle Serrano, Gimnasio: Centro X',
            'victima_telefonos' => '600123123, 915551234',
            'preferencia_contacto' => 'Horario laboral',
            'victima_idioma' => 'Español',
            'necesita_interprete' => 'no',
            'victima_situacion_economica' => 'empleada',
            'victima_red_apoyo' => 'familia',
            'victima_vivienda_compartida_con_agresor' => 'no'
        ],
        [
            'id' => 2,
            'victima_nombre' => 'María',
            'victima_apellidos' => 'Rodríguez Martín',
            'victima_tipo_documento' => 'NIE',
            'victima_num_documento' => 'X9876543B',
            'victima_fecha_nacimiento' => '1985-03-20',
            'victima_edad' => calcularEdad('1985-03-20'),
            'victima_nacionalidad' => 'Argentina',
            'victima_sexo' => 'Femenino',
            'victima_domicilio' => 'Av. Libertad 45, Barcelona',
            'victima_lugares_frecuentes' => 'Oficina: Paseo de Gracia, Casa de hermana: C/ Valencia',
            'victima_telefonos' => '632456789',
            'preferencia_contacto' => 'Cualquier hora',
            'victima_idioma' => 'Español',
            'necesita_interprete' => 'no',
            'victima_situacion_economica' => 'desempleada',
            'victima_red_apoyo' => 'amigos',
            'victima_vivienda_compartida_con_agresor' => 'sí'
        ],
        [
            'id' => 3,
            'victima_nombre' => 'Laura',
            'victima_apellidos' => 'Fernández Sánchez',
            'victima_tipo_documento' => 'DNI',
            'victima_num_documento' => '45678912C',
            'victima_fecha_nacimiento' => '1992-11-08',
            'victima_edad' => calcularEdad('1992-11-08'),
            'victima_nacionalidad' => 'España',
            'victima_sexo' => 'Femenino',
            'victima_domicilio' => 'C/ Sol 23, Valencia',
            'victima_lugares_frecuentes' => 'Centro comercial Nuevo Centro, Parque del Oeste',
            'victima_telefonos' => '678345612',
            'preferencia_contacto' => 'Solo por la mañana',
            'victima_idioma' => 'Español',
            'necesita_interprete' => 'no',
            'victima_estado_reproductivo' => 'embarazada',
            'victima_situacion_economica' => 'empleada',
            'victima_red_apoyo' => 'ninguna',
            'victima_vivienda_compartida_con_agresor' => 'no'
        ]
    ];
}

function victimas_find_by_name(string $term): array {
    $term = mb_strtolower($term);
    return array_values(array_filter(victimas_all(), function($v) use ($term){
        $nombre = mb_strtolower(($v['victima_nombre'] ?? '') . ' ' . ($v['victima_apellidos'] ?? ''));
        $docnum = mb_strtolower(($v['victima_num_documento'] ?? ''));
        $doctype = mb_strtolower(($v['victima_tipo_documento'] ?? ''));
        return $term === ''
            || str_contains($nombre, $term)
            || str_contains($docnum, $term)
            || str_contains($doctype, $term);
    }));
}
