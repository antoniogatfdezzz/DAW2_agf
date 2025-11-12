"use strict"; // Activa el modo estricto para evitar errores silenciosos y usar una sintaxis más segura

// Referencias a elementos del DOM
const elementos = { // Agrupamos todas las referencias a elementos HTML para accederlas cómodamente
	bloque: document.getElementById('block'), // Elemento clicable (el cuadrito que aparece y se mueve)
	btnIniciar: document.getElementById('startBtn'), // Botón para iniciar una partida
	btnReiniciar: document.getElementById('restartBtn'), // Botón para reiniciar tras terminar
	dificultad: document.getElementById('difficulty'), // Selector de nivel de dificultad
	puntuacion: document.getElementById('score'), // Elemento que muestra la puntuación actual
	temporizador: document.getElementById('timer'), // Elemento que muestra el tiempo restante de la ronda
	capa: document.getElementById('overlay'), // Capa que se muestra al finalizar el juego
	puntuacionFinal: document.getElementById('final-score'), // Elemento con la puntuación final
	areaJuego: document.getElementById('game-area') // Contenedor donde se posiciona el bloque
};

// Estado del juego
let estado = { // Objeto con el estado dinámico que controla la lógica de la partida
	enMarcha: false, // Indica si hay una partida en curso
	tamanoActual: 80, // Tamaño (px) del bloque actual
	tiempoPorRonda: 3.5, // Segundos disponibles por ronda al inicio
	tiempoRestante: 0, // Tiempo restante que va decreciendo
	puntuacion: 0, // Puntuación acumulada
	idTemporizador: null, // ID del setInterval activo (si lo hay)
	factorReduccion: 0.9, // Factor para reducir el tamaño tras cada acierto
	factorTiempo: 0.95 // Factor para reducir el tiempo por ronda tras cada acierto
};

// Configuración de dificultades (las claves se mantienen por compatibilidad con el <select>)
const DIFICULTADES = { // Tabla de parámetros por dificultad
	easy: {tamano: 110, tiempo: 5.0, reduccion: 0.92, factorTiempo: 0.96}, // Fácil: bloque más grande y más tiempo
	normal: {tamano: 80, tiempo: 3.5, reduccion: 0.88, factorTiempo: 0.92}, // Normal: valores intermedios
	hard: {tamano: 60, tiempo: 2.5, reduccion: 0.82, factorTiempo: 0.88} // Difícil: bloque pequeño y poco tiempo
};

/**
 * Establece la dificultad del juego ajustando tamaño inicial del bloque, tiempo por ronda y factores de reducción.
 * @param {'easy'|'normal'|'hard'} nivel Dificultad seleccionada.
 * @returns {void}
 */
function establecerDificultad(nivel){ // Ajusta el estado según la dificultad seleccionada
	const cfg = DIFICULTADES[nivel]; // Recupera la configuración del nivel
	estado.tamanoActual = cfg.tamano; // Define el tamaño inicial del bloque
	estado.tiempoPorRonda = cfg.tiempo; // Define el tiempo disponible por ronda
	estado.factorReduccion = cfg.reduccion; // Define cuánto se reduce el tamaño cada acierto
	estado.factorTiempo = cfg.factorTiempo; // Define cuánto se reduce el tiempo cada acierto
	aplicarTamanoBloque(); // Aplica el tamaño al bloque en el DOM inmediatamente
}

/**
 * Aplica el tamaño actual del estado al bloque en el DOM.
 * @returns {void}
 */
function aplicarTamanoBloque(){ // Sincroniza el tamaño del bloque visual con el estado
	elementos.bloque.style.width = Math.max(2, Math.round(estado.tamanoActual)) + 'px'; // Asegura mínimo 2px y número entero
	elementos.bloque.style.height = Math.max(2, Math.round(estado.tamanoActual)) + 'px'; // Misma lógica para la altura
}

/**
 * Inicia una nueva partida: resetea puntuación y tiempo, oculta overlay, genera el primer bloque y arranca el temporizador.
 * @returns {void}
 */
function iniciarJuego(){ // Prepara e inicia una nueva partida
	estado.enMarcha = true; // Marca el juego como activo
	estado.puntuacion = 0; // Reinicia la puntuación
	estado.tiempoRestante = estado.tiempoPorRonda; // Reinicia el tiempo de la ronda
	elementos.puntuacion.textContent = formatearPuntuacion(estado.puntuacion); // Muestra la puntuación inicial
	elementos.temporizador.textContent = estado.tiempoRestante.toFixed(2); // Muestra el tiempo inicial
	elementos.capa.classList.add('hidden'); // Oculta la capa de fin de juego si estaba visible
	generarBloque(); // Coloca el primer bloque
	iniciarTemporizador(); // Arranca el temporizador de la ronda
}

/**
 * Genera un bloque ajustando el tamaño y posicionándolo aleatoriamente.
 * @returns {void}
 */
function generarBloque(){ // Genera el bloque para la ronda actual
	aplicarTamanoBloque(); // Ajusta el tamaño según el estado
	posicionarBloqueAleatorio(); // Lo coloca en una posición al azar dentro del área
}

/**
 * Calcula una posición aleatoria dentro del área de juego y mueve el bloque. Garantiza márgenes internos y evita posiciones fuera de límites.
 * @returns {void}
 */
