<?php

function cumpleanosEnElJardinDeInfancia(int $N, array $columnas): array
{
    // Construimos permutación final de posiciones 0..N-1 tras seguir el amidakuji
    $perm = range(0, $N - 1);
    // Recorremos alturas de arriba a abajo: ordenamos las conexiones por altura
    $eventos = [];
    foreach ($columnas as $col => $lineas) {
        foreach ($lineas as $h) {
            $eventos[] = [$h, $col];
        }
    }
    sort($eventos);
    foreach ($eventos as [$h, $col]) {
        $tmp = $perm[$col];
        $perm[$col] = $perm[$col + 1];
        $perm[$col + 1] = $tmp;
    }

    $peor = $igual = $mejor = 0;
    for ($i = 0; $i < $N; $i++) {
        $fin = $perm[$i];
        if ($fin < $i) $peor++;
        elseif ($fin === $i) $igual++;
        else $mejor++;
    }
    return [$peor, $igual, $mejor];
}
