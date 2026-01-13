<?php

require __DIR__ . '/enchufandoLasLucesDeNavidad.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $N = intval($line);
    if ($N === 0) break;
    $regletas = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    echo enchufandoLasLucesDeNavidad($regletas) . PHP_EOL;
}
