<?php

function modificacionDeTablas(int $filas, int $columnas, array $mods): array
{
    // Creamos tabla inicial de ceros
    $tabla = array_fill(0, $filas, array_fill(0, $columnas, 0));

    foreach ($mods as $m) {
        if (count($m) < 4) continue;
        [$col, $a, $b, $val] = $m;
        if ($col < 0 || $col >= $columnas) continue;
        if ($a < 0) $a = 0;
        if ($b >= $filas) $b = $filas - 1;
        for ($i = $a; $i <= $b; $i++) {
            $tabla[$i][$col] += $val;
        }
    }

    return $tabla;
}
