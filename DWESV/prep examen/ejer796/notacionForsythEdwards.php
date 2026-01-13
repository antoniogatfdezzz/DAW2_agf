<?php

function notacionForsythEdwards(string $fen): int
{
    $tab = array_fill(0, 8, array_fill(0, 8, '.'));
    $ranks = explode('/', $fen);
    for ($r = 0; $r < 8; $r++) {
        $c = 0;
        for ($i = 0; $i < strlen($ranks[$r]); $i++) {
            $ch = $ranks[$r][$i];
            if (ctype_digit($ch)) {
                $c += intval($ch);
            } else {
                $tab[$r][$c] = $ch;
                $c++;
            }
        }
    }

    $att = array_fill(0, 8, array_fill(0, 8, false));

    $inside = function ($r, $c) {
        return $r >= 0 && $r < 8 && $c >= 0 && $c < 8;
    };

    for ($r = 0; $r < 8; $r++) {
        for ($c = 0; $c < 8; $c++) {
            $p = $tab[$r][$c];
            if ($p === '.') continue;
            $isWhite = ctype_upper($p);
            $pc = strtolower($p);
            if ($pc === 'p') {
                $dr = $isWhite ? -1 : 1;
                foreach ([-1, 1] as $dc) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($inside($nr, $nc)) $att[$nr][$nc] = true;
                }
            } elseif ($pc === 'n') {
                $deltas = [[-2,-1],[-2,1],[-1,-2],[-1,2],[1,-2],[1,2],[2,-1],[2,1]];
                foreach ($deltas as $d) {
                    $nr = $r + $d[0];
                    $nc = $c + $d[1];
                    if ($inside($nr, $nc)) $att[$nr][$nc] = true;
                }
            } elseif ($pc === 'k') {
                for ($dr=-1;$dr<=1;$dr++) for ($dc=-1;$dc<=1;$dc++) {
                    if ($dr==0 && $dc==0) continue;
                    $nr=$r+$dr; $nc=$c+$dc;
                    if ($inside($nr,$nc)) $att[$nr][$nc]=true;
                }
            } else {
                $dirs = [];
                if ($pc === 'r' || $pc === 'q') {
                    $dirs = array_merge($dirs, [[1,0],[-1,0],[0,1],[0,-1]]);
                }
                if ($pc === 'b' || $pc === 'q') {
                    $dirs = array_merge($dirs, [[1,1],[1,-1],[-1,1],[-1,-1]]);
                }
                foreach ($dirs as $d) {
                    $nr = $r + $d[0];
                    $nc = $c + $d[1];
                    while ($inside($nr, $nc)) {
                        $att[$nr][$nc] = true;
                        if ($tab[$nr][$nc] !== '.') break;
                        $nr += $d[0];
                        $nc += $d[1];
                    }
                }
            }
        }
    }

    $libres = 0;
    for ($r = 0; $r < 8; $r++) {
        for ($c = 0; $c < 8; $c++) {
            if ($tab[$r][$c] === '.' && !$att[$r][$c]) $libres++;
        }
    }
    return $libres;
}
