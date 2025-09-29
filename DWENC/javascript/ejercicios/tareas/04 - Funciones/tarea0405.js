 let pantalla0405 = 0;
        let memoria0405 = 0;

        const sumar0405 = (a = pantalla0405, b = pantalla0405) => a + b;
        const restar0405 = (a = pantalla0405, b = pantalla0405) => a - b;
        const multiplicar0405 = (a = pantalla0405, b = pantalla0405) => a * b;
        const dividir0405 = (a = pantalla0405, b = pantalla0405) => {
            if (b === 0) throw new Error("División por cero");
            return a / b;
        };
        const modulo0405 = (a = pantalla0405, b = pantalla0405) => a % b;
        const factorialCalc0405 = (a = pantalla0405) => factorial(a);
        const potencia0405 = (a = pantalla0405, b = pantalla0405) => Math.pow(a, b);
        const guardarMemoria0405 = () => memoria0405 = pantalla0405;
        const recuperarMemoria0405 = () => pantalla0405 = memoria0405;
        const limpiar0405 = () => { pantalla0405 = 0; memoria0405 = 0; };

        const operaciones0405 = new Map([
            ['+', sumar0405],
            ['-', restar0405],
            ['*', multiplicar0405],
            ['/', dividir0405],
            ['%', modulo0405],
            ['!', factorialCalc0405],
            ['^', potencia0405],
            ['M', guardarMemoria0405],
            ['R', recuperarMemoria0405],
            ['C', limpiar0405]
        ]);

        function menuPrincipal0405() {
            let resultado = "=== CALCULADORA IV (Argumentos por Defecto) ===\n\n";
            resultado += "Programa de prueba:\n\n";
            
            pantalla0405 = 6;
            resultado += `Pantalla inicial: ${pantalla0405}\n`;
            
            // Usando argumentos por defecto (pantalla como ambos operandos)
            pantalla0405 = operaciones0405.get('+')();
            resultado += `Sumar pantalla + pantalla (6+6): ${pantalla0405}\n`;
            
            // Con un argumento (pantalla como primer operando)
            pantalla0405 = operaciones0405.get('*')(undefined, 2);
            resultado += `Multiplicar pantalla por 2: ${pantalla0405}\n`;
            
            operaciones0405.get('M')();
            resultado += `Guardar en memoria: M = ${memoria0405}\n`;
            
            pantalla0405 = 4;
            resultado += `Nueva pantalla: ${pantalla0405}\n`;
            
            // Factorial usando valor por defecto
            pantalla0405 = operaciones0405.get('!')();
            resultado += `Factorial de pantalla (4): ${pantalla0405}\n`;
            
            operaciones0405.get('R')();
            resultado += `Recuperar memoria: ${pantalla0405}\n`;
            
            operaciones0405.get('C')();
            resultado += `Limpiar: ${pantalla0405}\n`;
            
            return resultado;
        }

        function ejercicio0405() {
            let resultado = menuPrincipal0405();
            document.getElementById("resultado0405").innerHTML = resultado;
            document.getElementById("resultado0405").style.display = "block";
        }