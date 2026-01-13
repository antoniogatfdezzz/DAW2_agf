<?php

function reinasAtacadas(int $n, array $reinas): string
{
    // $reinas: array de [x, y]
    $filas = [];
    $cols = [];
    $diag1 = [];// x-y
    $diag2 = [];// x+y

    foreach ($reinas as $r) {
        [$x, $y] = $r;
        if (isset($filas[$y]) || isset($cols[$x]) || isset($diag1[$x - $y]) || isset($diag2[$x + $y])) {
            return 'SI';
        }
        $filas[$y] = true;
        $cols[$x] = true;
        $diag1[$x - $y] = true;
        $diag2[$x + $y] = true;
    }

    return 'NO';
}
