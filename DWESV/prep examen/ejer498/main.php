<?php

require __DIR__ . '/entrenamientoDeLosMonjesDeHanoi.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$k, $n] = array_map('intval', preg_split('/\s+/', $line));
    if ($k === 0 && $n === 0) break;
    [$kk, $nn, $m1, $m2] = entrenamientoDeLosMonjesDeHanoi($k, $n);
    echo $kk . ' ' . $nn . ' ' . $m1 . ' ' . $m2 . PHP_EOL;
}
