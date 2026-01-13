<?php

function aproximacionDeGauss(int $n, int $m): string
{
    if ($n < 2) {
        $piN = 0;
    } else {
        // Criba de Eratóstenes hasta n
        $esPrimo = array_fill(0, $n + 1, true);
        $esPrimo[0] = false;
        $esPrimo[1] = false;
        $limite = (int)floor(sqrt($n));
        for ($p = 2; $p <= $limite; $p++) {
            if ($esPrimo[$p]) {
                for ($mult = $p * $p; $mult <= $n; $mult += $p) {
                    $esPrimo[$mult] = false;
                }
            }
        }
        $piN = 0;
        for ($i = 2; $i <= $n; $i++) {
            if ($esPrimo[$i]) {
                $piN++;
            }
        }
    }

    $lnN = log($n);
    if ($lnN == 0.0) {
        // Para n = 1 el logaritmo es 0; tratamos como error máximo.
        return "Mayor";
    }

    $error = ($piN / $n) - (1.0 / $lnN);
    $errorAbs = abs($error);

    $maximoError = 1.0 / pow(10.0, $m);

    if (abs($errorAbs - $maximoError) < 1e-12) {
        return "Igual";
    }

    if ($errorAbs > $maximoError) {
        return "Mayor";
    }

    return "Menor";
}
