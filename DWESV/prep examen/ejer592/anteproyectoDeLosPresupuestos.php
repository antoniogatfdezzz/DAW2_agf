<?php

function anteproyectoDeLosPresupuestos(int $presupuesto, array $sueldos): int
{
    sort($sueldos);
    $n = count($sueldos);

    $bajo = 0;
    $alto = max($sueldos);
    $mejorU = 0;
    $mejorGasto = -1;

    while ($bajo <= $alto) {
        $mid = intdiv($bajo + $alto, 2);
        $gasto = 0;
        foreach ($sueldos as $s) {
            $gasto += ($s > $mid) ? $mid : $s;
            if ($gasto > $presupuesto) break;
        }

        if ($gasto > $presupuesto) {
            $alto = $mid - 1;
        } else {
            if ($gasto > $mejorGasto || ($gasto === $mejorGasto && $mid < $mejorU)) {
                $mejorGasto = $gasto;
                $mejorU = $mid;
            }
            $bajo = $mid + 1;
        }
    }

    return $mejorU;
}
