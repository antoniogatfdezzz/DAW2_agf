<?php

function elMejorDatoDelParo(array $tasas): array
{
    $n = count($tasas);
    $res = array_fill(0, $n, 0);

    $stack = []; // índices de meses con tasas crecientes
    for ($i = 0; $i < $n; $i++) {
        while (!empty($stack) && $tasas[end($stack)] <= $tasas[$i]) {
            array_pop($stack);
        }
        if (empty($stack)) {
            $res[$i] = $i; // todos los anteriores eran peores
        } else {
            $res[$i] = $i - end($stack) - 1;
        }
        $stack[] = $i;
    }

    return $res;
}
