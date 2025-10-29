"use strict";

function ejercicio0219() {
            let tipo = parseInt(prompt("Introduce el tipo de motor (0, 1, 2, 3, 4):"));
            let mensaje;

            switch (tipo) {
                case 0:
                    mensaje = "No hay establecido un valor definido para el tipo de bomba";
                    break;
                case 1:
                    mensaje = "La bomba es una bomba de agua";
                    break;
                case 2:
                    mensaje = "La bomba es una bomba de gasolina";
                    break;
                case 3:
                    mensaje = "La bomba es una bomba de hormigón";
                    break;
                case 4:
                    mensaje = "La bomba es una bomba de pasta alimenticia";
                    break;
                default:
                    mensaje = "No existe un valor válido para tipo de bomba";
            }

            document.getElementById("resultado0219").innerHTML = mensaje;
            document.getElementById("resultado0219").style.display = "block";
        }