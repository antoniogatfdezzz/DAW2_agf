<?php

require __DIR__ . '/potitos.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $lineas = [];
    for ($i = 0; $i < $n; $i++) {
        $lineas[] = rtrim(fgets(STDIN), "\r\n");
    }
    echo potitos($lineas) . PHP_EOL;
}
