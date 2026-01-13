<?php

require __DIR__ . '/conectandoCables.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($case = 0; $case < $t; $case++) {
        $n = intval(trim(fgets(STDIN)));
        $cablesLine = trim(fgets(STDIN));
        $cables = preg_split('/\s+/', $cablesLine, -1, PREG_SPLIT_NO_EMPTY);
        echo conectandoCables($cables) . PHP_EOL;
    }
}
