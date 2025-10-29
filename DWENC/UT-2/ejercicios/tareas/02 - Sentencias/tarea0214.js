"use strict";

function ejercicio0214() {
            let numero = parseInt(prompt("Introduce un número mayor que cero:"));
            if (isNaN(numero) || numero <= 0) {
                document.getElementById("resultado0214").innerHTML = "Error: El número debe ser mayor que cero";
            } else {
                let resultado = "";
                for (let i = 0; i <= numero; i++) {
                    resultado += i + " ";
                }
                document.getElementById("resultado0214").innerHTML = resultado;
            }
            document.getElementById("resultado0214").style.display = "block";
        }