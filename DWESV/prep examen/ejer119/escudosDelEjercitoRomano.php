<?php

function escudosDelEjercitoRomano(int $legionarios): int
{
    $totalEscudos = 0;
    $restantes = $legionarios;

    while ($restantes > 0) {
        $lado = (int)floor(sqrt($restantes));
        $tamanio = $lado * $lado;
        $restantes -= $tamanio;

        // Cálculo de escudos en un cuadrado de lado $lado
        if ($lado === 1) {
            // Un solo legionario con escudos por todos lados y arriba: 4
            $totalEscudos += 4;
        } else {
            // Esquinas: 4 legionarios, cada uno con 3 escudos
            $esquinas = 4 * 3;
            // Bordes no esquina: 4 lados, cada uno con (lado-2) legionarios*2 escudos laterales +1 superior
            $bordesNoEsq = 0;
            if ($lado > 2) {
                $bordesNoEsq = 4 * ($lado - 2) * 3;
            }
            // Interiores: (lado-2)^2 legionarios, cada uno con 1 escudo superior
            $interiores = 0;
            if ($lado > 2) {
                $interiores = ($lado - 2) * ($lado - 2);
            }

            $totalEscudos += $esquinas + $bordesNoEsq + $interiores;
        }
    }

    return $totalEscudos;
}
