<?php

function distanciaAlSiguienteCapicua(int $n): int
{
    $x = $n + 1;
    while (true) {
        if (esCapicua($x)) {
            return $x - $n;
        }
        $x++;
    }
}

function esCapicua(int $n): bool
{
    $s = (string)$n;
    return $s === strrev($s);
}
