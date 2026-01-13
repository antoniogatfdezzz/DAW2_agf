<?php

require __DIR__ . '/notacionForsythEdwards.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $fen = trim(fgets(STDIN));
        echo notacionForsythEdwards($fen) . PHP_EOL;
    }
}
