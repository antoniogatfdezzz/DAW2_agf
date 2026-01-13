<?php

require __DIR__ . '/cartelesEnPapelContinuo.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$cols, $rows] = array_map('intval', preg_split('/\s+/', $line));
    if ($cols === 0 && $rows === 0) break;
    $lineas = [];
    for ($i = 0; $i < $rows + 2; $i++) {
        $lineas[] = rtrim(fgets(STDIN), "\r\n");
    }
    $out = cartelesEnPapelContinuo($lineas);
    foreach ($out as $l) {
        echo $l . PHP_EOL;
    }
}
