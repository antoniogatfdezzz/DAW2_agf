<?php

function colgadasOColgantes(string $palabra): string
{
    $p = strtolower($palabra);
    return ($p === 'colgadas') ? 'Bien' : 'Mal';
}
