<?php

require __DIR__ . '/enAscensorOAndando.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $parts = array_map('intval', preg_split('/\s+/', $line));
    if (count($parts) < 5) continue;
    [$f0, $f1, $fa, $ta, $te] = $parts;
    if ($f0 === 0 && $f1 === 0 && $fa === 0 && $ta === 0 && $te === 0) break;
    echo enAscensorOAndando($f0, $f1, $fa, $ta, $te) . PHP_EOL;
}
