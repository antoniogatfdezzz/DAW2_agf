<?php

function carreraPopular(array $participantes): string
{
    // $participantes: array de líneas "Apellido, Nombre" ordenadas por apellido, termina sin ====.
    $total = count($participantes);
    if ($total === 0) {
        return '0 0';
    }

    $hermanos = 0;
    $i = 0;
    while ($i < $total) {
        $linea = $participantes[$i];
        $partes = explode(',', $linea, 2);
        $apellido = strtolower(trim($partes[0]));
        $j = $i + 1;
        $cuenta = 1;
        while ($j < $total) {
            $partes2 = explode(',', $participantes[$j], 2);
            $apellido2 = strtolower(trim($partes2[0]));
            if ($apellido2 === $apellido) {
                $cuenta++;
                $j++;
            } else {
                break;
            }
        }
        if ($cuenta > 1) {
            $hermanos += $cuenta;
        }
        $i = $j;
    }

    return $total . ' ' . $hermanos;
}
