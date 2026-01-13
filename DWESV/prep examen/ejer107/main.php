<?php

require __DIR__ . '/aproximacionDeGauss.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$n, $m] = array_map('intval', preg_split('/\s+/', $line));
    if ($n === 0 && $m === 0) break;
    echo aproximacionDeGauss($n, $m) . PHP_EOL;
}
