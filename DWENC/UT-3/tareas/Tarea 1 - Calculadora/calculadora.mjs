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
  const actions = {
    '0': () => calc.inputDigit('0'),
    '1': () => calc.inputDigit('1'),
    '2': () => calc.inputDigit('2'),
    '3': () => calc.inputDigit('3'),
    '4': () => calc.inputDigit('4'),
    '5': () => calc.inputDigit('5'),
    '6': () => calc.inputDigit('6'),
    '7': () => calc.inputDigit('7'),
    '8': () => calc.inputDigit('8'),
    '9': () => calc.inputDigit('9'),
    ',': () => calc.inputDecimal(),
    '.': () => calc.inputDecimal(),
    'AC': () => calc.clearAll(),
    '+/-': () => calc.toggleSign(),
    '%': () => calc.percent(),
    '=': () => calc.equals(),
    '+': () => calc.setOperator('+'),
    '-': () => calc.setOperator('-'),
    'x': () => calc.setOperator('x'),
    '*': () => calc.setOperator('x'),
    '÷': () => calc.setOperator('÷'),
    '/': () => calc.setOperator('÷')
  };

  const action = actions[text];
  if (typeof action === 'function') action();
}

container.addEventListener('click', (ev) => {
  const btn = ev.target.closest('button');
  if (!btn) return;
  handleButton(btn.textContent || btn.innerText);
});

document.addEventListener('keydown', (ev) => {
  const key = ev.key;
  if (/^[0-9]$/.test(key)) { handleButton(key); ev.preventDefault(); return; }
  if (key === '.' || key === ',') { handleButton(key); ev.preventDefault(); return; }
  if (key === 'Enter') { handleButton('='); ev.preventDefault(); return; }
  if (key === 'Escape') { handleButton('AC'); ev.preventDefault(); return; }
  if (key === '%') { handleButton('%'); ev.preventDefault(); return; }
  if (key === '+' || key === '-' || key === '*' || key === '/') { handleButton(key); ev.preventDefault(); return; }
  if (key === 'Backspace') { calc.backspace(); ev.preventDefault(); return; }
});

window.__calc = calc;
