"use strict";

function ejercicio0205() {
            let resultado = "";
            for (let i = 1; i <= 30; i++) {
                for (let j = 0; j < i; j++) {
                    resultado += i;
                }
                resultado += "\n";
            }
            document.getElementById("resultado0205").innerHTML = resultado;
            document.getElementById("resultado0205").style.display = "block";
        }