<?php

function anunciosDeMoviles(array $ini, array $fin)
{
    $cellsIni = [];
    $cellsFin = [];
    $h = count($ini);
    for ($r = 0; $r < $h; $r++) {
        $w = strlen($ini[$r]);
        for ($c = 0; $c < $w; $c++) {
            $cellsIni[] = $ini[$r][$c];
            $cellsFin[] = $fin[$r][$c];
        }
    }
    $n = count($cellsIni);
    $need1 = 0;
    $need0 = 0;
    $avail1 = 0;
    $avail0 = 0;
    $availR = 0;

    for ($i = 0; $i < $n; $i++) {
        $a = $cellsIni[$i];
        $b = $cellsFin[$i];
        if ($b === '1') $need1++;
        if ($b === '0') $need0++;
        if ($a === '1') $avail1++;
        elseif ($a === '0') $avail0++;
        else $availR++;
    }

    if ($avail1 + $availR < $need1) return 'IMPOSSIBLE';
    if ($avail0 + $avail1 + $availR < $need0 + $need1) return 'IMPOSSIBLE';

    // coste mínimo = apagados necesarios + reinicios necesarios
    $extra1 = max(0, $need1 - $avail1);
    $reinicios = $extra1; // convertir R a 1
    $apagados = max(0, $need0 - $avail0);

    return $reinicios + $apagados;
}
