<?php

function elSuenoDeLosConcursantes(array $noches): string
{
    // $noches: array de strings "HH:MM-HH:MM"
    $totalMin = 0;

    foreach ($noches as $noche) {
        [$ini, $fin] = explode('-', trim($noche));
        [$hi, $mi] = array_map('intval', explode(':', $ini));
        [$hf, $mf] = array_map('intval', explode(':', $fin));

        $minIni = $hi * 60 + $mi;
        $minFin = $hf * 60 + $mf;

        if ($minFin <= $minIni) {
            // Cruza medianoche
            $minFin += 24 * 60;
        }

        $totalMin += ($minFin - $minIni);
    }

    $horas = intdiv($totalMin, 60);
    $minutos = $totalMin % 60;

    $hStr = str_pad((string)$horas, 2, '0', STR_PAD_LEFT);
    $mStr = str_pad((string)$minutos, 2, '0', STR_PAD_LEFT);

    return $hStr . ':' . $mStr;
}
