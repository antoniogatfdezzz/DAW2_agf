"use strict";

function ejercicio0202() {
            let nota = parseFloat(prompt("Introduce una nota (0-10):"));
            let calificacion;
            if (isNaN(nota)) {
                calificacion = "Por favor, introduce una nota válida.";
            } else if (nota >= 9) {
                calificacion = "Sobresaliente";
            } else if (nota >= 7) {
                calificacion = "Notable";
            } else if (nota >= 6) {
                calificacion = "Bien";
            } else if (nota >= 5) {
                calificacion = "Suficiente";
            } else {
                calificacion = "Insuficiente";
            }
            document.getElementById("resultado0202").innerHTML = `Nota: ${nota} - ${calificacion}`;
            document.getElementById("resultado0202").style.display = "block";
        }