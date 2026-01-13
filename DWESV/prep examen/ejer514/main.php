<?php

require __DIR__ . '/elMejorDatoDelParo.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $tasas = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    $res = elMejorDatoDelParo($tasas);
    echo implode(' ', $res) . PHP_EOL;
}
