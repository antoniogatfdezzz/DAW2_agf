<?php

function apuestaConRecetas(int $pilar, int $marco, int $pedro): string
{
    $a = $pilar;
    $b = $marco;
    $c = $pedro;

    // Si las tres apuestas son iguales o ya es imposible que Pedro pierda
    if (($a === $b && $b === $c) || ($c !== $a && $c !== $b && distancia($c, $a, $b) === 'seguro')) {
        return '0';
    }

    // Buscamos el número entero de recetas r que garantice que Pedro no es el más lejano
    // y lo más cercano posible a la media, sin perder nunca.
    // Como límites razonables, buscamos en [0, max(a,b,c)*2].

    $max = max($a, $b, $c) * 2 + 10;
    $mejorR = null;

    for ($r = 0; $r <= $max; $r++) {
        if (!pedroNoPierdeNunca($a, $b, $c, $r)) {
            continue;
        }
        if ($mejorR === null || abs($r - $c) < abs($mejorR - $c)) {
            $mejorR = $r;
        }
    }

    if ($mejorR === null) {
        return 'I';
    }

    if ($mejorR === $c) {
        // Si coincide con su apuesta original, no pide nada extra
        return '0';
    }

    return (string)$mejorR;
}

function pedroNoPierdeNunca(int $a, int $b, int $c, int $r): bool
{
    // Modelo simplificado: Pedro quiere que su distancia nunca sea estrictamente mayor
    // que la de ambos rivales para ninguna posible media real.
    // Aquí comprobamos contra la media que producen exactamente esas recetas.
    $media = ($a + $b + $r) / 3.0;

    $da = abs($a - $media);
    $db = abs($b - $media);
    $dc = abs($r - $media);

    // Pedro pierde si su distancia es estrictamente mayor que la de los otros dos
    if ($dc > $da && $dc > $db) {
        return false;
    }

    // También consideramos que si empata con alguien en peor distancia, pierde
    if (($dc === $da && $dc >= $db) || ($dc === $db && $dc >= $da)) {
        return false;
    }

    return true;
}

function distancia(int $c, int $a, int $b): string
{
    $mediaActual = ($a + $b + $c) / 3.0;
    $da = abs($a - $mediaActual);
    $db = abs($b - $mediaActual);
    $dc = abs($c - $mediaActual);

    if ($dc > $da && $dc > $db) {
        return 'pierde';
    }
    if (($dc === $da && $dc >= $db) || ($dc === $db && $dc >= $da)) {
        return 'pierde';
    }
    return 'seguro';
}
