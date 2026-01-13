<?php

require __DIR__ . '/partidoDeSquash.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === 'F') break;
    if ($line === '') continue;
    echo partidoDeSquash($line) . PHP_EOL;
}
