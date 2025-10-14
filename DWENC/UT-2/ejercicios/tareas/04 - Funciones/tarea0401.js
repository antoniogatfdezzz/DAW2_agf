function factorial(x) {
            if (x <= 1) {
                return 1;
            }
            return x * factorial(x - 1);
        }

        function ejercicio0401() {
            let resultado = "=== FACTORIAL RECURSIVO ===\n\n";
            
            // Pruebas del factorial
            let numeros = [0, 1, 5, 7, 10];
            numeros.forEach(num => {
                resultado += `${num}! = ${factorial(num)}\n`;
            });
            
            // Prueba interactiva
            let num = parseInt(prompt("Introduce un número para calcular su factorial:"));
            if (!isNaN(num) && num >= 0) {
                resultado += `\n${num}! = ${factorial(num)}`;
            }
            
            document.getElementById("resultado0401").innerHTML = resultado;
            document.getElementById("resultado0401").style.display = "block";
        }