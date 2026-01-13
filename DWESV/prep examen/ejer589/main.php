<?php

require __DIR__ . '/igualandoCopas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $copas = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    echo igualandoCopas($copas) . PHP_EOL;
}
