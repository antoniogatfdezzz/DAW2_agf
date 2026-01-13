<?php

require __DIR__ . '/distanciaAlSiguienteCapicua.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $n = intval(trim(fgets(STDIN)));
        echo distanciaAlSiguienteCapicua($n) . PHP_EOL;
    }
}
