"use strict";

//-------------------------------
//  DEPENDENCIAS
//-------------------------------



//-------------------------------
//  INICIALIZACIÓN
//-------------------------------

// Implementación de la suma
const botonSumar = document.getElementById("sumar");
botonSumar.addEventListener("click", eventoSumar);

// Implementacion del ocultar
const botonesOcultar = document.querySelectorAll(".ocultar");
for(let boton of botonesOcultar){
    boton.addEventListener("click", function(evento)){
        const campo = evento.target.parentNode.querySelector("input");
        campo.style.display = "none";
    }
}

//-------------------------------
//  EVENTOS
//-------------------------------



//-------------------------------
//  FUNCIONES DE UTILIDAD
//-------------------------------


