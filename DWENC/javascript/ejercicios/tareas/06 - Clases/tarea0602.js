let pantalla = 0;
let memoria = 0;

function mostrarMenu() {
    console.log("\n=== CALCULADORA ===");
    console.log("Pantalla: " + pantalla);
    console.log("Memoria: " + memoria);
    console.log("\nOperaciones disponibles:");
    console.log("1. Suma (+)");
    console.log("2. Resta (-)");
    console.log("3. Multiplicación (*)");
    console.log("4. División (/)");
    console.log("5. Módulo (%)");
    console.log("6. Factorial (!)");
    console.log("7. Potencia (^)");
    console.log("8. Raíz cuadrada (√)");
    console.log("9. Cambiar signo (+/-)");
    console.log("M. Guardar en memoria");
    console.log("R. Recuperar memoria");
    console.log("C. Limpiar pantalla y memoria");
    console.log("S. Salir");
    console.log("==================");
}

function sumar(operando) {
    pantalla = pantalla + operando;
    console.log(`Resultado: ${pantalla}`);
}

function restar(operando) {
    pantalla = pantalla - operando;
    console.log(`Resultado: ${pantalla}`);
}

function multiplicar(operando) {
    pantalla = pantalla * operando;
    console.log(`Resultado: ${pantalla}`);
}

function dividir(operando) {
    if (operando === 0) {
        console.log("Error: División por cero");
        return;
    }
    pantalla = pantalla / operando;
    console.log(`Resultado: ${pantalla}`);
}

function modulo(operando) {
    if (operando === 0) {
        console.log("Error: Módulo por cero");
        return;
    }
    pantalla = pantalla % operando;
    console.log(`Resultado: ${pantalla}`);
}

function potencia(exponente) {
    pantalla = Math.pow(pantalla, exponente);
    console.log(`Resultado: ${pantalla}`);
}

function factorial() {
    if (pantalla < 0) {
        console.log("Error: Factorial de número negativo");
        return;
    }
    if (pantalla !== Math.floor(pantalla)) {
        console.log("Error: Factorial solo para números enteros");
        return;
    }
    
    let resultado = 1;
    for (let i = 2; i <= pantalla; i++) {
        resultado *= i;
    }
    pantalla = resultado;
    console.log(`Resultado: ${pantalla}`);
}

function raizCuadrada() {
    if (pantalla < 0) {
        console.log("Error: Raíz cuadrada de número negativo");
        return;
    }
    pantalla = Math.sqrt(pantalla);
    console.log(`Resultado: ${pantalla}`);
}

function cambiarSigno() {
    pantalla = -pantalla;
    console.log(`Resultado: ${pantalla}`);
}

function guardarEnMemoria() {
    memoria = pantalla;
    console.log(`Valor ${pantalla} guardado en memoria`);
}

function recuperarMemoria() {
    pantalla = memoria;
    console.log(`Valor ${memoria} recuperado de memoria`);
}

function limpiar() {
    pantalla = 0;
    memoria = 0;
    console.log("Pantalla y memoria limpiadas");
}

function solicitarNumero(mensaje) {
    const readline = require('readline');
    const rl = readline.createInterface({
        input: process.stdin,
        output: process.stdout
    });
    
    return new Promise((resolve) => {
        rl.question(mensaje, (respuesta) => {
            rl.close();
            const numero = parseFloat(respuesta);
            if (isNaN(numero)) {
                console.log("Error: Entrada no válida");
                resolve(null);
            } else {
                resolve(numero);
            }
        });
    });
}

function solicitarOpcion() {
    const readline = require('readline');
    const rl = readline.createInterface({
        input: process.stdin,
        output: process.stdout
    });
    
    return new Promise((resolve) => {
        rl.question("Seleccione una opción: ", (respuesta) => {
            rl.close();
            resolve(respuesta.toLowerCase());
        });
    });
}

