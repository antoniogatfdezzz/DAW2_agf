<?php

require __DIR__ . '/arreglandoElAlgoritmo.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $n = intval($line);
    if ($n === 0) break;
    $v = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    echo arreglandoElAlgoritmo($v) . PHP_EOL;
}
