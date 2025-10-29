"use strict";

function ejercicio0301() {
            let numeros = [];
            let entrada;
            
            // Pedir números hasta cadena vacía
            while (true) {
                entrada = prompt("Introduce un número (cadena vacía para terminar):");
                
                // Si es cadena vacía, terminar
                if (entrada === "" || entrada === null) {
                    break;
                }
                
                // Convertir a número y añadir al array
                let numero = parseFloat(entrada);
                if (!isNaN(numero)) {
                    numeros.push(numero);
                } else {
                    alert("Por favor, introduce un número válido.");
                }
            }
            
            // Calcular suma
            let suma = numeros.reduce((total, num) => total + num, 0);
            
            // Mostrar resultados
            let resultado = `=== RESULTADOS ===\n\n`;
            resultado += `Longitud del array: ${numeros.length}\n\n`;
            resultado += `Elementos en orden de introducción:\n${numeros.join(", ")}\n\n`;
            resultado += `Elementos en orden inverso:\n${[...numeros].reverse().join(", ")}\n\n`;
            resultado += `Suma de todos los elementos: ${suma}`;
            
            document.getElementById("resultado0301").innerHTML = resultado;
            document.getElementById("resultado0301").style.display = "block";
        }