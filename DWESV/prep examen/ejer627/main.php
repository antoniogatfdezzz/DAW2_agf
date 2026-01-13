<?php

require __DIR__ . '/plinioElViejo.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $parts = preg_split('/\s+/', $line);
    if (count($parts) < 4) continue;
    [$fecha, $a, $m, $d] = $parts;
    if ($fecha === '00/00/0000' && intval($a) === 0 && intval($m) === 0 && intval($d) === 0) break;
    echo plinioElViejo($fecha, intval($a), intval($m), intval($d)) . PHP_EOL;
}
