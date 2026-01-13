<?php

function entrenamientoDeLosMonjesDeHanoi(int $k, int $n): array
{
    // Número total de movimientos con n discos
    $total = (1 << $n) - 1;

    // La i-ésima vez que se mueve el disco k ocurre en el movimiento (2i-1)*2^{k-1}
    // Buscamos m1: i=k (k-ésima vez)
    $m1 = (1 << ($k - 1)) * (2 * $k - 1);
    if ($m1 > $total) {
        $m1 = -1;
    }

    // m2: primer movimiento en el que, tras moverlo, quedan <= k movimientos de ese disco.
    // En el entrenamiento se usa igual fórmula; el último movimiento del disco k es (2^{n-k}-1)*2^k + 2^{k-1}
    $ultimo = ((1 << ($n - $k)) - 1) * (1 << $k) + (1 << ($k - 1));

    $m2 = -1;
    if ($ultimo <= $total) {
        // necesitamos i tal que totalMovimientosDiscoK - i + 1 <= k
        $totalK = (1 << ($n - $k));
        $i2 = $totalK - $k + 1;
        if ($i2 >= 1 && $i2 <= $totalK) {
            $m2cand = (1 << ($k - 1)) * (2 * $i2 - 1);
            if ($m2cand <= $total) {
                $m2 = $m2cand;
            }
        }
    }

    return [$k, $n, $m1, $m2];
}
