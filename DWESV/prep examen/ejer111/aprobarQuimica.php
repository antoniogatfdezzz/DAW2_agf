<?php

function aprobarQuimica(int $z): string
{
    // Definir el orden de los subniveles según el diagrama de Moeller
    $subniveles = [
        ['1s', 2],
        ['2s', 2],
        ['2p', 6],
        ['3s', 2],
        ['3p', 6],
        ['4s', 2],
        ['3d', 10],
        ['4p', 6],
        ['5s', 2],
        ['4d', 10],
        ['5p', 6],
        ['6s', 2],
        ['4f', 14],
        ['5d', 10],
        ['6p', 6],
        ['7s', 2],
        ['5f', 14],
        ['6d', 10],
        ['7p', 6],
    ];

    $restantes = $z;
    $resultado = [];

    foreach ($subniveles as [$nombre, $capacidad]) {
        if ($restantes <= 0) {
            break;
        }
        $poner = min($capacidad, $restantes);
        $resultado[] = $nombre . $poner;
        $restantes -= $poner;
    }

    // Caso especial Z = 0
    if ($z === 0) {
        return '1s0';
    }

    return implode(' ', $resultado);
}
