"use strict";

console.log('Script cargado correctamente');

const cells = document.querySelectorAll('.cell');
const statusText = document.querySelector('.status');
const restartBtn = document.querySelector('.restart-btn');
let currentPlayer = 'X';
let board = ['', '', '', '', '', '', '', '', ''];
let isGameActive = true;
let scoreX = 0;
let scoreO = 0;

const scoreXEl = document.getElementById('score-x');
const scoreOEl = document.getElementById('score-o');

const winningConditions = [
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8],
  [0, 3, 6],
  [1, 4, 7],
  [2, 5, 8],
  [0, 4, 8],
  [2, 4, 6]
];

function handleCellClick(event) {
  const cell = event.target;
  const index = cell.getAttribute('data-index');

  if (board[index] !== '' || !isGameActive) return;

  board[index] = currentPlayer;
  cell.textContent = currentPlayer;
  const winCondition = checkWin();
  if (winCondition) {
    winCondition.forEach(i => {
      const el = document.querySelector(`.cell[data-index="${i}"]`);
      if (el) el.classList.add('win');
    });
    isGameActive = false;
    statusText.textContent = `¡El jugador ${currentPlayer} gana!`;
    if (currentPlayer === 'X') {
      scoreX++;
      scoreXEl.textContent = scoreX;
    } else {
      scoreO++;
      scoreOEl.textContent = scoreO;
    }
  } else if (board.every(cell => cell !== '')) {
    isGameActive = false;
    statusText.textContent = '¡Es un empate!';
  } else {
    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
    statusText.textContent = `Turno del jugador ${currentPlayer}`;
  }
}

function checkWin() {
  for (let condition of winningConditions) {
    if (condition.every(index => board[index] === currentPlayer)) {
      return condition;
    }
  }
  return null;
}

function restartGame() {
  board = ['', '', '', '', '', '', '', '', ''];
  isGameActive = true;
  currentPlayer = 'X';
  statusText.textContent = `Turno del jugador ${currentPlayer}`;
  cells.forEach(cell => {
    cell.textContent = '';
    cell.classList.remove('win');
  });
}

cells.forEach(cell => cell.addEventListener('click', handleCellClick));
restartBtn.addEventListener('click', restartGame);
statusText.textContent = `Turno del jugador ${currentPlayer}`;