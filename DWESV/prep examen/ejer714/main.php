<?php

require __DIR__ . '/faseFinal.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $N = intval($line);
    if ($N === 0) break;

    // Leer nombres de equipos
    $equipos = [];
    while (count($equipos) < $N && ($l2 = fgets(STDIN)) !== false) {
        $l2 = trim($l2);
        if ($l2 === '') continue;
        $parts = preg_split('/\s+/', $l2);
        $equipos = array_merge($equipos, $parts);
    }
    $equipos = array_slice($equipos, 0, $N);

    // Leer marcadores (2*(N-1) enteros)
    $need = 2 * ($N - 1);
    $marcadores = [];
    while (count($marcadores) < $need && ($ls = fgets(STDIN)) !== false) {
        $ls = trim($ls);
        if ($ls === '') continue;
        $parts = array_map('intval', preg_split('/\s+/', $ls));
        $marcadores = array_merge($marcadores, $parts);
    }
    $marcadores = array_slice($marcadores, 0, $need);

    echo faseFinal($N, $equipos, $marcadores) . PHP_EOL;
}
