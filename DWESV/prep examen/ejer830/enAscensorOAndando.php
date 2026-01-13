<?php

function enAscensorOAndando(int $f0, int $f1, int $fa, int $ta, int $te): int
{
    if ($f0 === $f1) return 0;
    $dist = abs($f1 - $f0);
    $mejor = $dist * $ta; // todo andando

    // Solo ascensor: ir al ascensor, luego al destino
    $t1 = abs($f0 - $fa) * $ta + abs($f1 - $fa) * $te;
    if ($t1 < $mejor) $mejor = $t1;

    return $mejor;
}
