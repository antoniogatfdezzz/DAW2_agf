<?php

require __DIR__ . '/cervantesShakespeareYElDiaDelLibro.php';

$line = fgets(STDIN);
if ($line !== false) {
    $t = intval(trim($line));
    for ($i = 0; $i < $t; $i++) {
        $parts = array_map('intval', preg_split('/\s+/', trim(fgets(STDIN))));
        [$d, $m, $y] = $parts;
        [$dd, $mm, $yy] = cervantesShakespeareYElDiaDelLibro($d, $m, $y);
        echo $dd . ' ' . $mm . ' ' . $yy . PHP_EOL;
    }
}
