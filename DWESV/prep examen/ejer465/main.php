<?php

require __DIR__ . '/elCuelloDeLosPilotos.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$tx, $ty] = array_map('intval', preg_split('/\s+/', $line));
    $mapa = [];
    for ($i = 0; $i < $ty; $i++) {
        $mapa[] = rtrim(fgets(STDIN), "\r\n");
    }
    echo elCuelloDeLosPilotos($mapa) . PHP_EOL;
}
