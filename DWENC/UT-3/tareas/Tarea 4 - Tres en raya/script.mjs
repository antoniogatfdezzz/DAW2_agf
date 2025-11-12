"use strict"; // Activa el modo estricto para detectar errores y malas prácticas

console.log('Script cargado correctamente'); // Mensaje de depuración para confirmar carga del script

// Referencias a elementos del DOM
const celdas = document.querySelectorAll('.cell'); // Lista de las 9 celdas del tablero
const textoEstado = document.querySelector('.status'); // Elemento donde se muestra el estado del juego
const botonReiniciar = document.querySelector('.restart-btn'); // Botón para reiniciar la partida

// Estado de la partida
let jugadorActual = 'X'; // El jugador que tiene el turno (empieza 'X')
let tablero = ['', '', '', '', '', '', '', '', '']; // Representación del tablero (9 posiciones)
let juegoActivo = true; // Indica si la partida está en curso
let puntuacionX = 0; // Contador de victorias del jugador X
let puntuacionO = 0; // Contador de victorias del jugador O

// Elementos de puntuación
const puntuacionXEl = document.getElementById('score-x'); // Span o elemento para mostrar el marcador de X
const puntuacionOEl = document.getElementById('score-o'); // Span o elemento para mostrar el marcador de O

// Combinaciones ganadoras posibles
const condicionesVictoria = [ // Cada subarray define una línea ganadora por índices del tablero
  [0, 1, 2], // Fila superior
  [3, 4, 5], // Fila central
  [6, 7, 8], // Fila inferior
  [0, 3, 6], // Columna izquierda
  [1, 4, 7], // Columna central
  [2, 5, 8], // Columna derecha
  [0, 4, 8], // Diagonal principal
  [2, 4, 6]  // Diagonal secundaria
];

/**
 * Maneja el clic en una celda del tablero. Ignora el clic si la celda ya está ocupada o la partida terminó. Actualiza el tablero, comprueba victoria/empate y alterna turno.
 * @param {MouseEvent} event Evento de clic recibido.
 * @returns {void}
 */
function manejarClickCelda(event) { // Función controladora del clic en una celda
  const celda = event.target; // Elemento de celda clicado
  const indice = celda.getAttribute('data-index'); // Índice de la celda (0-8) leído del atributo data

  if (tablero[indice] !== '' || !juegoActivo) return; // Ignora si la celda ya tiene valor o el juego acabó

  tablero[indice] = jugadorActual; // Escribe la marca del jugador actual en el estado del tablero
  celda.textContent = jugadorActual; // Refleja la marca en el DOM
  const condicionVictoria = comprobarVictoria(); // Comprueba si la jugada produce una victoria
  if (condicionVictoria) { // Si hay combinación ganadora
    condicionVictoria.forEach(i => { // Recorre los índices ganadores
      const el = document.querySelector(`.cell[data-index="${i}"]`); // Obtiene la celda correspondiente en el DOM
      if (el) el.classList.add('win'); // Añade clase para resaltar la victoria
    });
    juegoActivo = false; // Detiene la partida
    textoEstado.textContent = `¡El jugador ${jugadorActual} gana!`; // Muestra mensaje de victoria
    if (jugadorActual === 'X') { // Actualiza marcador según el ganador
      puntuacionX++; // Incrementa contador de X
      puntuacionXEl.textContent = puntuacionX; // Actualiza visualmente el marcador de X
    } else {
      puntuacionO++; // Incrementa contador de O
      puntuacionOEl.textContent = puntuacionO; // Actualiza visualmente el marcador de O
    }
  } else if (tablero.every(c => c !== '')) { // Si no hay victoria y no quedan celdas vacías, es empate
    juegoActivo = false; // Detiene la partida
    textoEstado.textContent = '¡Es un empate!'; // Muestra mensaje de empate
  } else { // En caso contrario, continúa el juego
    jugadorActual = jugadorActual === 'X' ? 'O' : 'X'; // Cambia el turno al otro jugador
    textoEstado.textContent = `Turno del jugador ${jugadorActual}`; // Actualiza el texto de estado
  }
}

/**
 * Comprueba si el jugador actual tiene una combinación ganadora.
 * @returns {number[]|null} Array con los índices ganadores, o null si no hay victoria.
 */
function comprobarVictoria() { // Evalúa todas las combinaciones de victoria
  for (let condicion of condicionesVictoria) { // Itera cada combinación posible
    if (condicion.every(indice => tablero[indice] === jugadorActual)) { // Verifica si todos los índices los ocupa el jugador actual
      return condicion; // Devuelve la combinación ganadora encontrada
    }
  }
  return null; // Si no se encuentra victoria, retorna null
}

/**
 * Reinicia la partida a su estado inicial: limpia tablero y estilos, restablece el jugador y el estado del juego.
 * @returns {void}
 */
function reiniciarJuego() { // Restaura el estado inicial del juego
  tablero = ['', '', '', '', '', '', '', '', '']; // Limpia la representación del tablero
  juegoActivo = true; // Reactiva la partida
  jugadorActual = 'X'; // Vuelve a empezar con el jugador X
  textoEstado.textContent = `Turno del jugador ${jugadorActual}`; // Restablece el mensaje de turno
  celdas.forEach(celda => { // Recorre todas las celdas del tablero
    celda.textContent = ''; // Elimina el contenido visible de la celda
    celda.classList.remove('win'); // Quita la clase de victoria si estaba aplicada
  });
}

celdas.forEach(celda => celda.addEventListener('click', manejarClickCelda)); // Añade listener de clic a cada celda
botonReiniciar.addEventListener('click', reiniciarJuego); // Añade listener al botón de reinicio
textoEstado.textContent = `Turno del jugador ${jugadorActual}`; // Establece el estado inicial en la interfaz