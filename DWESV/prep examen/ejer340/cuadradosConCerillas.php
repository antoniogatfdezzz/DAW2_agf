<?php

function cuadradosConCerillas(int $n, int $m): int
{
    // Para una rejilla de n x m cuadrados, hay (m+1)*n cerillas horizontales
    // y (n+1)*m cerillas verticales.
    return ($m + 1) * $n + ($n + 1) * $m;
}
