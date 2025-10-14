"use strict";

const prompt = require('prompt-sync')();

const numerosAleatorios = [];
let i;

for (i = 0; i < 10; i++) {
    const numeroAleatorio = Math.floor(Math.random() * 21);
    numerosAleatorios[i] = numeroAleatorio;
}

console.log("¡Bienvenido al juego de números aleatorios!");
console.log("He generado 10 números aleatorios del 0 al 20.");
console.log("Ahora necesito que introduzcas 5 números:");

const numerosUsuario = [];

for (i = 0; i < 5; i++) {
    const numeroUsuario = Number(prompt("Introduce el número " + (i + 1) + " (del 0 al 20):"));
    numerosUsuario[i] = numeroUsuario;
}


const aciertos = [];
let numeroAciertos = 0;
let j;


for (i = 0; i < 5; i++) {
    for (j = 0; j < 10; j++) {
        if (numerosUsuario[i] == numerosAleatorios[j]) {
            aciertos[numeroAciertos] = numerosUsuario[i];
            numeroAciertos = numeroAciertos + 1;
        }
    }
}

let textoAleatorios = "Números generados: ";
for (i = 0; i < 10; i++) {
    textoAleatorios = textoAleatorios + numerosAleatorios[i];
    if (i < 9) {
        textoAleatorios = textoAleatorios + ", ";
    }
}

let textoUsuario = "Tus números: ";
for (i = 0; i < 5; i++) {
    textoUsuario = textoUsuario + numerosUsuario[i];
    if (i < 4) {
        textoUsuario = textoUsuario + ", ";
    }
}

console.log(textoAleatorios);
console.log(textoUsuario);
console.log("Número de aciertos: " + numeroAciertos);

if (numeroAciertos > 0) {
    let textoAciertos = "Números acertados: ";
    for (i = 0; i < numeroAciertos; i++) {
        textoAciertos = textoAciertos + aciertos[i];
        if (i < numeroAciertos - 1) {
            textoAciertos = textoAciertos + ", ";
        }
    }
    console.log(textoAciertos);
} else {
    console.log("No has acertado ningún número.");
}
