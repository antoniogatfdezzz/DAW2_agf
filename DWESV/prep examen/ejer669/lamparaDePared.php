<?php

function lamparaDePared(array $varillas): int
{
    $conteo = [];
    foreach ($varillas as $v) {
        if (!isset($conteo[$v])) $conteo[$v] = 0;
        $conteo[$v]++;
    }

    $pares = 0;
    $cuad = 0;

    foreach ($conteo as $c) {
        $cuad += intdiv($c, 4);
        $resto = $c % 4;
        $pares += intdiv($resto, 2);
    }

    // Se pueden usar cuádruples como 2 pares si sobra
    $totalPares = $pares + 2 * $cuad;
    // Una lámpara necesita 3 pares (2 horizontales y 4 verticales)
    return intdiv($totalPares, 3);
}
