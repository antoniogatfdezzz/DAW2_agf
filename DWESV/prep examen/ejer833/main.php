<?php

require __DIR__ . '/laberintoDiafano.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$Tx, $Ty] = array_map('intval', preg_split('/\s+/', $line));
    if ($Tx === 0 && $Ty === 0) break;
    $grid = [];
    for ($r = 0; $r < $Ty; $r++) {
        $row = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        $grid[] = $row;
    }
    echo laberintoDiafano($grid) . PHP_EOL;
}
