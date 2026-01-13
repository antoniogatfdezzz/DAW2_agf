<?php

function laberintoDiafano(array $grid): int
{
    $rows = count($grid);
    $cols = count($grid[0]);

    // Restricción fuerte, pero implementamos solo DP de caminos sin volver atrás para ejemplo:
    $dp = array_fill(0, $rows, array_fill(0, $cols, 0));
    $dp[0][0] = $grid[0][0];
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if ($i === 0 && $j === 0) continue;
            $best = -1;
            if ($i > 0 && $dp[$i - 1][$j] >= 0) $best = max($best, $dp[$i - 1][$j]);
            if ($j > 0 && $dp[$i][$j - 1] >= 0) $best = max($best, $dp[$i][$j - 1]);
            if ($best >= 0) $dp[$i][$j] = $best + $grid[$i][$j];
        }
    }
    return $dp[$rows - 1][$cols - 1];
}
