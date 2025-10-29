"use strict";

function ejercicio0102() {
            let cadena = prompt("Introduce una cadena de texto:");
            if (cadena !== null) {
                let resultado = "";
                for (let i = 0; i < cadena.length; i++) {
                    resultado += cadena[i];
                    if (i < cadena.length - 1) {
                        resultado += "-";
                    }
                }
                document.getElementById("resultado0102").innerHTML = `<strong>Texto original:</strong> ${cadena}<br><strong>Con guiones:</strong> ${resultado}`;
                document.getElementById("resultado0102").style.display = "block";
            }
        }