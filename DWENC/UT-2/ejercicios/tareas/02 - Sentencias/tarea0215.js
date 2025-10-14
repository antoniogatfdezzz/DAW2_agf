function ejercicio0215() {
            let numero = parseInt(prompt("Introduce un número para calcular su factorial:"));
            if (isNaN(numero) || numero < 0) {
                document.getElementById("resultado0215").innerHTML = "Error: Introduce un número válido mayor o igual a 0";
            } else {
                let factorial = 1;
                for (let i = 1; i <= numero; i++) {
                    factorial *= i;
                }
                document.getElementById("resultado0215").innerHTML = `${numero}! = ${factorial}`;
            }
            document.getElementById("resultado0215").style.display = "block";
        }