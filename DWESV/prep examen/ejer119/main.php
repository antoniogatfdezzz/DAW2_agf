<?php

require __DIR__ . '/escudosDelEjercitoRomano.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    echo escudosDelEjercitoRomano($n) . PHP_EOL;
}
