"use strict";

/** Carga el módulo. Función de node.js para cargar módulos  */
    const prompt = require('prompt-sync')();

/** Contiene el valor actual en la memoria de la calculadora */
let pantalla = 0;

/** Permite almacenar el valor actual de la pantalla de la calculadora*/
let memoria = 0;

let fin = false;
while (!fin) {
    /* Mostar el contenido de la pantalla */
    console.log(`Valor actual: ${pantalla}`);

    /* Mostrar el menú */
    mostrarMenu();

    /* Leer la entrada del usuario */
    const operacion = prompt('Elige una opción: ');

    /* Ejecutar la operación seleccionada */
    pantalla = ejecutarOperacion(pantalla, operacion);
    
}

//Muestra el menú de opciones
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


/* Ejecuta una operación de la calculadora 

    @param 

*/
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