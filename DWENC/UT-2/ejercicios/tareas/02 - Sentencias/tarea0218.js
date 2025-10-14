function ejercicio0218() {
            let tipo = parseInt(prompt("Introduce el tipo de motor (0, 1, 2, 3, 4):"));
            let mensaje;

            if (tipo === 0) {
                mensaje = "No hay establecido un valor definido para el tipo de bomba";
            } else if (tipo === 1) {
                mensaje = "La bomba es una bomba de agua";
            } else if (tipo === 2) {
                mensaje = "La bomba es una bomba de gasolina";
            } else if (tipo === 3) {
                mensaje = "La bomba es una bomba de hormigón";
            } else if (tipo === 4) {
                mensaje = "La bomba es una bomba de pasta alimenticia";
            } else {
                mensaje = "No existe un valor válido para tipo de bomba";
            }

            document.getElementById("resultado0218").innerHTML = mensaje;
            document.getElementById("resultado0218").style.display = "block";
        }