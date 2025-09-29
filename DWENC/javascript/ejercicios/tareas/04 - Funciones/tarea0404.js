let pantalla0404 = 0;
        let memoria0404 = 0;

        const sumar0404 = (a, b) => a + b;
        const restar0404 = (a, b) => a - b;
        const multiplicar0404 = (a, b) => a * b;
        const dividir0404 = (a, b) => {
            if (b === 0) throw new Error("División por cero");
            return a / b;
        };
        const modulo0404 = (a, b) => a % b;
        const factorialCalc0404 = (a) => factorial(a);
        const potencia0404 = (a, b) => Math.pow(a, b);
        const guardarMemoria0404 = () => memoria0404 = pantalla0404;
        const recuperarMemoria0404 = () => pantalla0404 = memoria0404;
        const limpiar0404 = () => { pantalla0404 = 0; memoria0404 = 0; };

        const operaciones0404 = new Map([
            ['+', sumar0404],
            ['-', restar0404],
            ['*', multiplicar0404],
            ['/', dividir0404],
            ['%', modulo0404],
            ['!', factorialCalc0404],
            ['^', potencia0404],
            ['M', guardarMemoria0404],
            ['R', recuperarMemoria0404],
            ['C', limpiar0404]
        ]);

        function menuPrincipal0404() {
            let resultado = "=== CALCULADORA III (Funciones Flecha) ===\n\n";
            resultado += "Programa de prueba:\n\n";
            
            pantalla0404 = 12;
            resultado += `Pantalla inicial: ${pantalla0404}\n`;
            
            pantalla0404 = operaciones0404.get('-')(pantalla0404, 4);
            resultado += `Restar 4: ${pantalla0404}\n`;
            
            pantalla0404 = operaciones0404.get('^')(pantalla0404, 2);
            resultado += `Elevar al cuadrado: ${pantalla0404}\n`;
            
            operaciones0404.get('M')();
            resultado += `Guardar en memoria: M = ${memoria0404}\n`;
            
            pantalla0404 = operaciones0404.get('%')(pantalla0404, 10);
            resultado += `Módulo 10: ${pantalla0404}\n`;
            
            operaciones0404.get('R')();
            resultado += `Recuperar memoria: ${pantalla0404}\n`;
            
            pantalla0404 = operaciones0404.get('/')(pantalla0404, 8);
            resultado += `Dividir por 8: ${pantalla0404}\n`;
            
            operaciones0404.get('C')();
            resultado += `Limpiar: ${pantalla0404}\n`;
            
            return resultado;
        }

        function ejercicio0404() {
            let resultado = menuPrincipal0404();
            document.getElementById("resultado0404").innerHTML = resultado;
            document.getElementById("resultado0404").style.display = "block";
        }