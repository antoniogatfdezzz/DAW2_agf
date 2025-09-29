const prompt = require('prompt-sync')();

var numerosAleatorios = [];
var i;

for (i = 0; i < 10; i++) {
    var numeroAleatorio = Math.floor(Math.random() * 21);
    numerosAleatorios[i] = numeroAleatorio;
}

console.log("¡Bienvenido al juego de números aleatorios!");
console.log("He generado 10 números aleatorios del 0 al 20.");


var numerosUsuario = [];

numerosUsuario[0] = parseInt(prompt("Introduce el primer número (del 0 al 20):"));
numerosUsuario[1] = parseInt(prompt("Introduce el segundo número (del 0 al 20):"));
numerosUsuario[2] = parseInt(prompt("Introduce el tercer número (del 0 al 20):"));
numerosUsuario[3] = parseInt(prompt("Introduce el cuarto número (del 0 al 20):"));
numerosUsuario[4] = parseInt(prompt("Introduce el quinto número (del 0 al 20):"));


var aciertos = [];
var numeroAciertos = 0;
var j;


for (i = 0; i < 5; i++) {
    for (j = 0; j < 10; j++) {
        if (numerosUsuario[i] == numerosAleatorios[j]) {
            aciertos[numeroAciertos] = numerosUsuario[i];
            numeroAciertos = numeroAciertos + 1;
            break;
        }
    }
}

var textoAleatorios = "Números generados: ";
for (i = 0; i < 10; i++) {
    textoAleatorios = textoAleatorios + numerosAleatorios[i];
    if (i < 9) {
        textoAleatorios = textoAleatorios + ", ";
    }
}

var textoUsuario = "Tus números: ";
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
    var textoAciertos = "Números acertados: ";
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
