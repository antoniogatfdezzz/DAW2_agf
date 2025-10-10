"use strict";

const prompt = require('prompt-sync')();

const mapaElementos = new Map();
let elemento;

console.log("Introduce elementos (presiona Enter para terminar):");

while (true) {
    elemento = prompt("Introduce un elemento: ");
    
    if (elemento === "") {
        break;
    }
    
    const elementoNormalizado = elemento.toLowerCase();
    
    if (mapaElementos.has(elementoNormalizado)) {
        const conteoActual = mapaElementos.get(elementoNormalizado);
        mapaElementos.set(elementoNormalizado, conteoActual + 1);
        console.log(`El elemento '${elemento}' ya existe. Apariciones: ${conteoActual + 1}`);
    } else {
        mapaElementos.set(elementoNormalizado, 1);
        console.log(`Elemento '${elemento}' añadido`);
    }
}

const elementosOrdenados = Array.from(mapaElementos.keys()).sort();

console.log("\n=== LISTA FINAL ===");
console.log("Elementos únicos ordenados:");
for (const elemento of elementosOrdenados) {
    const contador = mapaElementos.get(elemento);
    console.log(`${elemento} (apariciones: ${contador})`);
}

console.log(`\nTotal de elementos únicos: ${mapaElementos.size}`);

let totalApariciones = 0;
for (const contador of mapaElementos.values()) {
    totalApariciones += contador;
}
console.log(`Total de elementos introducidos: ${totalApariciones}`);
