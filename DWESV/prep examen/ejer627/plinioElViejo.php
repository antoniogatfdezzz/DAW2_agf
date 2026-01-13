<?php

function plinioElViejo(string $fechaNac, int $aLuna, int $mLuna, int $dLuna): string
{
    // convertir fecha nacimiento a días julianos simples (calendario juliano con regla simple de bisiestos)
    [$d, $m, $y] = array_map('intval', explode('/', $fechaNac));

    $isLeap = function (int $year): bool {
        return $year % 4 === 0;
    };

    $daysInMonth = function (int $month, int $year, callable $isLeap): int {
        $dm = [0,31,28,31,30,31,30,31,31,30,31,30,31];
        if ($month === 2 && $isLeap($year)) return 29;
        return $dm[$month];
    };

    $toAbs = function (int $d, int $m, int $y) use ($isLeap, $daysInMonth): int {
        $days = 0;
        for ($yy = 1; $yy < $y; $yy++) {
            $days += $isLeap($yy) ? 366 : 365;
        }
        for ($mm = 1; $mm < $m; $mm++) {
            $days += $daysInMonth($mm, $y, $isLeap);
        }
        $days += $d - 1;
        return $days;
    };

    $fromAbs = function (int $abs) use ($isLeap, $daysInMonth): array {
        $y = 1;
        while (true) {
            $yd = $isLeap($y) ? 366 : 365;
            if ($abs >= $yd) {
                $abs -= $yd;
                $y++;
            } else break;
        }
        $m = 1;
        while (true) {
            $md = $daysInMonth($m, $y, $isLeap);
            if ($abs >= $md) {
                $abs -= $md;
                $m++;
            } else break;
        }
        $d = $abs + 1;
        return [$d, $m, $y];
    };

    $absNac = $toAbs($d, $m, $y);

    $diasLuna = $aLuna * 12 * 28 + $mLuna * 28 + $dLuna;

    $absFinal = $absNac + $diasLuna;

    [$df, $mf, $yf] = $fromAbs($absFinal);

    return sprintf('%02d/%02d/%04d', $df, $mf, $yf);
}
