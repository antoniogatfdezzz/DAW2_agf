<?php

require __DIR__ . '/avituallamientoEnLasEtapasCiclistas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $alturas = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    echo avituallamientoEnLasEtapasCiclistas($alturas) . PHP_EOL;
}
