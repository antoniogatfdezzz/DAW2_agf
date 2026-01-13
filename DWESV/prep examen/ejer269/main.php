<?php

require __DIR__ . '/entrenandoParaLaVueltaCiclista.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($case = 0; $case < $t; $case++) {
        $valores = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        echo entrenandoParaLaVueltaCiclista($valores) . PHP_EOL;
    }
}
