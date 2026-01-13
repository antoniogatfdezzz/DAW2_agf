<?php

function copistasDaltonicos(array $cuadro, array $transformaciones): array
{
    // $cuadro: array de strings, cada una una fila
    // $transformaciones: array de [origen, destino] en orden de copistas

    $map = [];

    foreach ($transformaciones as $t) {
        if (!is_array($t) || count($t) < 2) {
            continue;
        }
        [$orig, $dest] = $t;
        $orig = $orig[0];
        $dest = $dest[0];

        // aplicar esta transformación a todos los colores ya mapeados
        foreach ($map as $c => $m) {
            if ($m === $orig) {
                $map[$c] = $dest;
            }
        }
        // y a sí mismo
        $map[$orig] = $dest;
    }

    $res = [];
    foreach ($cuadro as $fila) {
        $nueva = '';
        $len = strlen($fila);
        for ($i = 0; $i < $len; $i++) {
            $c = $fila[$i];
            if (isset($map[$c])) {
                $nueva .= $map[$c];
            } else {
                $nueva .= $c;
            }
        }
        $res[] = $nueva;
    }

    return $res;
}
