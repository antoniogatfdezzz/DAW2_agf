const prompt = require('prompt-sync')();

let lista = [];
let elemento;

while (true) {
    elemento = prompt("Introduce un elemento: ");
    
    if (elemento === "") {
        break;
    }
    
    let existe = false;
    for (let i = 0; i < lista.length; i++) {
        if (lista[i].toLowerCase() === elemento.toLowerCase()) {
            existe = true;
            break;
        }
    }
    
    if (existe) {
        console.log("El elemento ya existe");
    } else {
        lista.push(elemento);
        console.log("Elemento añadido");
    }
}

lista.sort();

console.log("Lista final:");
for (let i = 0; i < lista.length; i++) {
    console.log(lista[i]);
}
