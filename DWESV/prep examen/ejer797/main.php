<?php

require __DIR__ . '/anunciosDeMoviles.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$filas, $cols] = array_map('intval', preg_split('/\s+/', $line));
    if ($filas === 0 && $cols === 0) break;
    $ini = [];
    $fin = [];
    for ($r = 0; $r < $filas; $r++) {
        $l = trim(fgets(STDIN));
        if ($l === '') {
            $r--;
            continue;
        }
        [$a, $b] = preg_split('/\s+/', $l);
        $ini[] = $a;
        $fin[] = $b;
    }
    $res = anunciosDeMoviles($ini, $fin);
    echo $res . PHP_EOL;
}
