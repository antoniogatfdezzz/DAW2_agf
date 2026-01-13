<?php

require __DIR__ . '/carreraPopular.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '0') break;
    if ($line === '') continue;
    $n = intval($line);
    $participantes = [];
    for ($i = 0; $i < $n; $i++) {
        $participantes[] = trim(fgets(STDIN));
    }
    echo carreraPopular($participantes) . PHP_EOL;
}
