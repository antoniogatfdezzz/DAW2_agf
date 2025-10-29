"use strict";

const prompt = require('prompt-sync')();

let pantalla = 0;

let memoria = 0;

let fin = false;
while (!fin) {
    console.log(`Valor actual: ${pantalla}`);

    mostrarMenu();

    const operacion = prompt('Elige una opción: ');

    pantalla = ejecutarOperacion(pantalla, operacion);
    
}

function mostrarMenu() {
    console.log('Operaciones disponibles:');
    console.log('+. Sumar');
    console.log('-. Restar');
    console.log('*. Multiplicar');
    console.log('/ Dividir');
    console.log('C. Pone la pantalla a cero');
    console.log('M. Guarda el valor de la pantalla en memoria');
    console.log('R. Recupera el valor de la memoria a la pantalla');
    console.log('S. Salir');
}

function ejecutarOperacion(operacion) {

    let resultado = 0;
    let operando2 = 0;

    switch (operacion) {
        case '+':
            operando2 = Number(prompt('Introduce el segundo sumando: '));
            resultado += operando2;
            break;
        case 'C':
            pantalla = 0;
            break;
        case 'M':
            memoria = pantalla;
            break;
        case 'R':
            resultado = memoria;
            break;
        case 'S':
            fin = true;
            break;
        default:
            console.log('Operación no soportada');
            break;
    }

    return resultado;
}