<?php

function codificacionLimite(string $cod): string
{
    $pos = 0;
    $res = '';
    parseNodo($cod, $pos, $res);
    return $res;
}

function parseNodo(string $s, int &$i, string &$out): void
{
    if ($i >= strlen($s)) return;
    $c = $s[$i++];
    if ($c === '.') {
        return;
    }
    // nodo: carácter c, luego hijo izquierdo y derecho
    $leftStart = $i;
    // procesar hijo izquierdo
    $outLeft = '';
    parseNodo($s, $i, $outLeft);
    // procesar hijo derecho
    $outRight = '';
    parseNodo($s, $i, $outRight);
    if ($outLeft === '' && $outRight === '') {
        $out .= $c;
    } else {
        $out .= $outLeft . $outRight;
    }
}
