<?php

require __DIR__ . '/cuantosNumerosCapicua.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $d = intval($line);
    if ($d === 0) break;
    echo cuantosNumerosCapicua($d) . PHP_EOL;
}
