"use strict";

function ejercicio0208() {
            let nombre1 = prompt("Introduce el primer nombre:");
            let edad1 = parseInt(prompt("Introduce la primera edad:"));
            let nombre2 = prompt("Introduce el segundo nombre:");
            let edad2 = parseInt(prompt("Introduce la segunda edad:"));
            let nombre3 = prompt("Introduce el tercer nombre:");
            let edad3 = parseInt(prompt("Introduce la tercera edad:"));

            let nombreMayor, edadMayor;
            if (edad1 >= edad2 && edad1 >= edad3) {
                nombreMayor = nombre1;
                edadMayor = edad1;
            } else if (edad2 >= edad1 && edad2 >= edad3) {
                nombreMayor = nombre2;
                edadMayor = edad2;
            } else {
                nombreMayor = nombre3;
                edadMayor = edad3;
            }

            document.getElementById("resultado0208").innerHTML = `El mayor es ${nombreMayor} con ${edadMayor} años`;
            document.getElementById("resultado0208").style.display = "block";
        }