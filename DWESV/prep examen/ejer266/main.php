<?php

require __DIR__ . '/copistasDaltonicos.php';

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    [$filas, $cols] = array_map('intval', preg_split('/\s+/', $line));
    if ($filas === 0 && $cols === 0) break;
    $cuadro = [];
    for ($i = 0; $i < $filas; $i++) {
        $cuadro[] = rtrim(fgets(STDIN), "\r\n");
    }
    $k = intval(trim(fgets(STDIN)));
    $transformaciones = [];
    for ($i = 0; $i < $k; $i++) {
        $t = trim(fgets(STDIN));
        $parts = preg_split('/\s+/', $t);
        $transformaciones[] = [$parts[0], $parts[1]];
    }
    $resultado = copistasDaltonicos($cuadro, $transformaciones);
    foreach ($resultado as $fila) {
        echo $fila . PHP_EOL;
    }
}
