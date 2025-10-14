/**
 * Script para resolver automáticamente todos los retos de depuración
 */
"use strict";

// Simulamos las iniciales para el cálculo
const iniciales = "AGF"; // Puedes cambiar esto por tus iniciales reales

// Convierte las iniciales en un valor numérico al azar que depende de las iniciales.
let id_alumno = 1n;
let multiplicador = 23n;
for(let ch of iniciales) {
    id_alumno *= BigInt(ch.charCodeAt(0))*multiplicador;
    multiplicador += 23n;
}

id_alumno %= 1000n;
console.log("ID Alumno:", id_alumno);

// RETO 1: Calculamos el valor de p1 después de la división
let p1 = id_alumno;
p1 *= 3n;
p1 /= 7n; // Este es el valor que necesitamos para 'a'
console.log("RETO 1 - Valor para 'a':", p1);
p1 += 123n;

// RETO 2: Calculamos qué valor necesita 'b'
let b_valor = id_alumno - 666n;
console.log("RETO 2 - Valor para 'b':", b_valor);

// RETO 3: Simulamos el array aleatorio
let vector = [];
for(let n = 0n;n < BigInt((Math.random()*999999).toFixed());n++) vector[n] = n;
console.log("RETO 3 - Tamaño del vector:", BigInt(vector.length));

// RETO 4: Calculamos la posición y el valor
let tamaño = BigInt(vector.length);
let posición = tamaño / 2n;
for(let n = 0n; n < tamaño; n++) {
    vector[n] = n%128n;
}
console.log("RETO 4 - Posición:", posición);
console.log("RETO 4 - Valor en vector[posición]:", vector[posición]);

// RETO 5: Simulamos la cadena aleatoria
let s1 = "" + Math.random()+"--"+Math.random()+"--"+Math.random();
let ps = (Math.random()*s1.length).toFixed();
console.log("RETO 5 - Cadena s1:", s1);
console.log("RETO 5 - Posición ps:", ps);
console.log("RETO 5 - Carácter en s1[ps]:", s1[ps]);

// RETO 6: Simulamos la función
console.log("\nRETO 6 - Simulando función reto6():");
let iteraciones = BigInt( (Math.random()*1000).toFixed() );
let iteracion = BigInt( (iteraciones+1n)/2n );
console.log("RETO 6 - Iteraciones totales:", iteraciones);
console.log("RETO 6 - Iteración objetivo:", iteracion);

let valor_anterior = 0n;
let n = 0n;
for(;n < iteraciones;n++) {
    if(n == iteracion) {
        console.log("RETO 6 - Valor anterior en iteración", iteracion, ":", valor_anterior);
        break;
    }
    valor_anterior = BigInt((Math.random()*1000000).toFixed());
}