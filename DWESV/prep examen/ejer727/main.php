<?php

require __DIR__ . '/cumpleanosEnElJardinDeInfancia.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($case = 0; $case < $t; $case++) {
        $N = intval(trim(fgets(STDIN)));
        $columnas = array_fill(0, $N - 1, []);
        for ($col = 0; $col < $N - 1; $col++) {
            $l = trim(fgets(STDIN));
            if ($l === '') {
                $col--;
                continue;
            }
            $parts = preg_split('/\s+/', $l, -1, PREG_SPLIT_NO_EMPTY);
            $k = intval(array_shift($parts));
            $heights = [];
            for ($i = 0; $i < $k; $i++) {
                $heights[] = intval($parts[$i]);
            }
            $columnas[$col] = $heights;
        }
        [$peor, $igual, $mejor] = cumpleanosEnElJardinDeInfancia($N, $columnas);
        echo $peor . ' ' . $igual . ' ' . $mejor . PHP_EOL;
    }
}
