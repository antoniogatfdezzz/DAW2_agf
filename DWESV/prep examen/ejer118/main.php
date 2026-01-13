<?php

require __DIR__ . '/apuestaConRecetas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$pilar, $marco, $pedro] = array_map('intval', preg_split('/\s+/', $line));
    if ($pilar < 0) break;
    echo apuestaConRecetas($pilar, $marco, $pedro) . PHP_EOL;
}
