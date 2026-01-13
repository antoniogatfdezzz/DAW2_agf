<?php

function arreglandoElAlgoritmo(array $v): int
{
    $n = count($v);
    if ($n === 0) return 0;

    // Contar cuántos elementos finales están ya en orden no decreciente.
    $cnt = 1;
    for ($i = $n - 1; $i > 0; $i--) {
        if ($v[$i - 1] <= $v[$i]) {
            $cnt++;
        } else {
            break;
        }
    }
    return $cnt;
}
