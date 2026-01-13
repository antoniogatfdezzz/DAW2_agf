<?php

function dadosDeRol(int $d1, int $d2): array
{
    $maxSuma = $d1 + $d2;
    $cont = array_fill(0, $maxSuma + 1, 0);

    for ($i = 1; $i <= $d1; $i++) {
        for ($j = 1; $j <= $d2; $j++) {
            $cont[$i + $j]++;
        }
    }

    $max = max($cont);
    $res = [];
    for ($s = 2; $s <= $maxSuma; $s++) {
        if ($cont[$s] === $max) {
            $res[] = $s;
        }
    }

    return $res;
}
