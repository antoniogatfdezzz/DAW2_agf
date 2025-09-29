function ejercicio0204() {
            let numero = prompt("Introduce el número del DNI (sin letra):");
            if (numero !== null && !isNaN(numero) && numero.length === 8) {
                let letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                let letra = letras[numero % 23];
                document.getElementById("resultado0204").innerHTML = `DNI completo: ${numero}${letra}`;
            } else {
                document.getElementById("resultado0204").innerHTML = "Número de DNI no válido";
            }
            document.getElementById("resultado0204").style.display = "block";
        }