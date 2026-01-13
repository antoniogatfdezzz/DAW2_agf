<?php

require __DIR__ . '/codificacionLimite.php';

while (($line = fgets(STDIN)) !== false) {
    $line = rtrim($line, "\r\n");
    if ($line === '') continue;
    echo codificacionLimite($line) . PHP_EOL;
}
