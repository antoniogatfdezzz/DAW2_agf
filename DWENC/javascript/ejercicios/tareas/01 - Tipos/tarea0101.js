function ejercicio0101() {
            let texto = prompt("Introduce un texto:");
            if (texto !== null) {
                let resultado = texto.toUpperCase();
                document.getElementById("resultado0101").innerHTML = `<strong>Texto original:</strong> ${texto}<br><strong>En mayúsculas:</strong> ${resultado}`;
                document.getElementById("resultado0101").style.display = "block";
            }
        }