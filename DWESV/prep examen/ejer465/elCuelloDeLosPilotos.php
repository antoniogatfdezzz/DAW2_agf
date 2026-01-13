<?php

function elCuelloDeLosPilotos(array $mapa): string
{
    // $mapa: array de strings, contiene '#', '.', 'O'. Recorrer trazado clockwise.
    $h = count($mapa);
    $w = strlen($mapa[0]);

    // Buscar posición de O
    $sx = $sy = -1;
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if ($mapa[$y][$x] === 'O') {
                $sx = $x;
                $sy = $y;
                break 2;
            }
        }
    }

    // Encontrar dirección inicial (clockwise, primer vecino '#')
    $dirs = [[1,0],[0,1],[-1,0],[0,-1]]; // derecha, abajo, izquierda, arriba (sentido horario)
    $dir = 0;
    for ($k = 0; $k < 4; $k++) {
        $nx = $sx + $dirs[$k][0];
        $ny = $sy + $dirs[$k][1];
        if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h && $mapa[$ny][$nx] === '#') {
            $dir = $k;
            break;
        }
    }

    $x = $sx;
    $y = $sy;
    $izq = 0;
    $der = 0;

    do {
        $prevDir = $dir;
        $x += $dirs[$dir][0];
        $y += $dirs[$dir][1];

        // Determinar próxima dirección manteniendo siempre la pared a la derecha (seguir circuito)
        // Probar girar derecha, seguir recto, girar izquierda, girar atrás.
        for ($delta = 1; $delta >= -2; $delta--) {
            $nd = ($dir + $delta + 4) % 4;
            $nx = $x + $dirs[$nd][0];
            $ny = $y + $dirs[$nd][1];
            if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h && $mapa[$ny][$nx] !== '.') {
                $dir = $nd;
                break;
            }
        }

        // Contar curva
        if ($dir !== $prevDir) {
            // Giro horario vs antihorario
            $diff = ($dir - $prevDir + 4) % 4;
            if ($diff === 1) {
                $der++;
            } elseif ($diff === 3) {
                $izq++;
            }
        }

    } while (!($x === $sx && $y === $sy && $dir === $dir));

    return $izq . ' ' . $der;
}
