<?php

function potitos(array $lineas): string
{
    $gustan = [];
    $nogustan = [];

    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '') {
            continue;
        }
        if (strpos($linea, 'SI:') === 0) {
            $resto = trim(substr($linea, 3));
            $ingredientes = explode(' ', $resto);
            foreach ($ingredientes as $ing) {
                if ($ing === 'FIN' || $ing === '') {
                    continue;
                }
                $gustan[$ing] = true;
            }
        } elseif (strpos($linea, 'NO:') === 0) {
            $resto = trim(substr($linea, 3));
            $ingredientes = explode(' ', $resto);
            foreach ($ingredientes as $ing) {
                if ($ing === 'FIN' || $ing === '') {
                    continue;
                }
                $nogustan[$ing] = true;
            }
        }
    }

    $resultado = [];
    foreach ($nogustan as $ing => $_) {
        if (!isset($gustan[$ing])) {
            $resultado[] = $ing;
        }
    }

    sort($resultado, SORT_STRING);

    return implode(' ', $resultado);
}
