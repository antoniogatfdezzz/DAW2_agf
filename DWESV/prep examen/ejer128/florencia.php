<?php

function florencia(int $longVarilla, array $paraguas)
{
    // $paraguas: lista de [nervios, segmentos, longitud]
    if ($longVarilla <= 0) {
        return 'IMPOSIBLE';
    }

    $totalNecesario = 0;
    foreach ($paraguas as $p) {
        if (!is_array($p) || count($p) < 3) {
            continue;
        }
        [$nervios, $segmentos, $lseg] = $p;
        $longParaguas = $nervios * $segmentos * $lseg;
        $totalNecesario += $longParaguas;
    }

    // Si algún paraguas individualmente no cabe en una varilla, es imposible
    foreach ($paraguas as $p) {
        [$nervios, $segmentos, $lseg] = $p;
        $longParaguas = $nervios * $segmentos * $lseg;
        if ($longParaguas > $longVarilla) {
            return 'IMPOSIBLE';
        }
    }

    // Simulación voraz simple: cada paraguas consume una varilla entera
    // Este modelo es suficiente para preparar una estructura básica aunque no minimiza varillas.
    $varillas = 0;
    $retalTotal = 0;
    foreach ($paraguas as $p) {
        [$nervios, $segmentos, $lseg] = $p;
        $longParaguas = $nervios * $segmentos * $lseg;
        $necesarias = intdiv($longParaguas, $longVarilla);
        if ($longParaguas % $longVarilla !== 0) {
            $necesarias++;
        }
        $varillas += $necesarias;
        $retalTotal += $necesarias * $longVarilla - $longParaguas;
    }

    return $varillas . ' ' . $retalTotal;
}
