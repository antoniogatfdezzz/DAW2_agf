<?php

function faseFinal(int $N, array $equipos, array $marcadores): string
{
    $actual = $equipos;
    $idx = 0;
    $n = $N;
    while ($n > 1) {
        $siguiente = [];
        for ($i = 0; $i < $n; $i += 2) {
            $g1 = $marcadores[$idx++];
            $g2 = $marcadores[$idx++];
            if ($g1 > $g2) {
                $siguiente[] = $actual[$i];
            } else {
                $siguiente[] = $actual[$i + 1];
            }
        }
        $actual = $siguiente;
        $n = count($actual);
    }
    return $actual[0];
}
