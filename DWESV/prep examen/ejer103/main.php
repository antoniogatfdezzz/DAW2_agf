<?php

require __DIR__ . '/problemasDeHerencia.php';

while (($line = trim(fgets(STDIN))) !== '') {
    $grado = intval($line);
    if ($grado === 20) {
        break;
    }

    // leer coeficientes
    $coefLine = trim(fgets(STDIN));
    $coefParts = preg_split('/\s+/', $coefLine, -1, PREG_SPLIT_NO_EMPTY);
    $coeficientes = array_map('floatval', $coefParts);

    // leer número de rectángulos
    $n = intval(trim(fgets(STDIN)));

    $res = problemasDeHerencia($coeficientes, $n);
    echo $res . PHP_EOL;
}