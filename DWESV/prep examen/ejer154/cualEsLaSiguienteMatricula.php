<?php

function cualEsLaSiguienteMatricula(string $matricula): string
{
    // Matricula formato "NNNN LLL"
    [$numStr, $letras] = explode(' ', trim($matricula));
    $num = (int)$numStr;

    // Alfabeto permitido sin vocales ni Ñ
    $alf = ['B','C','D','F','G','H','J','K','L','M','P','Q','R','S','T','V','W','X','Y','Z'];
    $pos = array_flip($alf);

    $l = str_split($letras);

    $num++;
    if ($num > 9999) {
        $num = 0;
        // incrementar letras estilo contador
        for ($i = 2; $i >= 0; $i--) {
            $idx = $pos[$l[$i]];
            $idx++;
            if ($idx >= count($alf)) {
                $idx = 0;
                $l[$i] = $alf[$idx];
                if ($i === 0) {
                    // se llegaría a ZZZ pero el enunciado no nos pide ir más allá
                }
            } else {
                $l[$i] = $alf[$idx];
                break;
            }
        }
    }

    $numStrOut = str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    return $numStrOut . ' ' . implode('', $l);
}
