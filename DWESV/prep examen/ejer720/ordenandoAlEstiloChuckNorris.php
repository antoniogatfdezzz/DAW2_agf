<?php

function ordenandoAlEstiloChuckNorris(array $v): int
{
    $n = count($v);
    $l = 0;
    $r = $n;
    while ($r - $l > 1) {
        $ordenado = true;
        for ($i = $l + 1; $i < $r; $i++) {
            if ($v[$i - 1] > $v[$i]) {
                $ordenado = false;
                break;
            }
        }
        if ($ordenado) break;
        $l = intdiv($l + $r + 1, 2); // descartar mitad izquierda (si impar, se queda la pequeña)
    }
    return $r - $l;
}
