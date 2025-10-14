function ejercicio0207() {
            let numero = parseInt(prompt("Introduce un número (máximo 50):"));
            if (isNaN(numero) || numero <= 0 || numero > 50) {
                document.getElementById("resultado0207").innerHTML = "Número no válido";
                document.getElementById("resultado0207").style.display = "block";
                return;
            }
            let resultado = "";
            for (let i = 1; i <= numero; i++) {
                for (let j = 1; j <= i; j++) {
                    resultado += j;
                }
                resultado += "\n";
            }
            document.getElementById("resultado0207").innerHTML = resultado;
            document.getElementById("resultado0207").style.display = "block";
        }