<?php

function problemasDeHerencia(array $coeficientes, int $rectangulos): string
{
    $n = $rectangulos;
    if ($n <= 0) {
        return "JUSTO";
    }

    $areaCain = 0.0;
    $base = 1.0 / $n;

    for ($i = 0; $i < $n; $i++) {
        $x = $i * $base;

        // Evaluar polinomio con regla de Horner
        $y = 0.0;
        foreach ($coeficientes as $coef) {
            $y = $y * $x + $coef;
        }

        if ($y < 0.0) {
            $altura = 0.0;
        } elseif ($y > 1.0) {
            $altura = 1.0;
        } else {
            $altura = $y;
        }

        $areaCain += $base * $altura;
    }

    $areaAbel = 1.0 - $areaCain;
    $diferencia = $areaCain - $areaAbel; // positiva si gana Caín

    if (abs($diferencia) <= 0.001 + 1e-12) {
        return "JUSTO";
    }

    return ($diferencia > 0.0) ? "CAIN" : "ABEL";
}
