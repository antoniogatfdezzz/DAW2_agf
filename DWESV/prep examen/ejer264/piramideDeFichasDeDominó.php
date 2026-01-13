<?php

function piramideDeFichasDeDomino(int $fichas): int
{
    // Queremos la mayor altura h tal que existe una progresión de longitud h,
    // con términos enteros positivos consecutivos, que suma exactamente 'fichas'.
    // Suma = h * (2a + (h-1)) / 2, donde a es nº de fichas del piso superior.
    // Recorremos h desde 1 hasta mientras h*(h+1)/2 <= fichas.

    $mejorH = 0;
    for ($h = 1; (int)(($h * ($h + 1)) / 2) <= $fichas; $h++) {
        $num = 2 * $fichas - $h * ($h - 1);
        if ($num <= 0) {
            continue;
        }
        if ($num % (2 * $h) !== 0) {
            continue;
        }
        $a = (int)($num / (2 * $h));
        if ($a <= 0) {
            continue;
        }
        // suma comprobada implícitamente por la fórmula
        if ($h > $mejorH) {
            $mejorH = $h;
        }
    }

    return $mejorH;
}
