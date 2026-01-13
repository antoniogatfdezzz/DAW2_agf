<?php

require __DIR__ . '/reconstruyendoLaMuralla.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($case = 0; $case < $t; $case++) {
        $N = intval(trim(fgets(STDIN)));
        $alturas = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        $Q = intval(trim(fgets(STDIN)));
        $consultas = [];
        for ($i = 0; $i < $Q; $i++) {
            $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
            $consultas[] = $parts; // [A, B]
        }
        $res = reconstruyendoLaMuralla($alturas, $consultas);
        foreach ($res as $val) {
            echo $val . PHP_EOL;
        }
        echo "---" . PHP_EOL;
    }
}
