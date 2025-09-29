function ejercicio0217() {
            const MAX_INTENTOS = 7;
            let numeroSecreto = Math.floor(Math.random() * 100) + 1;
            let intentos = 0;
            let adivinado = false;
            let resultado = `Juego: Adivina el número entre 1 y 100\nTienes ${MAX_INTENTOS} intentos\n\n`;

            while (intentos < MAX_INTENTOS && !adivinado) {
                let numero = parseInt(prompt(`Intento ${intentos + 1}/${MAX_INTENTOS}\nIntroduce un número entre 1 y 100:`));
                
                if (isNaN(numero)) {
                    alert("Por favor, introduce un número válido");
                    continue;
                }

                intentos++;
                
                if (numero === numeroSecreto) {
                    adivinado = true;
                    resultado += `¡Felicidades! Has adivinado el número ${numeroSecreto} en ${intentos} intentos.`;
                } else if (numero < numeroSecreto) {
                    resultado += `Intento ${intentos}: ${numero} - El número es mayor\n`;
                } else {
                    resultado += `Intento ${intentos}: ${numero} - El número es menor\n`;
                }
            }

            if (!adivinado) {
                resultado += `\nGame Over. El número era ${numeroSecreto}`;
            }

            document.getElementById("resultado0217").innerHTML = resultado;
            document.getElementById("resultado0217").style.display = "block";
        }