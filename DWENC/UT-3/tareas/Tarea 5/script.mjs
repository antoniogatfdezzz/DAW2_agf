/* Juego 2D: bloque a clicar antes de que acabe el tiempo.
	 Mecánicas:
	 - Selector de dificultad (easy/normal/hard)
	 - Cada acierto suma el tiempo restante al marcador
	 - Tras cada acierto, el bloque se hace más pequeño y el tiempo disponible disminuye
	 - Juego infinito hasta que tiempo < 0 o tamaño <= 0
*/

const el = {
	block: document.getElementById('block'),
	startBtn: document.getElementById('startBtn'),
	restartBtn: document.getElementById('restartBtn'),
	difficulty: document.getElementById('difficulty'),
	score: document.getElementById('score'),
	timer: document.getElementById('timer'),
	overlay: document.getElementById('overlay'),
	finalScore: document.getElementById('final-score'),
	gameArea: document.getElementById('game-area')
};

let state = {
	running: false,
	currentSize: 80,
	timePerRound: 3.5,
	remainingTime: 0,
	score: 0,
	timerId: null,
	shrinkFactor: 0.9,
	timeFactor: 0.95
};

const DIFFICULTIES = {
	easy: {size: 110, time: 5.0, shrink: 0.92, timeFactor: 0.96},
	normal: {size: 80, time: 3.5, shrink: 0.88, timeFactor: 0.92},
	hard: {size: 60, time: 2.5, shrink: 0.82, timeFactor: 0.88}
};

function setDifficulty(level){
	const cfg = DIFFICULTIES[level];
	state.currentSize = cfg.size;
	state.timePerRound = cfg.time;
	state.shrinkFactor = cfg.shrink;
	state.timeFactor = cfg.timeFactor;
	applyBlockSize();
}

function applyBlockSize(){
	el.block.style.width = Math.max(2, Math.round(state.currentSize)) + 'px';
	el.block.style.height = Math.max(2, Math.round(state.currentSize)) + 'px';
}

function startGame(){
	state.running = true;
	state.score = 0;
	state.remainingTime = state.timePerRound;
	el.score.textContent = formatScore(state.score);
	el.timer.textContent = state.remainingTime.toFixed(2);
	el.overlay.classList.add('hidden');
	spawnBlock();
	startTimer();
}

function spawnBlock(){
	applyBlockSize();
	positionBlockRandom();
}

function positionBlockRandom(){
	const area = el.gameArea.getBoundingClientRect();
	const size = state.currentSize;
	// Margen para que el bloque no salga de la pantalla
	const padding = 8;
	const x = Math.random() * (Math.max(0, area.width - size - padding*2)) + padding;
	const y = Math.random() * (Math.max(0, area.height - size - padding*2)) + padding;
	el.block.style.left = x + 'px';
	el.block.style.top = y + 'px';
	el.block.style.transform = 'translate(0,0)';
}

function startTimer(){
	if(state.timerId) clearInterval(state.timerId);
	state.remainingTime = state.timePerRound;
	el.timer.textContent = state.remainingTime.toFixed(2);
	state.timerId = setInterval(()=>{
		state.remainingTime -= 0.05; // 50ms
		el.timer.textContent = Math.max(0, state.remainingTime).toFixed(2);
		if(state.remainingTime <= 0){
			gameOver();
		}
	},50);
}

function onBlockClick(e){
	if(!state.running) return;
	// Añadir el tiempo restante al marcador
	const gained = Math.max(0, state.remainingTime);
	state.score += gained;
	el.score.textContent = formatScore(state.score);

	// Incremento de dificultad
	state.currentSize = state.currentSize * state.shrinkFactor;
	state.timePerRound = state.timePerRound * state.timeFactor;

	// Condiciones de fin de juego: tamaño menor o tiempo disponible casi 0
	if(state.currentSize <= 4 || state.timePerRound <= 0.05){
		// consideramos que el jugador gana pero el juego termina
		gameOver();
		return;
	}

	// Reiniciar ronda
	spawnBlock();
	startTimer();
}

function formatScore(v){
	return Number(v).toFixed(2);
}

function gameOver(){
	state.running = false;
	if(state.timerId) { clearInterval(state.timerId); state.timerId = null; }
	el.finalScore.textContent = formatScore(state.score);
	el.overlay.classList.remove('hidden');
}

// Event listeners
el.startBtn.addEventListener('click', ()=>{
	setDifficulty(el.difficulty.value);
	startGame();
});

el.restartBtn.addEventListener('click', ()=>{
	setDifficulty(el.difficulty.value);
	startGame();
});

el.block.addEventListener('click', onBlockClick);

// Cambiar dificultad antes de iniciar actualiza valores visuales
el.difficulty.addEventListener('change', ()=>{
	setDifficulty(el.difficulty.value);
});

// Inicializar visualmente
setDifficulty(el.difficulty.value);

// Asegurar que el bloque se reposiciona cuando se redimensiona la ventana
window.addEventListener('resize', ()=>{
	// limitar la posición actual al área
	const area = el.gameArea.getBoundingClientRect();
	const size = state.currentSize;
	const left = parseFloat(el.block.style.left || 0);
	const top = parseFloat(el.block.style.top || 0);
	const maxX = Math.max(0, area.width - size - 8);
	const maxY = Math.max(0, area.height - size - 8);
	if(left > maxX || top > maxY){
		positionBlockRandom();
	}
});
