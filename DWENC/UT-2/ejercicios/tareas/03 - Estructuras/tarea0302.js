"use strict";

function ejercicio0302() {
            let palabras = [];
            
            for (let i = 1; i <= 3; i++) {
                let palabra = prompt(`Introduce la palabra ${i}:`);
                if (palabra !== null && palabra !== "") {
                    palabras.push(palabra);
                } else {
                    alert("Por favor, introduce una palabra válida.");
                    i--;
                }
            }
            
            // Ordenar alfabéticamente
            let palabrasOrdenadas = [...palabras].sort();
            let palabrasInversas = [...palabrasOrdenadas].reverse();
            
            let resultado = `=== ORDENAR PALABRAS ===\n\n`;
            resultado += `Palabras originales: ${palabras.join(", ")}\n\n`;
            resultado += `Orden alfabético: ${palabrasOrdenadas.join(", ")}\n\n`;
            resultado += `Orden alfabético inverso: ${palabrasInversas.join(", ")}`;
            
            document.getElementById("resultado0302").innerHTML = resultado;
            document.getElementById("resultado0302").style.display = "block";
        }