<?php

function internetEnElMetro(int $longitudTunel, array $antenas): string
{
    // $antenas: array de [pos, radio]
    $actual = 0.0;

    foreach ($antenas as $a) {
        [$pos, $rad] = $a;
        $ini = max(0.0, $pos - $rad);
        $fin = min((float)$longitudTunel, $pos + $rad);

        if ($ini > $actual + 1e-9) {
            return 'NO';
        }
        if ($fin > $actual) {
            $actual = $fin;
        }
        if ($actual >= $longitudTunel - 1e-9) {
            return 'SI';
        }
    }

    return ($actual >= $longitudTunel - 1e-9) ? 'SI' : 'NO';
}
