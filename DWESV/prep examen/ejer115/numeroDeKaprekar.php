<?php

function numeroDeKaprekar(int $n): string
{
    if ($n < 1) {
        return 'NO';
    }

    $cuad = $n * $n;
    $s = (string)$cuad;
    $len = strlen($s);

    for ($i = 1; $i < $len; $i++) {
        $izqStr = substr($s, 0, $i);
        $derStr = substr($s, $i);

        // La parte derecha no puede ser cero
        if ((int)$derStr === 0) {
            continue;
        }

        $izq = ($izqStr === '') ? 0 : (int)$izqStr;
        $der = (int)$derStr;

        if ($izq + $der === $n) {
            return 'SI';
        }
    }

    // Caso especial en el que la parte izquierda sea 0 (por ejemplo 1)
    if ($n === 1) {
        return 'SI';
    }

    return 'NO';
}
