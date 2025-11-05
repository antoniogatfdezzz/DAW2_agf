"use strict";

import Calculadora from './calculadora-clase.mjs';


const calc = new Calculadora();
const display = document.getElementById('input');
display.setAttribute('readonly', '');

calc.setPantallaActualidadListener(valor => {
  display.value = valor;
});

display.value = calc.getPantalla();

const container = document.querySelector('.calculadora');

function handleButton(text) {
  text = (text || '').trim();
  if (!text) return;
  if (/^[0-9]$/.test(text)) {
    calc.inputDigit(text);
    return;
  }
  if (text === ',' || text === '.') {
    calc.inputDecimal();
    return;
  }
  if (text === 'AC') { calc.clearAll(); return; }
  if (text === '+/-') { calc.toggleSign(); return; }
  if (text === '%') { calc.percent(); return; }
  if (text === '=') { calc.equals(); return; }
  
  if (['+', '-', 'x', '÷', '/', '*'].includes(text)) {
    let op = text;
    if (op === '/') op = '÷';
    if (op === '*') op = 'x';
    calc.setOperator(op);
  }
}

container.addEventListener('click', (ev) => {
  const btn = ev.target.closest('button');
  if (!btn) return;
  handleButton(btn.textContent || btn.innerText);
});

document.addEventListener('keydown', (ev) => {
  const key = ev.key;
  if (/^[0-9]$/.test(key)) { calc.inputDigit(key); ev.preventDefault(); return; }
  if (key === '.' || key === ',') { calc.inputDecimal(); ev.preventDefault(); return; }
  if (key === 'Enter') { calc.equals(); ev.preventDefault(); return; }
  if (key === 'Escape') { calc.clearAll(); ev.preventDefault(); return; }
  if (key === '%') { calc.percent(); ev.preventDefault(); return; }
  if (key === '+' || key === '-' || key === '*' || key === '/') { handleButton(key); ev.preventDefault(); return; }
  if (key === 'Backspace') { calc.backspace(); ev.preventDefault(); return; }
});

window.__calc = calc;
