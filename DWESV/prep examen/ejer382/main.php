<?php

require __DIR__ . '/internetEnElMetro.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($case = 0; $case < $t; $case++) {
        $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        [$L, $k] = $parts;
        $vals = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        $antenas = [];
        for ($i = 0; $i < $k; $i++) {
            $pos = $vals[2 * $i];
            $rad = $vals[2 * $i + 1];
            $antenas[] = [$pos, $rad];
        }
        echo internetEnElMetro($L, $antenas) . PHP_EOL;
    }
}
