function sumaNumeros(...numeros) {
            return numeros.reduce((suma, num) => suma + num, 0);
        }

        function multiplicaNumeros(...numeros) {
            return numeros.reduce((producto, num) => producto * num, 1);
        }

        function minimo(...numeros) {
            return Math.min(...numeros);
        }

        function maximo(...numeros) {
            return Math.max(...numeros);
        }

        function ejercicio0406() {
            let resultado = "=== NÚMERO DE ARGUMENTOS VARIABLE ===\n\n";
            
            // Pruebas pasando argumentos directamente
            resultado += "=== Pasando argumentos directamente ===\n";
            resultado += `sumaNumeros(1,2,3,4,5) = ${sumaNumeros(1,2,3,4,5)}\n`;
            resultado += `multiplicaNumeros(2,3,4) = ${multiplicaNumeros(2,3,4)}\n`;
            resultado += `minimo(10,5,15,3,8) = ${minimo(10,5,15,3,8)}\n`;
            resultado += `maximo(10,5,15,3,8) = ${maximo(10,5,15,3,8)}\n\n`;
            
            // Pruebas pasando arrays con spread
            let numeros1 = [7, 14, 21, 28];
            let numeros2 = [2, 4, 6];
            let numeros3 = [100, 50, 75, 25, 90];
            
            resultado += "=== Pasando arrays con spread ===\n";
            resultado += `sumaNumeros(...[7,14,21,28]) = ${sumaNumeros(...numeros1)}\n`;
            resultado += `multiplicaNumeros(...[2,4,6]) = ${multiplicaNumeros(...numeros2)}\n`;
            resultado += `minimo(...[100,50,75,25,90]) = ${minimo(...numeros3)}\n`;
            resultado += `maximo(...[100,50,75,25,90]) = ${maximo(...numeros3)}\n`;
            
            document.getElementById("resultado0406").innerHTML = resultado;
            document.getElementById("resultado0406").style.display = "block";
        }
