<?php

function votacionesCapicua(int $N, int $Q): array
{
    $M = 0;
    for ($a = 0; $a <= $N; $a++) {
        $b = $N - $a;
        if ($a + $b < $Q) continue;
        $s = sprintf('%04d%04d', $a, $b);
        if ($s === strrev($s)) {
            $M++;
        }
    }
    return [$N, $Q, $M];
}
