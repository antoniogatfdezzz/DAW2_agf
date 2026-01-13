<?php

function ventas(array $ventasSemana): string
{
    // Esperamos 6 valores: de martes a domingo
    if (count($ventasSemana) !== 6) {
        return "";
    }

    $dias = ["MARTES", "MIERCOLES", "JUEVES", "VIERNES", "SABADO", "DOMINGO"];

    $max = max($ventasSemana);
    $min = min($ventasSemana);

    $indicesMax = [];
    $indicesMin = [];

    foreach ($ventasSemana as $indice => $valor) {
        if ($valor === $max) {
            $indicesMax[] = $indice;
        }
        if ($valor === $min) {
            $indicesMin[] = $indice;
        }
    }

    $diaMax = (count($indicesMax) === 1) ? $dias[$indicesMax[0]] : "EMPATE";
    $diaMin = (count($indicesMin) === 1) ? $dias[$indicesMin[0]] : "EMPATE";

    $media = array_sum($ventasSemana) / 6.0;
    $domingo = $ventasSemana[5];

    $comparaDomingo = ($domingo > $media) ? "SI" : "NO";

    return $diaMax . " " . $diaMin . " " . $comparaDomingo;
}
