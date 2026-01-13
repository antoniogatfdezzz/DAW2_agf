<?php

function cartelesEnPapelContinuo(array $lineas): array
{
    // lineas incluye marco completo; extraemos interior, rotamos, y volvemos a enmarcar
    $h = count($lineas) - 2;
    $w = strlen($lineas[0]) - 2;
    $mat = [];
    for ($i = 0; $i < $h; $i++) {
        $fila = substr($lineas[$i + 1], 1, $w);
        $mat[$i] = str_split($fila);
    }
    // rotar 90º antihorario: nuevo tamaño w x h
    $rot = [];
    for ($x = 0; $x < $w; $x++) {
        $fila = '';
        for ($y = 0; $y < $h; $y++) {
            $fila .= $mat[$y][$w - 1 - $x];
        }
        $rot[] = $fila;
    }
    $nh = count($rot);
    $nw = strlen($rot[0]);
    $out = [];
    $out[] = str_repeat('-', $nw + 2);
    foreach ($rot as $f) {
        $out[] = '|' . $f . '|';
    }
    $out[] = str_repeat('-', $nw + 2);
    return $out;
}