async function calculadora() {
    let continuar = true;
    
    console.log("¡Bienvenido a la Calculadora!");
    
    while (continuar) {
        mostrarMenu();
        const opcion = await solicitarOpcion();
        
        switch (opcion) {
            case '1':
            case '+':
                const sumando = await solicitarNumero("Ingrese el número a sumar: ");
                if (sumando !== null) sumar(sumando);
                break;
                
            case '2':
            case '-':
                const sustraendo = await solicitarNumero("Ingrese el número a restar: ");
                if (sustraendo !== null) restar(sustraendo);
                break;
                
            case '3':
            case '*':
                const multiplicando = await solicitarNumero("Ingrese el número a multiplicar: ");
                if (multiplicando !== null) multiplicar(multiplicando);
                break;
                
            case '4':
            case '/':
                const divisor = await solicitarNumero("Ingrese el número por el que dividir: ");
                if (divisor !== null) dividir(divisor);
                break;
                
            case '5':
            case '%':
                const modDivisor = await solicitarNumero("Ingrese el número para el módulo: ");
                if (modDivisor !== null) modulo(modDivisor);
                break;
                
            case '6':
            case '!':
                factorial();
                break;
                
            case '7':
            case '^':
                const exponente = await solicitarNumero("Ingrese el exponente: ");
                if (exponente !== null) potencia(exponente);
                break;
                
            case '8':
            case '√':
                raizCuadrada();
                break;
                
            case '9':
            case '+/-':
                cambiarSigno();
                break;
                
            case 'm':
                guardarEnMemoria();
                break;
                
            case 'r':
                recuperarMemoria();
                break;
                
            case 'c':
                limpiar();
                break;
                
            case 's':
                console.log("¡Hasta luego!");
                continuar = false;
                break;
                
            default:
                console.log("Opción no válida");
        }
        
        if (continuar) {
            await new Promise(resolve => {
                const readline = require('readline');
                const rl = readline.createInterface({
                    input: process.stdin,
                    output: process.stdout
                });
                rl.question("\nPresione Enter para continuar...", () => {
                    rl.close();
                    resolve();
                });
            });
        }
    }
}

async function programaPrueba() {
    console.log("\n=== PROGRAMA DE PRUEBA ===");
    console.log("Ejecutando todas las operaciones de la calculadora...\n");
    
    pantalla = 0;
    memoria = 0;
    
    console.log("1. Suma: 0 + 5");
    sumar(5);
    
    console.log("\n2. Resta: 5 - 2");
    restar(2);
    
    console.log("\n3. Multiplicación: 3 * 4");
    multiplicar(4);
    
    console.log("\n4. División: 12 / 3");
    dividir(3);
    
    console.log("\n5. Módulo: 4 % 3");
    modulo(3);
    
    console.log("\n6. Guardando 1 en memoria");
    guardarEnMemoria();
    
    console.log("\n7. Factorial de 1");
    factorial();
    
    console.log("\n8. Potencia: 1^3");
    potencia(3);
    
    console.log("\n9. Raíz cuadrada de 1");
    raizCuadrada();
    
    console.log("\n10. Cambiar signo de 1");
    cambiarSigno();
    
    console.log("\n11. Recuperando memoria");
    recuperarMemoria();
    
    console.log("\n12. Limpiando pantalla y memoria");
    limpiar();
    
    console.log("\n=== FIN DEL PROGRAMA DE PRUEBA ===");
}

async function main() {
    const readline = require('readline');
    const rl = readline.createInterface({
        input: process.stdin,
        output: process.stdout
    });
    
    console.log("=== CALCULADORA JAVASCRIPT ===");
    console.log("1. Modo interactivo");
    console.log("2. Programa de prueba");
    
    rl.question("Seleccione una opción (1 o 2): ", async (respuesta) => {
        rl.close();
        
        if (respuesta === '1') {
            await calculadora();
        } else if (respuesta === '2') {
            await programaPrueba();
        } else {
            console.log("Opción no válida");
        }
        
        process.exit(0);
    });
}

if (require.main === module) {
    main();
}

module.exports = {
    mostrarMenu,
    sumar,
    restar,
    multiplicar,
    dividir,
    modulo,
    factorial,
    potencia,
    raizCuadrada,
    cambiarSigno,
    guardarEnMemoria,
    recuperarMemoria,
    limpiar,
    calculadora,
    programaPrueba
};
