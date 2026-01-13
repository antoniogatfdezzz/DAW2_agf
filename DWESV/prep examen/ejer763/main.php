<?php

require __DIR__ . '/colgadasOColgantes.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $pal = rtrim(fgets(STDIN), "\r\n");
        echo colgadasOColgantes($pal) . PHP_EOL;
    }
}
