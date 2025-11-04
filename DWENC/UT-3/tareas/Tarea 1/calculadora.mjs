"use strict";

import Calculadora from './calculadora-clase.mjs';


const calc = new Calculadora();
const display = document.getElementById('input');
display.setAttribute('readonly', '');

// Actualizar la UI cuando la clase notifique cambios
calc.setPantallaActualidadListener(valor => {
  display.value = valor;
});

// Inicializar valor de la pantalla
display.value = calc.getPantalla();

const container = document.querySelector('.calculadora');

function handleButton(text) {
  text = (text || '').trim();
  if (!text) return;
  // Dígitos
  if (/^[0-9]$/.test(text)) {
    calc.inputDigit(text);
    return;
  }
  // Decimal: en la maqueta aparece "," para decimal
  if (text === ',' || text === '.') {
    calc.inputDecimal();
    return;
  }
  // Acciones especiales
  if (text === 'AC') { calc.clearAll(); return; }
  if (text === '+/-') { calc.toggleSign(); return; }
  if (text === '%') { calc.percent(); return; }
  if (text === '=') { calc.equals(); return; }
  
  // Operadores
  if (['+', '-', 'x', '÷', '/', '*'].includes(text)) {
    // mapear '/','*' a '÷' o 'x' según convenga
    let op = text;
    if (op === '/') op = '÷';
    if (op === '*') op = 'x';
    calc.setOperator(op);
  }
}

// Delegación de eventos para clicks sobre los botones
container.addEventListener('click', (ev) => {
  const btn = ev.target.closest('button');
  if (!btn) return;
  handleButton(btn.textContent || btn.innerText);
});

// Soporte para teclado
document.addEventListener('keydown', (ev) => {
  const key = ev.key;
  // Dígitos
  if (/^[0-9]$/.test(key)) { calc.inputDigit(key); ev.preventDefault(); return; }
  if (key === '.' || key === ',') { calc.inputDecimal(); ev.preventDefault(); return; }
  if (key === 'Enter') { calc.equals(); ev.preventDefault(); return; }
  if (key === 'Escape') { calc.clearAll(); ev.preventDefault(); return; }
  if (key === '%') { calc.percent(); ev.preventDefault(); return; }
  if (key === '+' || key === '-' || key === '*' || key === '/') { handleButton(key); ev.preventDefault(); return; }
  if (key === 'Backspace') { calc.backspace(); ev.preventDefault(); return; }
});

// También exponemos la instancia en window para depuración en consola
window.__calc = calc;
