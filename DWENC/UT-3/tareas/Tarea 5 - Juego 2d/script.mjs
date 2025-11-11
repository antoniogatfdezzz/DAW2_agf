"use strict";

// Referencias a elementos del DOM
const elementos = {
	bloque: document.getElementById('block'),
	btnIniciar: document.getElementById('startBtn'),
	btnReiniciar: document.getElementById('restartBtn'),
	dificultad: document.getElementById('difficulty'),
	puntuacion: document.getElementById('score'),
	temporizador: document.getElementById('timer'),
	capa: document.getElementById('overlay'),
	puntuacionFinal: document.getElementById('final-score'),
	areaJuego: document.getElementById('game-area')
};

// Estado del juego
let estado = {
	enMarcha: false,
	tamanoActual: 80,
	tiempoPorRonda: 3.5,
	tiempoRestante: 0,
	puntuacion: 0,
	idTemporizador: null,
	factorReduccion: 0.9,
	factorTiempo: 0.95
};

// Configuración de dificultades (las claves se mantienen por compatibilidad con el <select>)
const DIFICULTADES = {
	easy: {tamano: 110, tiempo: 5.0, reduccion: 0.92, factorTiempo: 0.96},
	normal: {tamano: 80, tiempo: 3.5, reduccion: 0.88, factorTiempo: 0.92},
	hard: {tamano: 60, tiempo: 2.5, reduccion: 0.82, factorTiempo: 0.88}
};

/**
 * Establece la dificultad del juego ajustando tamaño inicial del bloque, tiempo por ronda y factores de reducción.
 * @param {'easy'|'normal'|'hard'} nivel Dificultad seleccionada.
 * @returns {void}
 */
function establecerDificultad(nivel){
	const cfg = DIFICULTADES[nivel];
	estado.tamanoActual = cfg.tamano;
	estado.tiempoPorRonda = cfg.tiempo;
	estado.factorReduccion = cfg.reduccion;
	estado.factorTiempo = cfg.factorTiempo;
	aplicarTamanoBloque();
}

/**
 * Aplica el tamaño actual del estado al bloque en el DOM.
 * @returns {void}
 */
function aplicarTamanoBloque(){
	elementos.bloque.style.width = Math.max(2, Math.round(estado.tamanoActual)) + 'px';
	elementos.bloque.style.height = Math.max(2, Math.round(estado.tamanoActual)) + 'px';
}

/**
 * Inicia una nueva partida: resetea puntuación y tiempo, oculta overlay, genera el primer bloque y arranca el temporizador.
 * @returns {void}
 */
function iniciarJuego(){
	estado.enMarcha = true;
	estado.puntuacion = 0;
	estado.tiempoRestante = estado.tiempoPorRonda;
	elementos.puntuacion.textContent = formatearPuntuacion(estado.puntuacion);
	elementos.temporizador.textContent = estado.tiempoRestante.toFixed(2);
	elementos.capa.classList.add('hidden');
	generarBloque();
	iniciarTemporizador();
}

/**
 * Genera un bloque ajustando el tamaño y posicionándolo aleatoriamente.
 * @returns {void}
 */
function generarBloque(){
	aplicarTamanoBloque();
	posicionarBloqueAleatorio();
}

/**
 * Calcula una posición aleatoria dentro del área de juego y mueve el bloque. Garantiza márgenes internos y evita posiciones fuera de límites.
 * @returns {void}
 */
function posicionarBloqueAleatorio(){
	const area = elementos.areaJuego.getBoundingClientRect();
	const tamano = estado.tamanoActual;
	const margen = 8;
	const x = Math.random() * (Math.max(0, area.width - tamano - margen*2)) + margen;
	const y = Math.random() * (Math.max(0, area.height - tamano - margen*2)) + margen;
	elementos.bloque.style.left = x + 'px';
	elementos.bloque.style.top = y + 'px';
	elementos.bloque.style.transform = 'translate(0,0)';
}

/**
 * Inicia o reinicia el temporizador de la ronda; decrementa el tiempo cada 50ms y finaliza el juego si llega a cero.
 * @returns {void}
 */
function iniciarTemporizador(){
	if(estado.idTemporizador) clearInterval(estado.idTemporizador);
	estado.tiempoRestante = estado.tiempoPorRonda;
	elementos.temporizador.textContent = estado.tiempoRestante.toFixed(2);
	estado.idTemporizador = setInterval(()=>{
		estado.tiempoRestante -= 0.05;
		elementos.temporizador.textContent = Math.max(0, estado.tiempoRestante).toFixed(2);
		if(estado.tiempoRestante <= 0){
			finDelJuego();
		}
	},50);
}

/**
 * Maneja el clic sobre el bloque durante la partida: suma puntuación por tiempo restante, reduce tamaño y tiempo y genera nueva ronda. Finaliza si se alcanza un límite mínimo.
 * @returns {void}
 */
function manejarClicBloque(){
	if(!estado.enMarcha) return;
	const ganado = Math.max(0, estado.tiempoRestante);
	estado.puntuacion += ganado;
	elementos.puntuacion.textContent = formatearPuntuacion(estado.puntuacion);

	estado.tamanoActual = estado.tamanoActual * estado.factorReduccion;
	estado.tiempoPorRonda = estado.tiempoPorRonda * estado.factorTiempo;

	if(estado.tamanoActual <= 4 || estado.tiempoPorRonda <= 0.05){
		finDelJuego();
		return;
	}

	generarBloque();
	iniciarTemporizador();
}

/**
 * Formatea un número como puntuación con dos decimales.
 * @param {number} valor Puntuación numérica.
 * @returns {string} Puntuación formateada.
 */
function formatearPuntuacion(valor){
	return Number(valor).toFixed(2);
}

/**
 * Finaliza el juego: detiene el temporizador y muestra overlay con la puntuación.
 * @returns {void}
 */
function finDelJuego(){
	estado.enMarcha = false;
	if(estado.idTemporizador) { clearInterval(estado.idTemporizador); estado.idTemporizador = null; }
	elementos.puntuacionFinal.textContent = formatearPuntuacion(estado.puntuacion);
	elementos.capa.classList.remove('hidden');
}

elementos.btnIniciar.addEventListener('click', ()=>{
	establecerDificultad(elementos.dificultad.value);
	iniciarJuego();
});

elementos.btnReiniciar.addEventListener('click', ()=>{
	establecerDificultad(elementos.dificultad.value);
	iniciarJuego();
});

elementos.bloque.addEventListener('click', manejarClicBloque);

elementos.dificultad.addEventListener('change', ()=>{
	establecerDificultad(elementos.dificultad.value);
});

establecerDificultad(elementos.dificultad.value);

window.addEventListener('resize', ()=>{
	const area = elementos.areaJuego.getBoundingClientRect();
	const tamano = estado.tamanoActual;
	const left = parseFloat(elementos.bloque.style.left || 0);
	const top = parseFloat(elementos.bloque.style.top || 0);
	const maxX = Math.max(0, area.width - tamano - 8);
	const maxY = Math.max(0, area.height - tamano - 8);
	if(left > maxX || top > maxY){
		posicionarBloqueAleatorio();
	}
});
