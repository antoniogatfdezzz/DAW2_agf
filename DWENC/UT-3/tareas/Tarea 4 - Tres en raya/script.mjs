"use strict";

console.log('Script cargado correctamente');

// Referencias a elementos del DOM
const celdas = document.querySelectorAll('.cell');
const textoEstado = document.querySelector('.status');
const botonReiniciar = document.querySelector('.restart-btn');

// Estado de la partida
let jugadorActual = 'X';
let tablero = ['', '', '', '', '', '', '', '', ''];
let juegoActivo = true;
let puntuacionX = 0;
let puntuacionO = 0;

// Elementos de puntuación
const puntuacionXEl = document.getElementById('score-x');
const puntuacionOEl = document.getElementById('score-o');

// Combinaciones ganadoras posibles
const condicionesVictoria = [
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8],
  [0, 3, 6],
  [1, 4, 7],
  [2, 5, 8],
  [0, 4, 8],
  [2, 4, 6]
];

/**
 * Maneja el clic en una celda del tablero. Ignora el clic si la celda ya está ocupada o la partida terminó. Actualiza el tablero, comprueba victoria/empate y alterna turno.
 * @param {MouseEvent} event Evento de clic recibido.
 * @returns {void}
 */
function manejarClickCelda(event) {
  const celda = event.target;
  const indice = celda.getAttribute('data-index');

  if (tablero[indice] !== '' || !juegoActivo) return;

  tablero[indice] = jugadorActual;
  celda.textContent = jugadorActual;
  const condicionVictoria = comprobarVictoria();
  if (condicionVictoria) {
    condicionVictoria.forEach(i => {
      const el = document.querySelector(`.cell[data-index="${i}"]`);
      if (el) el.classList.add('win');
    });
    juegoActivo = false;
    textoEstado.textContent = `¡El jugador ${jugadorActual} gana!`;
    if (jugadorActual === 'X') {
      puntuacionX++;
      puntuacionXEl.textContent = puntuacionX;
    } else {
      puntuacionO++;
      puntuacionOEl.textContent = puntuacionO;
    }
  } else if (tablero.every(c => c !== '')) {
    juegoActivo = false;
    textoEstado.textContent = '¡Es un empate!';
  } else {
    jugadorActual = jugadorActual === 'X' ? 'O' : 'X';
    textoEstado.textContent = `Turno del jugador ${jugadorActual}`;
  }
}

/**
 * Comprueba si el jugador actual tiene una combinación ganadora.
 * @returns {number[]|null} Array con los índices ganadores, o null si no hay victoria.
 */
function comprobarVictoria() {
  for (let condicion of condicionesVictoria) {
    if (condicion.every(indice => tablero[indice] === jugadorActual)) {
      return condicion;
    }
  }
  return null;
}

/**
 * Reinicia la partida a su estado inicial: limpia tablero y estilos, restablece el jugador y el estado del juego.
 * @returns {void}
 */
function reiniciarJuego() {
  tablero = ['', '', '', '', '', '', '', '', ''];
  juegoActivo = true;
  jugadorActual = 'X';
  textoEstado.textContent = `Turno del jugador ${jugadorActual}`;
  celdas.forEach(celda => {
    celda.textContent = '';
    celda.classList.remove('win');
  });
}

celdas.forEach(celda => celda.addEventListener('click', manejarClickCelda));
botonReiniciar.addEventListener('click', reiniciarJuego);
textoEstado.textContent = `Turno del jugador ${jugadorActual}`;