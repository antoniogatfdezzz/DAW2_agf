<?php

function avituallamientoEnLasEtapasCiclistas(array $alturas)
{
    // $alturas: lista de alturas de PK 0..n (sin el -1 final)
    $n = count($alturas);
    if ($n < 2) {
        return 'HOY NO COMEN';
    }

    $mejorInicio = -1;
    $mejorLong = 0;

    $i = 0;
    while ($i < $n - 1) {
        if ($alturas[$i] === $alturas[$i + 1]) {
            $inicio = $i;
            $long = 1; // ya hay un km llano entre i e i+1
            $j = $i + 1;
            while ($j < $n - 1 && $alturas[$j] === $alturas[$j + 1]) {
                $long++;
                $j++;
            }
            if ($long > $mejorLong) {
                $mejorLong = $long;
                $mejorInicio = $inicio;
            }
            $i = $j + 1;
        } else {
            $i++;
        }
    }

    if ($mejorLong === 0) {
        return 'HOY NO COMEN';
    }

    return $mejorInicio . ' ' . $mejorLong;
}
