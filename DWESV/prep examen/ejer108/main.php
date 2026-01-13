<?php

require __DIR__ . '/deNuevoEnElBarDeJavier.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $ventas = [];
    while (true) {
        [$cat, $val] = preg_split('/\s+/', $line);
        if ($cat === 'N') {
            // fin del día
            echo deNuevoEnElBarDeJavier($ventas) . PHP_EOL;
            break;
        } else {
            $importe = floatval(str_replace(',', '.', $val));
            $ventas[] = [$cat, $importe];
        }
        $line = fgets(STDIN);
        if ($line === false) break 2;
        $line = trim($line);
    }
}
