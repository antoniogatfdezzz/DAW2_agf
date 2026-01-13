<?php

function cervantesShakespeareYElDiaDelLibro(int $d, int $m, int $y): array
{
    // Convertimos la fecha española (proleptico gregoriano con sus saltos históricos)
    // a un número de días relativo, y restamos la diferencia con Inglaterra según tramo.

    // Función auxiliar: ¿es bisiesto gregoriano?
    $isLeap = function (int $year): bool {
        if ($year % 400 === 0) return true;
        if ($year % 100 === 0) return false;
        return $year % 4 === 0;
    };

    $daysInMonth = function (int $month, int $year, callable $isLeap): int {
        $dm = [0,31,28,31,30,31,30,31,31,30,31,30,31];
        if ($month === 2 && $isLeap($year)) return 29;
        return $dm[$month];
    };

    // Convertir fecha española a día absoluto contando desde 1/1/1 (gregoriano proleptico)
    $toAbs = function (int $d, int $m, int $y) use ($isLeap, $daysInMonth): int {
        $days = 0;
        for ($yy = 1; $yy < $y; $yy++) {
            $days += $isLeap($yy) ? 366 : 365;
        }
        for ($mm = 1; $mm < $m; $mm++) {
            $days += $daysInMonth($mm, $y, $isLeap);
        }
        $days += $d - 1;

        // Ajustes históricos del calendario español
        // Saltos 1582-10-05..14 inexistentes: ya modelados en proleptico, así que no tocamos.
        // Falta 29-2-1700: después de esa fecha, Inglaterra va un día por delante adicional.
        if ($y > 1700 || ($y === 1700 && ($m > 2 || ($m === 2 && $d > 28)))) {
            $days -= 1; // España se "salta" un día que en proleptico existiría
        }
        return $days;
    };

    $fromAbs = function (int $abs) use ($isLeap, $daysInMonth): array {
        $y = 1;
        while (true) {
            $yearDays = $isLeap($y) ? 366 : 365;
            if ($abs >= $yearDays) {
                $abs -= $yearDays;
                $y++;
            } else {
                break;
            }
        }
        $m = 1;
        while (true) {
            $mdays = $daysInMonth($m, $y, $isLeap);
            if ($abs >= $mdays) {
                $abs -= $mdays;
                $m++;
            } else {
                break;
            }
        }
        $d = $abs + 1;
        return [$d, $m, $y];
    };

    $absEsp = $toAbs($d, $m, $y);

    // Determinar diferencia de días entre calendarios según tramo histórico
    // Antes del 15-10-1582: calendarios iguales
    // 15-10-1582 .. 28-02-1700 (inclusive): Inglaterra va 10 días por detrás
    // Desde 01-03-1700 hasta 13-09-1752 (inclusive): Inglaterra va 11 días por detrás
    // Desde 14-09-1752: calendarios coinciden

    $comp = function (int $d1,int $m1,int $y1,int $d2,int $m2,int $y2): int {
        if ($y1 !== $y2) return $y1 < $y2 ? -1 : 1;
        if ($m1 !== $m2) return $m1 < $m2 ? -1 : 1;
        if ($d1 !== $d2) return $d1 < $d2 ? -1 : 1;
        return 0;
    };

    $diff = 0;
    if ($comp($y,$m,$d,1582,10,15) < 0) {
        $diff = 0;
    } elseif ($comp($y,$m,$d,1700,2,28) <= 0) {
        $diff = 10;
    } elseif ($comp($y,$m,$d,1752,9,13) <= 0) {
        $diff = 11;
    } else {
        $diff = 0;
    }

    $absEng = $absEsp - $diff;
    return $fromAbs($absEng);
}
