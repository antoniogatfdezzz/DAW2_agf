
const prompt = require('prompt-sync')();
const valMax = 200;
const numeroRandom = Math.floor(Math.random() * (valMax + 1));
    
let intentosRestantes = 3;
let adivinado = false;

console.info(`Adivina un número entre 0 y ${valMax}. Tienes ${intentosRestantes} intentos.`);

while (intentosRestantes > 0 && !adivinado) {
    
    let numero = prompt(`Intento ${intentosMax - intentosRestantes + 1}: Introduce un número entre 0 y ${valMax}`);

    if (isNaN(numero)) {
        console.error("Por favor, introduce un número válido.");
    }

    intentosRestantes--;

    if (numero === numeroRandom) {
        console.log("¡Correcto! Has adivinado el número.");
        adivinado = true;
    } else if (numero < numeroRandom) {
        console.info("El número secreto es mayor.");
    } else {
        console.info("El número secreto es menor.");
    }
}

if (!adivinado) {
    console.error(`Ya no tienes más intentos. El número era ${numeroRandom}.`);
}
