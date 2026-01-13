<?php

require __DIR__ . '/reinasAtacadas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$n, $q] = array_map('intval', preg_split('/\s+/', $line));
    if ($n === 0 && $q === 0) break;
    $coords = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    $reinas = [];
    for ($i = 0; $i < $q; $i++) {
        $x = $coords[2 * $i];
        $y = $coords[2 * $i + 1];
        $reinas[] = [$x, $y];
    }
    echo reinasAtacadas($n, $reinas) . PHP_EOL;
}
