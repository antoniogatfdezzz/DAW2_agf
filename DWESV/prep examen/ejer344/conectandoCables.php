<?php

function conectandoCables(array $cables): string
{
    // $cables: array de strings "HM", "MH", "HH", "MM"...
    $deg = ['H' => 0, 'M' => 0];
    $hasEdge = false;
    foreach ($cables as $c) {
        $c = trim($c);
        if ($c === '') continue;
        $a = $c[0];
        $b = $c[1];
        $deg[$a]++;
        $deg[$b]++;
        $hasEdge = true;
    }
    if (!$hasEdge) {
        return 'IMPOSIBLE';
    }
    // Para tener un ciclo euleriano en un grafo con 2 vértices basta que todos los grados sean pares
    foreach ($deg as $d) {
        if ($d % 2 !== 0) {
            return 'IMPOSIBLE';
        }
    }
    return 'POSIBLE';
}
