<?php

require __DIR__ . '/elSuenoDeLosConcursantes.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '0') break;
    if ($line === '') continue;
    $n = intval($line);
    $noches = [];
    for ($i = 0; $i < $n; $i++) {
        $noches[] = trim(fgets(STDIN));
    }
    echo elSuenoDeLosConcursantes($noches) . PHP_EOL;
}
