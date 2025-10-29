"use strict";

function ejercicio0201() {
            let edad = parseInt(prompt("Introduce tu edad:"));
            let resultado;
            if (isNaN(edad)) {
                resultado = "Por favor, introduce una edad válida.";
            } else if (edad >= 18) {
                resultado = `Tienes ${edad} años. Ya puedes conducir.`;
            } else {
                resultado = `Tienes ${edad} años. Aún no puedes conducir.`;
            }
            document.getElementById("resultado0201").innerHTML = resultado;
            document.getElementById("resultado0201").style.display = "block";
        }