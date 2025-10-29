"use strict";

function ejercicio0209() {
            let numeroAleatorio = Math.floor(Math.random() * 99) + 1;
            document.getElementById("resultado0209").innerHTML = `Número aleatorio generado: ${numeroAleatorio}`;
            document.getElementById("resultado0209").style.display = "block";
        }