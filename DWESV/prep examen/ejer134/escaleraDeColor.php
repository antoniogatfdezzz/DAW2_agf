<?php

function escaleraDeColor(array $cartas): string
{
    // $cartas: array de 4 strings tipo "2C", "10P", "JD", etc.

    if (count($cartas) !== 4) {
        return 'NADA';
    }

    $valMap = [
        '2' => 2, '3' => 3, '4' => 4, '5' => 5,
        '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13, 'A' => 14,
    ];

    // Parseo de cartas
    $palos = [];
    $vals = [];

    foreach ($cartas as $c) {
        $c = trim($c);
        $len = strlen($c);
        if ($len < 2) {
            return 'NADA';
        }
        $palo = $c[$len - 1];
        $numStr = substr($c, 0, $len - 1);
        if (!isset($valMap[$numStr])) {
            return 'NADA';
        }
        $palos[] = $palo;
        $vals[] = $valMap[$numStr];
    }

    // Todos los palos iguales
    if (count(array_unique($palos)) !== 1) {
        return 'NADA';
    }
    $paloComun = $palos[0];

    sort($vals);
    $vals = array_values(array_unique($vals));
    if (count($vals) !== 4) {
        return 'NADA';
    }

    $mejorCarta = null;
    $mejorValor = -1;

    // Probar todas las posibles escaleras con As alto (10-14 máximo)
    for ($inicio = 2; $inicio <= 10; $inicio++) {
        $secuencia = [];
        for ($k = 0; $k < 5; $k++) {
            $secuencia[] = $inicio + $k;
        }
        // As solo como 14, por condición del enunciado
        if (in_array(1, $secuencia, true)) {
            continue;
        }

        $presentes = 0;
        foreach ($vals as $v) {
            if (in_array($v, $secuencia, true)) {
                $presentes++;
            }
        }

        if ($presentes === 4) {
            // falta exactamente una carta, elegimos la que no está de la secuencia
            foreach ($secuencia as $v) {
                if (!in_array($v, $vals, true)) {
                    if ($v > $mejorValor) {
                        $mejorValor = $v;
                    }
                    break;
                }
            }
        }
    }

    if ($mejorValor === -1) {
        return 'NADA';
    }

    $revValMap = [];
    foreach ($valMap as $k => $v) {
        $revValMap[$v] = $k;
    }

    $valorStr = $revValMap[$mejorValor];
    return $valorStr . ' ' . $paloComun;
}
