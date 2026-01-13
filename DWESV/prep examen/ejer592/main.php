<?php

require __DIR__ . '/anteproyectoDeLosPresupuestos.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$presupuesto, $n] = array_map('intval', preg_split('/\s+/', $line));
    $sueldos = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
    echo anteproyectoDeLosPresupuestos($presupuesto, $sueldos) . PHP_EOL;
}
