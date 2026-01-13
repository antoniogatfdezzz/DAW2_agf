<?php

function igualandoCopas(array $copas): int
{
    $n = count($copas);
    if ($n === 0) return 0;

    $max = max($copas);
    $total = 0;
    foreach ($copas as $c) {
        if ($c < $max) {
            $total += $max - $c;
        }
    }
    return $total;
}
