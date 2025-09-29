function contadorUnico() {
            // Inicializar propiedades si no existen
            if (!contadorUnico.hasOwnProperty('actual')) {
                contadorUnico.actual = contadorUnico.inicial || 1;
            }
            
            let valorActual = contadorUnico.actual;
            
            // Incrementar para la próxima llamada
            contadorUnico.actual++;
            
            // Si se ha definido un máximo, comprobar si se ha alcanzado
            if (contadorUnico.maximo && valorActual >= contadorUnico.maximo) {
                contadorUnico.actual = contadorUnico.inicial || 1;
            }
            
            return valorActual;
        }

        function ejercicio0408() {
            let resultado = "=== CONTADOR ÚNICO ===\n\n";
            
            // Reiniciar contador
            delete contadorUnico.actual;
            contadorUnico.inicial = 1;
            delete contadorUnico.maximo;
            
            resultado += "=== Contador básico ===\n";
            for (let i = 0; i < 5; i++) {
                resultado += `contadorUnico() = ${contadorUnico()}\n`;
            }
            
            resultado += "\n=== Configurando inicial = 10, máximo = 13 ===\n";
            contadorUnico.inicial = 10;
            contadorUnico.maximo = 13;
            delete contadorUnico.actual; // Reiniciar
            
            for (let i = 0; i < 8; i++) {
                resultado += `contadorUnico() = ${contadorUnico()}\n`;
            }
            
            resultado += "\n=== Configurando inicial = 5, máximo = 8 ===\n";
            contadorUnico.inicial = 5;
            contadorUnico.maximo = 8;
            delete contadorUnico.actual; // Reiniciar
            
            for (let i = 0; i < 6; i++) {
                resultado += `contadorUnico() = ${contadorUnico()}\n`;
            }
            
            document.getElementById("resultado0408").innerHTML = resultado;
            document.getElementById("resultado0408").style.display = "block";
        }