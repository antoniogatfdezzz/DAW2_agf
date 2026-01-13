<?php

function entrenandoParaLaVueltaCiclista(array $extras): int
{
    // $extras: lista de incrementos diarios (hasta encontrar 0, que no se incluye)
    $distPrev = 0; // distancia máxima del día anterior desde salida
    $total = 0;

    foreach ($extras as $e) {
        if ($e === 0) {
            break;
        }
        $distHoy = $distPrev + $e;
        // kilómetro total de hoy es ida y vuelta: 2*distHoy
        $total += 2 * $distHoy;
        $distPrev = $distHoy;
    }

    return $total;
}
