<?php

require __DIR__ . '/aprobarQuimica.php';

while (($nombre = fgets(STDIN)) !== false) {
    $nombre = trim($nombre);
    if ($nombre === '') continue;
    if ($nombre === 'Exit') break;
    $zLine = trim(fgets(STDIN));
    $z = intval($zLine);
    echo aprobarQuimica($z) . PHP_EOL;
}
