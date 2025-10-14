// -----------------------------
//  Dependencias
// -----------------------------



// -----------------------------
//  Variables Globales
// -----------------------------

let pantallaCalculadora = null;
let operando1 = 0;
let operando2 = 0;
let operador = null;

// -----------------------------
//  Inicialización
// -----------------------------

window.addEventListener("load",() => {

    // Referencia a la pantalla de la calculadora
    pantallaCalculadora = document.getElementById("input");

    // Referencia al array de botones de la calculadora
    const botones = document.querySelectorAll(".calculadora button");

    // Recorremos el arraya de botones
    for (const boton of botones) {
        boton.addEventListener("click", onBotonClick);
    }
})

// -----------------------------
//  Eventos
// -----------------------------

function onBotonClick(evento) {

    // Referencia al boton sobre el que se ha hecho click
    const boton = evento.target;

    // Obtenemos el valor del boton
    const textoBoton = boton.innerText;

    // Procesamos el valor del boton
    if ("0123456789".includes(textoBoton)) {
        pantallaCalculadora.value += textoBoton;
    } else if ("+-x/".includes(textoBoton)) {
        operando1 = Number(pantallaCalculadora.value);
        operador = textoBoton;

        // Limpiamos la pantalla
        pantallaCalculadora.value = "";
    } else if (textoBoton === "=") {
        operando2 = Number(pantallaCalculadora.value);
        let resultado = 0;
    }
}

// -----------------------------
//  Funciones
// -----------------------------

function operar(operando1, operando2, operador) {
    let resultado = 0;
    switch (operador) {
        case "+":
            resultado = operando1 + operando2;
            break;
        case "-":
            resultado = operando1 - operando2;
            break;
        case "*":
            resultado = operando1 * operando2;
            break;
        case "/":
            resultado = operando1 / operando2;
            break;
    }
    return resultado;
}