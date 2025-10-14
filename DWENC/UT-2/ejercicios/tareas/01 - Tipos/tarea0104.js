function ejercicio0104() {
            let cadena = prompt("Introduce una cadena para invertir:");
            if (cadena !== null) {
                let resultado = "";
                for (let i = cadena.length - 1; i >= 0; i--) {
                    resultado += cadena[i];
                }
                document.getElementById("resultado0104").innerHTML = `<strong>Texto original:</strong> ${cadena}<br><strong>Texto invertido:</strong> ${resultado}`;
                document.getElementById("resultado0104").style.display = "block";
            }
        }