"use strict";

function ejercicio0206() {
            let numero = parseInt(prompt("Introduce un número:"));
            if (isNaN(numero) || numero <= 0) {
                document.getElementById("resultado0206").innerHTML = "Número no válido";
                document.getElementById("resultado0206").style.display = "block";
                return;
            }
            let resultado = "";
            for (let i = numero; i >= 1; i--) {
                for (let j = 0; j < i; j++) {
                    resultado += i;
                }
                resultado += "\n";
            }
            document.getElementById("resultado0206").innerHTML = resultado;
            document.getElementById("resultado0206").style.display = "block";
        }