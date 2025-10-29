"use strict";

function ejercicio0211() {
            let opciones = ["1", "X", "2"];
            let resultado = "";
            for (let i = 1; i <= 14; i++) {
                let opcion = opciones[Math.floor(Math.random() * 3)];
                resultado += `Resultado ${i}: ${opcion}\n`;
            }
            document.getElementById("resultado0211").innerHTML = resultado;
            document.getElementById("resultado0211").style.display = "block";
        }