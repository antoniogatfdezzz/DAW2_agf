<?php
require_once __DIR__ . '/../configuracion/config.php';
require_once __DIR__ . '/victimas.php';

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        if ($needle === '') { return true; }
        return mb_strpos($haystack, $needle) !== false;
    }
}

function victimas_all(): array {
    global $victimas;

    $victimas = $victimas ?? ['A' => [], 'B' => [], 'C' => [], 'D' => []];

    $count = min(
        count($victimas['A'] ?? []),
        count($victimas['B'] ?? []),
        count($victimas['C'] ?? []),
        count($victimas['D'] ?? [])
    );
    $result = [];

    for ($i = 0; $i < $count; $i++) {
        $result[] = [
            'id' => $i + 1,
            'victima_nombre' => $victimas['A'][$i] ?? '',
            'victima_apellidos' => $victimas['B'][$i] ?? '',
            'victima_tipo_documento' => '',
            'victima_num_documento' => $victimas['C'][$i] ?? '',
            'victima_fecha_nacimiento' => '',
            'victima_edad' => null,
            'victima_nacionalidad' => '',
            'victima_sexo' => '',
            'victima_domicilio' => '',
            'victima_lugares_frecuentes' => '',
            'victima_telefonos' => $victimas['D'][$i] ?? '',
            'preferencia_contacto' => '',
            'victima_idioma' => '',
            'necesita_interprete' => '',
            'victima_estado_reproductivo' => '',
            'victima_situacion_economica' => '',
            'victima_red_apoyo' => '',
            'victima_vivienda_compartida_con_agresor' => ''
        ];
    }

    return $result;
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
