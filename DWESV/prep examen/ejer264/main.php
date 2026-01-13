<?php

require __DIR__ . '/piramideDeFichasDeDomino.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $f = intval($line);
    echo piramideDeFichasDeDomino($f) . PHP_EOL;
}
