<?php

require __DIR__ . '/votacionesCapicua.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$N, $Q] = array_map('intval', preg_split('/\s+/', $line));
    if ($N === 0 && $Q === 0) break;
    [$nIn, $qIn, $M] = votacionesCapicua($N, $Q);
    echo $nIn . ' ' . $qIn . ' ' . $M . PHP_EOL;
}
