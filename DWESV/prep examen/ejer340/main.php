<?php

require __DIR__ . '/cuadradosConCerillas.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        [$n, $m] = $parts;
        echo cuadradosConCerillas($n, $m) . PHP_EOL;
    }
}
