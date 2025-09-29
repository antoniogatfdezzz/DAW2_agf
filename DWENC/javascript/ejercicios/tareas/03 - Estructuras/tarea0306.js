let equipo = {};

while (true) {
    let numero = prompt("Introduce el número del jugador:");
    if (numero === "" || numero === null) break;
    
    let nombre = prompt("Introduce el nombre del jugador:");
    if (nombre === "" || nombre === null) break;
    
    equipo[numero] = nombre;
}

while (true) {
    let numero = prompt("Introduce el número del jugador (0 para salir):");
    if (numero === "0" || numero === null) break;
    
    if (equipo[numero]) {
        console.log(`El jugador número ${numero} es: ${equipo[numero]}`);
    } else {
        console.log(`No hay ningún jugador con el número ${numero}`);
    }
}
