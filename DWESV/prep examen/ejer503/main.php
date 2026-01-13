<?php

require __DIR__ . '/dadosDeRol.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        [$d1, $d2] = $parts;
        $res = dadosDeRol($d1, $d2);
        echo implode(' ', $res) . PHP_EOL;
    }
}
