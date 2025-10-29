"use strict";

function ejercicio0203() {
            let cadenas = [];
            let cadena;
            do {
                cadena = prompt("Introduce una cadena (Cancelar para terminar):");
                if (cadena !== null) {
                    cadenas.push(cadena);
                }
            } while (cadena !== null);
            
            let resultado = cadenas.join("-");
            document.getElementById("resultado0203").innerHTML = `Cadenas concatenadas: ${resultado}`;
            document.getElementById("resultado0203").style.display = "block";
        }