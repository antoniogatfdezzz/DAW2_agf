"use strict";

function sumaNumeros0407(...numeros) {
            const suma = numeros.reduce((total, num) => total + num, 0);
            sumaNumeros0407.acumulado = (sumaNumeros0407.acumulado || 0) + suma;
            return suma;
        }

        function multiplicaNumeros0407(...numeros) {
            const producto = numeros.reduce((total, num) => total * num, 1);
            multiplicaNumeros0407.acumulado = (multiplicaNumeros0407.acumulado || 0) + producto;
            return producto;
        }

        function minimo0407(...numeros) {
            const min = Math.min(...numeros);
            minimo0407.acumulado = Math.min(minimo0407.acumulado || Infinity, min);
            return min;
        }

        function maximo0407(...numeros) {
            const max = Math.max(...numeros);
            maximo0407.acumulado = Math.max(maximo0407.acumulado || -Infinity, max);
            return max;
        }

        function ejercicio0407() {
            let resultado = "=== PROPIEDADES DE UNA FUNCIÓN ===\n\n";
            
            // Reiniciar acumulados
            delete sumaNumeros0407.acumulado;
            delete multiplicaNumeros0407.acumulado;
            delete minimo0407.acumulado;
            delete maximo0407.acumulado;
            
            resultado += "=== Primera llamada ===\n";
            resultado += `sumaNumeros(1,2,3) = ${sumaNumeros0407(1,2,3)} | Acumulado: ${sumaNumeros0407.acumulado}\n`;
            resultado += `multiplicaNumeros(2,3) = ${multiplicaNumeros0407(2,3)} | Acumulado: ${multiplicaNumeros0407.acumulado}\n`;
            resultado += `minimo(10,5,8) = ${minimo0407(10,5,8)} | Acumulado: ${minimo0407.acumulado}\n`;
            resultado += `maximo(10,5,8) = ${maximo0407(10,5,8)} | Acumulado: ${maximo0407.acumulado}\n\n`;
            
            resultado += "=== Segunda llamada ===\n";
            resultado += `sumaNumeros(4,5) = ${sumaNumeros0407(4,5)} | Acumulado: ${sumaNumeros0407.acumulado}\n`;
            resultado += `multiplicaNumeros(4,5) = ${multiplicaNumeros0407(4,5)} | Acumulado: ${multiplicaNumeros0407.acumulado}\n`;
            resultado += `minimo(3,12) = ${minimo0407(3,12)} | Acumulado: ${minimo0407.acumulado}\n`;
            resultado += `maximo(15,7) = ${maximo0407(15,7)} | Acumulado: ${maximo0407.acumulado}\n\n`;
            
            resultado += "=== Tercera llamada ===\n";
            resultado += `sumaNumeros(10) = ${sumaNumeros0407(10)} | Acumulado: ${sumaNumeros0407.acumulado}\n`;
            resultado += `multiplicaNumeros(3) = ${multiplicaNumeros0407(3)} | Acumulado: ${multiplicaNumeros0407.acumulado}\n`;
            resultado += `minimo(1,20) = ${minimo0407(1,20)} | Acumulado: ${minimo0407.acumulado}\n`;
            resultado += `maximo(25,2) = ${maximo0407(25,2)} | Acumulado: ${maximo0407.acumulado}\n`;
            
            document.getElementById("resultado0407").innerHTML = resultado;
            document.getElementById("resultado0407").style.display = "block";
        }