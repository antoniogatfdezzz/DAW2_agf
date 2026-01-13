<?php

function partidoDeSquash(string $secuencia): string
{
    // secuencia: cadena de 'A' y 'B' terminada en 'F'
    $puntosSets = [];
    $setA = 0;
    $setB = 0;
    $setsGanadosA = 0;
    $setsGanadosB = 0;
    $saque = 'A';

    $len = strlen($secuencia);
    for ($i = 0; $i < $len; $i++) {
        $c = $secuencia[$i];
        if ($c === 'F') {
            break;
        }

        if ($setsGanadosA === 3 || $setsGanadosB === 3) {
            // Partido ya acabado, pero pueden venir puntos extra; se ignoran
            continue;
        }

        if ($c === $saque) {
            if ($c === 'A') {
                $setA++;
            } else {
                $setB++;
            }
        } else {
            // Solo cambia el saque
            $saque = $c;
        }

        // Comprobar si termina set
        $max = max($setA, $setB);
        $min = min($setA, $setB);
        if ($max >= 9 && ($max - $min) >= 2) {
            if ($setA > $setB) {
                $setsGanadosA++;
            } else {
                $setsGanadosB++;
            }
            $puntosSets[] = $setA . '-' . $setB;
            $setA = 0;
            $setB = 0;
            $saque = ($setA > $setB) ? 'A' : 'B';
            // Según enunciado, el ganador del set saca en el siguiente.
            if ($setsGanadosA + $setsGanadosB < 5) {
                $saque = ($setsGanadosA > $setsGanadosB) ? 'A' : 'B';
            }
        }
    }

    // Si el último set está empezado pero no terminado, se muestra también
    if ($setA !== 0 || $setB !== 0 || empty($puntosSets)) {
        $puntosSets[] = $setA . '-' . $setB;
    }

    return implode(' ', $puntosSets);
}
