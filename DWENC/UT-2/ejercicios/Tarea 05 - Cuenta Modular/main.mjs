"use strict";

import { Cuenta } from './cuenta.mjs';

export function ejercicio0603() {
    let resultado = "=== CLASE CUENTA ===\n\n";

    try {
        resultado += "=== Creando cuentas ===\n";

        const cuenta1 = new Cuenta("Ana García");
        resultado += `Cuenta 1: ${cuenta1.toString()}\n`;

        const cuenta2 = new Cuenta("Carlos López", 1500.75);
        resultado += `Cuenta 2: ${cuenta2.toString()}\n`;

        const cuenta3 = new Cuenta("María Ruiz", -100);
        resultado += `Cuenta 3 (cantidad negativa): ${cuenta3.toString()}\n\n`;

        resultado += "=== Operaciones ===\n";

        cuenta1.ingresar(500);
        resultado += `Después de ingresar 500€ en cuenta1: ${cuenta1.toString()}\n`;

        cuenta1.ingresar(-100);
        resultado += `Después de intentar ingresar -100€: ${cuenta1.toString()}\n`;

        cuenta1.retirar(200);
        resultado += `Después de retirar 200€: ${cuenta1.toString()}\n`;

        cuenta2.retirar(2000);
        resultado += `Después de retirar 2000€ de cuenta2 (más del saldo): ${cuenta2.toString()}\n`;

        cuenta1.retirar(-50);
        resultado += `Después de intentar retirar -50€: ${cuenta1.toString()}\n\n`;

        resultado += "=== Getters y Setters ===\n";
        resultado += `Titular cuenta1: ${cuenta1.titular}\n`;
        cuenta1.titular = "Ana García Martínez";
        resultado += `Nuevo titular: ${cuenta1.titular}\n`;

        cuenta1.cantidad = 750.25;
        resultado += `Nueva cantidad: ${cuenta1.cantidad}€\n`;

        cuenta1.cantidad = -100;
        resultado += `Intentar asignar cantidad negativa (-100): ${cuenta1.cantidad}€\n\n`;

        // Prueba solicitada: secuencia de operaciones mostrando saldo tras cada operación
        resultado += "=== Prueba solicitada ===\n";
        const cuentaPrueba = new Cuenta("Cuenta Prueba", 100);
        resultado += `Cuenta creada (100€): ${cuentaPrueba.toString()}\n`;

        cuentaPrueba.ingresar(10);
        resultado += `Después de ingresar 10€: ${cuentaPrueba.toString()}\n`;

        cuentaPrueba.retirar(50);
        resultado += `Después de retirar 50€: ${cuentaPrueba.toString()}\n`;

        cuentaPrueba.ingresar(15);
        resultado += `Después de ingresar 15€: ${cuentaPrueba.toString()}\n`;

        cuentaPrueba.retirar(100);
        resultado += `Después de retirar 100€: ${cuentaPrueba.toString()}\n\n`;

    } catch (error) {
        resultado += `Error: ${error.message}\n`;
    }

    return resultado;
}

const texto = ejercicio0603();

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
    let salida = document.getElementById('resultado0603');
    if (!salida) {
        salida = document.createElement('div');
        salida.id = 'resultado0603';
        salida.style.whiteSpace = 'pre-wrap';
        salida.style.padding = '8px';
        salida.style.border = '1px solid #ccc';
        salida.style.background = '#f8f8f8';
        document.body.appendChild(salida);
    }
    salida.innerText = texto;
    salida.style.display = 'block';
} else if (typeof process !== 'undefined' && process.stdout) {
    console.log(texto);
}

export { Cuenta };
