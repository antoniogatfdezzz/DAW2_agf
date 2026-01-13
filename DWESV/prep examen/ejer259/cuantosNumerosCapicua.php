<?php

function cuantosNumerosCapicua(int $digitos): string
{
    if ($digitos <= 0) {
        return '0';
    }
    if ($digitos === 1) {
        return '10';
    }

    // Números capicúa de d dígitos en base 10
    // Se determinan por la mitad de los dígitos (redondeo hacia arriba), 
    // el primero no puede ser 0.
    $k = intdiv($digitos, 2);
    if ($digitos % 2 === 1) {
        $k = $k + 1;
    }

    $cantidad = 9 * pow(10, $k - 1);
    return (string)$cantidad;
}
