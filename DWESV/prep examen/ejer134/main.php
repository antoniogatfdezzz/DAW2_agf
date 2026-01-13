<?php

require __DIR__ . '/escaleraDeColor.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    if ($line === '0') break;
    $cartas = preg_split('/\s+/', $line);
    echo escaleraDeColor($cartas) . PHP_EOL;
}
