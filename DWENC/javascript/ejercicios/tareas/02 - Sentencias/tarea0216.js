function ejercicio0216() {
            let numero = parseInt(prompt("Introduce un número para calcular su factorial:"));
            if (isNaN(numero) || numero < 0) {
                document.getElementById("resultado0216").innerHTML = "Error: Introduce un número válido mayor o igual a 0";
            } else {
                let factorial = 1;
                let i = 1;
                while (i <= numero) {
                    factorial *= i;
                    i++;
                }
                document.getElementById("resultado0216").innerHTML = `${numero}! = ${factorial}`;
            }
            document.getElementById("resultado0216").style.display = "block";
        }