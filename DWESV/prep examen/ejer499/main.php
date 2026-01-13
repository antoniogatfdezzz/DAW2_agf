<?php

require __DIR__ . '/modificacionDeTablas.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$F, $C, $N] = array_map('intval', preg_split('/\s+/', $line));
    if ($F === 0 && $C === 0 && $N === 0) break;
    $mods = [];
    for ($i = 0; $i < $N; $i++) {
        $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        $mods[] = $parts; // [I, A, B, M]
    }
    $tabla = modificacionDeTablas($F, $C, $mods);
    foreach ($tabla as $fila) {
        echo implode(' ', $fila) . PHP_EOL;
    }
}
