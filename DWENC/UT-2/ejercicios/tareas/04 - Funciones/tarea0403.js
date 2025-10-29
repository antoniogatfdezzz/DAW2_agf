"use strict";

let pantalla0403 = 0;
let memoria0403 = 0;

        const sumar0403 = function(a, b) { return a + b; };
        const restar0403 = function(a, b) { return a - b; };
        const multiplicar0403 = function(a, b) { return a * b; };
        const dividir0403 = function(a, b) { 
            if (b === 0) throw new Error("División por cero");
            return a / b; 
        };
        const modulo0403 = function(a, b) { return a % b; };
        const factorialCalc0403 = function(a) { return factorial(a); };
        const potencia0403 = function(a, b) { return Math.pow(a, b); };
        const guardarMemoria0403 = function() { memoria0403 = pantalla0403; };
        const recuperarMemoria0403 = function() { pantalla0403 = memoria0403; };
        const limpiar0403 = function() { pantalla0403 = 0; memoria0403 = 0; };

        const operaciones0403 = new Map([
            ['+', sumar0403],
            ['-', restar0403],
            ['*', multiplicar0403],
            ['/', dividir0403],
            ['%', modulo0403],
            ['!', factorialCalc0403],
            ['^', potencia0403],
            ['M', guardarMemoria0403],
            ['R', recuperarMemoria0403],
            ['C', limpiar0403]
        ]);

        function menuPrincipal0403() {
            let resultado = "=== CALCULADORA II (Expresiones + Map) ===\n\n";
            resultado += "Programa de prueba:\n\n";
            
            pantalla0403 = 8;
            resultado += `Pantalla inicial: ${pantalla0403}\n`;
            
            pantalla0403 = operaciones0403.get('+')(pantalla0403, 7);
            resultado += `Sumar 7: ${pantalla0403}\n`;
            
            pantalla0403 = operaciones0403.get('*')(pantalla0403, 3);
            resultado += `Multiplicar por 3: ${pantalla0403}\n`;
            
            operaciones0403.get('M')();
            resultado += `Guardar en memoria: M = ${memoria0403}\n`;
            
            pantalla0403 = operaciones0403.get('!')(5);
            resultado += `Factorial de 5: ${pantalla0403}\n`;
            
            operaciones0403.get('R')();
            resultado += `Recuperar memoria: ${pantalla0403}\n`;
            
            pantalla0403 = operaciones0403.get('^')(2, 10);
            resultado += `2 elevado a 10: ${pantalla0403}\n`;
            
            operaciones0403.get('C')();
            resultado += `Limpiar: ${pantalla0403}\n`;
            
            return resultado;
        }

        function ejercicio0403() {
            let resultado = menuPrincipal0403();
            document.getElementById("resultado0403").innerHTML = resultado;
            document.getElementById("resultado0403").style.display = "block";
        }