<?php

function reconstruyendoLaMuralla(array $alturas, array $consultas): array
{
    $n = count($alturas);
    // precomputo de prefijos de sumas
    $pref = [0];
    for ($i = 0; $i < $n; $i++) {
        $pref[$i + 1] = $pref[$i] + $alturas[$i];
    }
    // sparse table para máximos
    $log = [0];
    for ($i = 1; $i <= $n; $i++) {
        $log[$i] = ($i > 1) ? $log[intdiv($i, 2)] + 1 : 0;
    }
    $K = $log[$n];
    $st = [];
    $st[0] = $alturas;
    for ($k = 1; $k <= $K; $k++) {
        $len = 1 << $k;
        $st[$k] = [];
        for ($i = 0; $i + $len <= $n; $i++) {
            $st[$k][$i] = max($st[$k - 1][$i], $st[$k - 1][$i + ($len >> 1)]);
        }
    }
    $rmq = function (int $l, int $r) use (&$st, &$log) {
        $len = $r - $l + 1;
        $k = $log[$len];
        return max($st[$k][$l], $st[$k][$r - (1 << $k) + 1]);
    };

    $res = [];
    foreach ($consultas as $q) {
        [$A, $B] = $q; // 1-based
        $l = $A - 1;
        $r = $B - 1;
        $max = $rmq($l, $r);
        $suma = $pref[$r + 1] - $pref[$l];
        $res[] = $max * ($r - $l + 1) - $suma;
    }
    return $res;
}
