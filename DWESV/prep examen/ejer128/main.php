<?php

require __DIR__ . '/florencia.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $longVarilla = intval($line);
    if ($longVarilla === 0) break;
    $n = intval(trim(fgets(STDIN)));
    $paraguas = [];
    for ($i = 0; $i < $n; $i++) {
        $paraguas[] = intval(trim(fgets(STDIN)));
    }
    echo florencia($longVarilla, $paraguas) . PHP_EOL;
}
