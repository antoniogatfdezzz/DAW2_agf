"use strict";

function ejercicio0221() {
            let opciones = ["piedra", "papel", "tijera"];
            let jugador = prompt("Elige: piedra, papel o tijera").toLowerCase();
            
            if (!opciones.includes(jugador)) {
                document.getElementById("resultado0221").innerHTML = "Opción no válida";
                document.getElementById("resultado0221").style.display = "block";
                return;
            }

            let pc = opciones[Math.floor(Math.random() * 3)];
            let resultado = `Jugador: ${jugador}\nPC: ${pc}\n\n`;

            if (jugador === pc) {
                resultado += "¡Empate!";
            } else if (
                (jugador === "piedra" && pc === "tijera") ||
                (jugador === "papel" && pc === "piedra") ||
                (jugador === "tijera" && pc === "papel")
            ) {
                resultado += "¡Has ganado!";
            } else {
                resultado += "Has perdido";
            }

            document.getElementById("resultado0221").innerHTML = resultado;
            document.getElementById("resultado0221").style.display = "block";
        }