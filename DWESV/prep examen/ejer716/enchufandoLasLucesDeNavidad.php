<?php

function enchufandoLasLucesDeNavidad(array $regletas): int
{
    $n = count($regletas);
    if ($n === 0) return 0;
    rsort($regletas);
    $tomas = $regletas[0];
    for ($i = 1; $i < $n; $i++) {
        $tomas += $regletas[$i] - 1;
    }
    return $tomas;
}
