<?php

function estrofas(array $versos): string
{
    $n = count($versos);
    if ($n === 0) {
        return 'DESCONOCIDO';
    }

    $rimasCon = [];
    $rimasAso = [];

    foreach ($versos as $verso) {
        $ultimaPalabra = extraerUltimaPalabra($verso);
        $rimasCon[] = obtenerTerminacionConsonante($ultimaPalabra);
        $rimasAso[] = obtenerTerminacionAsonante($ultimaPalabra);
    }

    // Funciones internas para comparar patrones
    $patCon = $rimasCon;
    $patAso = $rimasAso;

    if ($n === 2) {
        if ($patCon[0] !== '' && $patCon[0] === $patCon[1]) {
            return 'PAREADO';
        }
        return 'DESCONOCIDO';
    }

    if ($n === 3) {
        if ($patCon[0] !== '' && $patCon[2] !== '' && $patCon[0] === $patCon[2] && $patCon[1] !== $patCon[0]) {
            return 'TERCETO';
        }
        return 'DESCONOCIDO';
    }

    if ($n === 4) {
        $a = $patCon[0];
        $b = $patCon[1];
        $c = $patCon[2];
        $d = $patCon[3];

        $aa = $patAso[0];
        $ab = $patAso[1];
        $ac = $patAso[2];
        $ad = $patAso[3];

        // Cuaderna via: AAAA consonante
        if ($a !== '' && $a === $b && $a === $c && $a === $d) {
            return 'CUADERNA VIA';
        }

        // Cuarteto: ABBA consonante
        if ($a !== '' && $b !== '' && $a === $d && $b === $c && $a !== $b) {
            return 'CUARTETO';
        }

        // Cuarteta: ABAB consonante
        if ($a !== '' && $b !== '' && $a === $c && $b === $d && $a !== $b) {
            return 'CUARTETA';
        }

        // Seguidilla: -a-a asonante, sólo rima en los pares y debe ser asonante
        $rima2 = $ab;
        $rima4 = $ad;
        $con2 = $b;
        $con4 = $d;

        $esSeg = false;
        if ($rima2 !== '' && $rima2 === $rima4) {
            // Los impares no deben rimar asonantemente con los pares
            if ($aa === '' || $aa !== $rima2) {
                if ($ac === '' || $ac !== $rima2) {
                    // Y no debe haber rima consonante entre 2 y 4 ni con 1 o 3
                    if (!($con2 !== '' && $con2 === $con4) &&
                        !($con2 !== '' && $con2 === $a) &&
                        !($con2 !== '' && $con2 === $c) &&
                        !($con4 !== '' && $con4 === $a) &&
                        !($con4 !== '' && $con4 === $c)) {
                        $esSeg = true;
                    }
                }
            }
        }

        if ($esSeg) {
            return 'SEGUIDILLA';
        }

        return 'DESCONOCIDO';
    }

    return 'DESCONOCIDO';
}

function extraerUltimaPalabra(string $verso): string
{
    $verso = trim($verso);
    if ($verso === '') {
        return '';
    }
    $trozos = preg_split('/\s+/', $verso);
    $palabra = strtolower(end($trozos));
    // dejamos solo letras
    $palabra = preg_replace('/[^a-z]/', '', $palabra);
    return $palabra ?? '';
}

function obtenerTerminacionConsonante(string $palabra): string
{
    if ($palabra === '') {
        return '';
    }
    $vocales = ['a', 'e', 'i', 'o', 'u'];
    $len = strlen($palabra);
    // Última vocal tónica (simplificado: última vocal de la palabra)
    $pos = -1;
    for ($i = $len - 1; $i >= 0; $i--) {
        if (in_array($palabra[$i], $vocales, true)) {
            $pos = $i;
            break;
        }
    }
    if ($pos === -1) {
        return $palabra;
    }
    return substr($palabra, $pos);
}

function obtenerTerminacionAsonante(string $palabra): string
{
    $termCon = obtenerTerminacionConsonante($palabra);
    if ($termCon === '') {
        return '';
    }
    $vocales = ['a', 'e', 'i', 'o', 'u'];
    $res = '';
    $len = strlen($termCon);
    for ($i = 0; $i < $len; $i++) {
        if (in_array($termCon[$i], $vocales, true)) {
            $res .= $termCon[$i];
        }
    }
    return $res;
}
