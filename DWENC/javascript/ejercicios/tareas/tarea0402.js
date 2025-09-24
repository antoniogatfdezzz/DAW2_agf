const prompt = require('prompt-sync')();

let pantalla = 0;
let memoria = 0;

function mostrarMenu() {
    console.log("\n=== CALCULADORA ===");
    console.log("Pantalla: " + pantalla);
    console.log("Memoria: " + memoria);
    console.log("\nOperaciones disponibles:");
    console.log("+ : Sumar");
    console.log("- : Restar");
    console.log("* : Multiplicar");
    console.log("/ : Dividir");
    console.log("% : Módulo");
    console.log("! : Factorial");
    console.log("^ : Potencia");
    console.log("M : Guardar en memoria");
    console.log("R : Recuperar de memoria");
    console.log("C : Limpiar pantalla y memoria");
    console.log("S : Salir");
    return prompt("Selecciona una operación: ");
}

function sumar() {
    const operando = parseFloat(prompt("Introduce el número a sumar: "));
    pantalla = pantalla + operando;
}

function restar() {
    const operando = parseFloat(prompt("Introduce el número a restar: "));
    pantalla = pantalla - operando;
}

function multiplicar() {
    const operando = parseFloat(prompt("Introduce el número a multiplicar: "));
    pantalla = pantalla * operando;
}

function dividir() {
    const operando = parseFloat(prompt("Introduce el número a dividir: "));
    if (operando !== 0) {
        pantalla = pantalla / operando;
    } else {
        console.log("Error: División por cero");
    }
}

function modulo() {
    const operando = parseFloat(prompt("Introduce el número para el módulo: "));
    if (operando !== 0) {
        pantalla = pantalla % operando;
    } else {
        console.log("Error: División por cero");
    }
}

function factorial() {
    if (pantalla >= 0 && Number.isInteger(pantalla)) {
        let resultado = 1;
        for (let i = 1; i <= pantalla; i++) {
            resultado *= i;
        }
        pantalla = resultado;
    } else {
        console.log("Error: Factorial solo para números enteros no negativos");
    }
}

function potencia() {
    const exponente = parseFloat(prompt("Introduce el exponente: "));
    pantalla = Math.pow(pantalla, exponente);
}

function guardarMemoria() {
    memoria = pantalla;
    console.log("Valor guardado en memoria: " + memoria);
}

function recuperarMemoria() {
    pantalla = memoria;
    console.log("Valor recuperado de memoria: " + pantalla);
}

function limpiar() {
    pantalla = 0;
    memoria = 0;
    console.log("Pantalla y memoria limpiadas");
}

function calculadora() {
    let continuar = true;
    
    while (continuar) {
        const opcion = mostrarMenu();
        
        switch (opcion.toUpperCase()) {
            case '+':
                sumar();
                break;
            case '-':
                restar();
                break;
            case '*':
                multiplicar();
                break;
            case '/':
                dividir();
                break;
            case '%':
                modulo();
                break;
            case '!':
                factorial();
                break;
            case '^':
                potencia();
                break;
            case 'M':
                guardarMemoria();
                break;
            case 'R':
                recuperarMemoria();
                break;
            case 'C':
                limpiar();
                break;
            case 'S':
                continuar = false;
                console.log("Saliendo de la calculadora...");
                break;
            default:
                console.log("Opción no válida");
        }
    }
}

function programaPrueba() {
    console.log("\n=== PROGRAMA DE PRUEBA ===");
    
    pantalla = 5;
    console.log("Pantalla inicial: " + pantalla);
    
    pantalla = pantalla + 3;
    console.log("Suma 3: " + pantalla);
    
    pantalla = pantalla - 2;
    console.log("Resta 2: " + pantalla);
    
    pantalla = pantalla * 4;
    console.log("Multiplica por 4: " + pantalla);
    
    pantalla = pantalla / 2;
    console.log("Divide entre 2: " + pantalla);
    
    pantalla = pantalla % 3;
    console.log("Módulo 3: " + pantalla);
    
    memoria = pantalla;
    console.log("Guarda en memoria: " + memoria);
    
    pantalla = 5;
    let resultado = 1;
    for (let i = 1; i <= pantalla; i++) {
        resultado *= i;
    }
    pantalla = resultado;
    console.log("Factorial de 5: " + pantalla);
    
    pantalla = Math.pow(2, 3);
    console.log("2 elevado a 3: " + pantalla);
    
    pantalla = memoria;
    console.log("Recupera de memoria: " + pantalla);
    
    pantalla = 0;
    memoria = 0;
    console.log("Limpia pantalla y memoria: pantalla=" + pantalla + ", memoria=" + memoria);
    
    console.log("=== FIN PROGRAMA DE PRUEBA ===\n");
}

programaPrueba();
calculadora();
