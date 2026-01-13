<?php

function deNuevoEnElBarDeJavier(array $ventas): string
{
    // $ventas: array de [categoria, importe]
    $categorias = [
        'D' => 'DESAYUNOS',
        'A' => 'COMIDAS',
        'M' => 'MERIENDAS',
        'I' => 'CENAS',
        'C' => 'COPAS',
    ];

    $sumaPorCat = [
        'D' => 0.0,
        'A' => 0.0,
        'M' => 0.0,
        'I' => 0.0,
        'C' => 0.0,
    ];

    $conteoPorCat = [
        'D' => 0,
        'A' => 0,
        'M' => 0,
        'I' => 0,
        'C' => 0,
    ];

    $totalImporte = 0.0;
    $totalVentas = 0;

    foreach ($ventas as $venta) {
        if (!is_array($venta) || count($venta) < 2) {
            continue;
        }
        $cat = $venta[0];
        $importe = (float)$venta[1];

        if (!isset($sumaPorCat[$cat])) {
            continue;
        }

        $sumaPorCat[$cat] += $importe;
        $conteoPorCat[$cat]++;
        $totalImporte += $importe;
        $totalVentas++;
    }

    // Cálculo máximo y mínimo
    $max = max($sumaPorCat);
    $min = min($sumaPorCat);

    $catsMax = [];
    $catsMin = [];

    foreach ($sumaPorCat as $codigo => $suma) {
        if ($suma == $max) {
            $catsMax[] = $codigo;
        }
        if ($suma == $min) {
            $catsMin[] = $codigo;
        }
    }

    if (count($catsMax) === 1) {
        $catMaxNombre = $categorias[$catsMax[0]];
    } else {
        $catMaxNombre = 'EMPATE';
    }

    if (count($catsMin) === 1) {
        $catMinNombre = $categorias[$catsMin[0]];
    } else {
        $catMinNombre = 'EMPATE';
    }

    if ($totalVentas === 0) {
        $mediaDia = 0.0;
    } else {
        $mediaDia = $totalImporte / $totalVentas;
    }

    if ($conteoPorCat['A'] === 0) {
        $mediaComidas = 0.0;
    } else {
        $mediaComidas = $sumaPorCat['A'] / $conteoPorCat['A'];
    }

    $superaMedia = ($mediaComidas > $mediaDia) ? 'SI' : 'NO';

    return $catMaxNombre . '#' . $catMinNombre . '#' . $superaMedia;
}
