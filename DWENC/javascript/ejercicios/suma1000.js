
// Escribe un programa que defina una variable suma y que sume a esa variable 0.1 1000 veces. Muestra el resultado.

let suma = 0.0;
let veces = 1000;

for (let contador = 0; contador < veces; contador++) {
  suma += 0.1;
}

console.log("El resultado es de :", suma);
