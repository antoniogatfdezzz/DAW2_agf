<?php

require __DIR__ . '/cualEsLaSiguienteMatricula.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '0') break;
    if ($line === '') continue;
    echo cualEsLaSiguienteMatricula($line) . PHP_EOL;
}
