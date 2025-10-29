"use strict";

function ejercicio0220() {
            let numeros = [];
            let numero;
            
            do {
                numero = parseInt(prompt("Introduce un número (0 para terminar):"));
                if (!isNaN(numero)) {
                    numeros.push(numero);
                }
            } while (numero !== 0);

            document.getElementById("resultado0220").innerHTML = `Números introducidos: ${numeros.join(", ")}`;
            document.getElementById("resultado0220").style.display = "block";
        }