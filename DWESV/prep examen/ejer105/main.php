<?php

require __DIR__ . '/ventas.php';

while (true) {
    $semana = [];
    for ($i = 0; $i < 6; $i++) {
        $line = trim(fgets(STDIN));
        if ($line === '') {
            // fin de entrada
            exit;
        }
        $valor = floatval(str_replace(',', '.', $line));
        // condición de fin: primer día -1
        if ($i === 0 && $valor == -1) {
            exit;
        }
        $semana[] = $valor;
    }

    echo ventas($semana) . PHP_EOL;
}