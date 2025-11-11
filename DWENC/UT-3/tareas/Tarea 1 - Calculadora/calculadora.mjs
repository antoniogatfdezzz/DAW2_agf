"use strict";

// Importa la fábrica de calculadoras (versión básica sin clases)
import crearCalculadora from './calculadora-clase.mjs';


// Crea una instancia simple de calculadora
const calc = crearCalculadora();
const display = document.getElementById('input');
display.setAttribute('readonly', '');

/**
 * Refresca el valor del input del display con el texto actual
 * que proporciona la instancia de la calculadora.
 * @returns {void}
 */
function actualizarPantalla() {
  display.value = calc.obtenerPantalla();
}

// Estado inicial del display
actualizarPantalla();

const container = document.querySelector('.calculadora');

/**
 * Interpreta el texto de un botón pulsado o una tecla y
 * despacha la acción correspondiente sobre la calculadora.
 * Tras ejecutar la acción, actualiza el display.
 * @param {string} texto Etiqueta del botón pulsado (p. ej. "7", ",", "+", "=").
 * @returns {void}
 */
function manejarBoton(texto) {
  texto = (texto || '').trim();
  if (!texto) return;
  const acciones = {
    '0': () => calc.introducirDigito('0'),
    '1': () => calc.introducirDigito('1'),
    '2': () => calc.introducirDigito('2'),
    '3': () => calc.introducirDigito('3'),
    '4': () => calc.introducirDigito('4'),
    '5': () => calc.introducirDigito('5'),
    '6': () => calc.introducirDigito('6'),
    '7': () => calc.introducirDigito('7'),
    '8': () => calc.introducirDigito('8'),
    '9': () => calc.introducirDigito('9'),
    ',': () => calc.introducirDecimal(),
    '.': () => calc.introducirDecimal(),
    'AC': () => calc.borrarTodo(),
    '+/-': () => calc.cambiarSigno(),
    '%': () => calc.convertirPorcentaje(),
    '=': () => calc.calcularResultado(),
    '+': () => calc.establecerOperador('+'),
    '-': () => calc.establecerOperador('-'),
    'x': () => calc.establecerOperador('x'),
    '*': () => calc.establecerOperador('x'),
    '÷': () => calc.establecerOperador('÷'),
    '/': () => calc.establecerOperador('÷')
  };

  const accion = acciones[texto];
  if (typeof accion === 'function') {
    accion();
    actualizarPantalla();
  }
}

container.addEventListener('click', (ev) => {
  const btn = ev.target.closest('button');
  if (!btn) return;
  manejarBoton(btn.textContent || btn.innerText);
});

document.addEventListener('keydown', (ev) => {
  const key = ev.key;
  if (/^[0-9]$/.test(key)) { manejarBoton(key); ev.preventDefault(); return; }
  if (key === '.' || key === ',') { manejarBoton(key); ev.preventDefault(); return; }
  if (key === 'Enter') { manejarBoton('='); ev.preventDefault(); return; }
  if (key === 'Escape') { manejarBoton('AC'); ev.preventDefault(); return; }
  if (key === '%') { manejarBoton('%'); ev.preventDefault(); return; }
  if (key === '+' || key === '-' || key === '*' || key === '/') { manejarBoton(key); ev.preventDefault(); return; }
  if (key === 'Backspace') { calc.borrarUltimo(); actualizarPantalla(); ev.preventDefault(); return; }
});

// Exponemos la instancia para pruebas desde consola
window.__calc = calc;
