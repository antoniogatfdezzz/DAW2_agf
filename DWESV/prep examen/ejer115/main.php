<?php

require __DIR__ . '/numeroDeKaprekar.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    echo numeroDeKaprekar($n) . PHP_EOL;
}