function posicionarBloqueAleatorio(){ // Calcula y aplica una posición válida al bloque
	const area = elementos.areaJuego.getBoundingClientRect(); // Caja y tamaño del área de juego
	const tamano = estado.tamanoActual; // Tamaño del bloque actual
	const margen = 8; // Margen interno para no pegarse al borde
	const x = Math.random() * (Math.max(0, area.width - tamano - margen*2)) + margen; // X aleatoria dentro de límites
	const y = Math.random() * (Math.max(0, area.height - tamano - margen*2)) + margen; // Y aleatoria dentro de límites
	elementos.bloque.style.left = x + 'px'; // Posición horizontal en píxeles
	elementos.bloque.style.top = y + 'px'; // Posición vertical en píxeles
	elementos.bloque.style.transform = 'translate(0,0)'; // Resetea posibles transformaciones previas
}

/**
 * Inicia o reinicia el temporizador de la ronda; decrementa el tiempo cada 50ms y finaliza el juego si llega a cero.
 * @returns {void}
 */
function iniciarTemporizador(){ // Controla la cuenta atrás de la ronda
	if(estado.idTemporizador) clearInterval(estado.idTemporizador); // Evita múltiples intervalos activos
	estado.tiempoRestante = estado.tiempoPorRonda; // Reinicia el tiempo de la ronda
	elementos.temporizador.textContent = estado.tiempoRestante.toFixed(2); // Pinta el tiempo inicial
	estado.idTemporizador = setInterval(()=>{ // Disminuye el tiempo cada 50 ms
		estado.tiempoRestante -= 0.05; // Resta 0,05 segundos
		elementos.temporizador.textContent = Math.max(0, estado.tiempoRestante).toFixed(2); // Actualiza visualmente
		if(estado.tiempoRestante <= 0){ // Si el tiempo se agota
			finDelJuego(); // Finaliza la partida
		}
	},50); // Intervalo de 50 milisegundos
}

/**
 * Maneja el clic sobre el bloque durante la partida: suma puntuación por tiempo restante, reduce tamaño y tiempo y genera nueva ronda. Finaliza si se alcanza un límite mínimo.
 * @returns {void}
 */
function manejarClicBloque(){ // Lógica al acertar clicando el bloque
	if(!estado.enMarcha) return; // Ignora clics si no hay partida
	const ganado = Math.max(0, estado.tiempoRestante); // Puntos ganados = tiempo restante (no negativo)
	estado.puntuacion += ganado; // Acumula la puntuación
	elementos.puntuacion.textContent = formatearPuntuacion(estado.puntuacion); // Refleja la puntuación en pantalla

	estado.tamanoActual = estado.tamanoActual * estado.factorReduccion; // Reduce el tamaño para subir dificultad
	estado.tiempoPorRonda = estado.tiempoPorRonda * estado.factorTiempo; // Reduce el tiempo para la siguiente ronda

	if(estado.tamanoActual <= 4 || estado.tiempoPorRonda <= 0.05){ // Si se alcanza el mínimo razonable
		finDelJuego(); // Finaliza la partida
		return; // Evita continuar con nueva ronda
	}

	generarBloque(); // Prepara la siguiente posición del bloque
	iniciarTemporizador(); // Reinicia el temporizador para la nueva ronda
}

/**
 * Formatea un número como puntuación con dos decimales.
 * @param {number} valor Puntuación numérica.
 * @returns {string} Puntuación formateada.
 */
function formatearPuntuacion(valor){ // Devuelve un string con dos decimales
	return Number(valor).toFixed(2); // Convierte a número y formatea a 2 decimales
}

/**
 * Finaliza el juego: detiene el temporizador y muestra overlay con la puntuación.
 * @returns {void}
 */
function finDelJuego(){ // Limpia temporizador y muestra resultados finales
	estado.enMarcha = false; // Marca el juego como detenido
	if(estado.idTemporizador) { clearInterval(estado.idTemporizador); estado.idTemporizador = null; } // Detiene el intervalo
	elementos.puntuacionFinal.textContent = formatearPuntuacion(estado.puntuacion); // Pinta la puntuación final
	elementos.capa.classList.remove('hidden'); // Muestra el overlay de fin de juego
}

elementos.btnIniciar.addEventListener('click', ()=>{ // Al pulsar Iniciar
	establecerDificultad(elementos.dificultad.value); // Aplica la dificultad seleccionada
	iniciarJuego(); // Comienza la partida
});

elementos.btnReiniciar.addEventListener('click', ()=>{ // Al pulsar Reiniciar
	establecerDificultad(elementos.dificultad.value); // Reaplica la dificultad actual
	iniciarJuego(); // Inicia una nueva partida
});

elementos.bloque.addEventListener('click', manejarClicBloque); // Contabiliza acierto y pasa a la siguiente ronda

elementos.dificultad.addEventListener('change', ()=>{ // Si se cambia la dificultad en el select
	establecerDificultad(elementos.dificultad.value); // Ajusta parámetros inmediatamente
});

establecerDificultad(elementos.dificultad.value); // Inicializa el estado con la dificultad actual del select

window.addEventListener('resize', ()=>{ // Reacciona a cambios de tamaño de ventana
	const area = elementos.areaJuego.getBoundingClientRect(); // Recalcula límites del área
	const tamano = estado.tamanoActual; // Tamaño actual del bloque
	const left = parseFloat(elementos.bloque.style.left || 0); // Posición X actual del bloque
	const top = parseFloat(elementos.bloque.style.top || 0); // Posición Y actual del bloque
	const maxX = Math.max(0, area.width - tamano - 8); // Máximo X dentro del área con margen
	const maxY = Math.max(0, area.height - tamano - 8); // Máximo Y dentro del área con margen
	if(left > maxX || top > maxY){ // Si el bloque queda fuera tras el resize
		posicionarBloqueAleatorio(); // Reubicarlo dentro de los límites
	}
});
