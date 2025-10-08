"use strict";

//-------------------------------
//  DEPENDENCIAS
//-------------------------------



//-------------------------------
//  INICIALIZACIÓN
//-------------------------------

const titulo = document.getElementById("titulo");
titulo.textContent = "Otro titulo";

const parrafos = document.getElementsByTagName("p");
let n = 1;
for(let p of parrafos){
    p.textContent = "Parrafo " + n++;
}

const botones = document.getElementsByName("boton");
let i = 1;
for(let b of botones){
    b.value = "Boton " + i++;
}

const primerParrafo = document.querySelector("p");
primerParrafo.textContent = "Este es el primer parrafo";

//-------------------------------
//  EVENTOS
//-------------------------------



//-------------------------------
//  FUNCIONES DE UTILIDAD
//-------------------------------


