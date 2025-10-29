"use strict";

function ejercicio0213() {
            let texto = prompt("Introduce un texto:");
            if (texto !== null) {
                let vocales = "aeiouAEIOU";
                let posicion = -1;
                for (let i = 0; i < texto.length; i++) {
                    if (vocales.includes(texto[i])) {
                        posicion = i;
                        break;
                    }
                }
                let resultado = posicion !== -1 ? 
                    `Primera vocal '${texto[posicion]}' encontrada en posición ${posicion}` :
                    "No se encontraron vocales";
                document.getElementById("resultado0213").innerHTML = resultado;
            }
            document.getElementById("resultado0213").style.display = "block";
        }