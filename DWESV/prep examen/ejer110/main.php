<?php

require __DIR__ . '/estrofas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $versos = [];
    for ($i = 0; $i < $n; $i++) {
        $versos[] = rtrim(fgets(STDIN), "\r\n");
    }
    echo estrofas($versos) . PHP_EOL;
}
