"use strict";


export default class Calculadora {
  constructor() {
    this._pantalla = '0';
    this._operando = null; // valor acumulado
    this._operador = null; // '+', '-', 'x', '÷'
    this._nuevaEntrada = true; // si true, el siguiente dígito reemplaza la pantalla
    this._listener = null;
  }

  // Registrar listener que se invoca con el nuevo valor de pantalla
  setPantallaActualidadListener(fn) {
    this._listener = fn;
  }

  // Obtener pantalla (lectura)
  getPantalla() {
    return this._pantalla;
  }

  _notify() {
    if (typeof this._listener === 'function') this._listener(this._pantalla);
  }

  // Borrar todo
  clearAll() {
    this._pantalla = '0';
    this._operando = null;
    this._operador = null;
    this._nuevaEntrada = true;
    this._notify();
  }

  // Retroceso (backspace)
  backspace() {
    if (this._pantalla === 'ERROR') {
      this.clearAll();
      return;
    }
    if (this._nuevaEntrada) {
      this.clearAll();
      return;
    }
    if (this._pantalla.length <= 1 || (this._pantalla.length === 2 && this._pantalla.startsWith('-'))) {
      this._pantalla = '0';
    } else {
      this._pantalla = this._pantalla.slice(0, -1);
    }
    this._notify();
  }

  // Añadir dígito (0-9)
  inputDigit(d) {
    if (this._pantalla === 'ERROR') {
      this._pantalla = d;
      this._nuevaEntrada = false;
      this._notify();
      return;
    }
    if (this._nuevaEntrada) {
      this._pantalla = d;
      this._nuevaEntrada = false;
    } else {
      this._pantalla = (this._pantalla === '0' ? d : this._pantalla + d);
    }
    this._notify();
  }

  // Añadir decimal
  inputDecimal() {
    if (this._pantalla === 'ERROR') {
      this._pantalla = '0.';
      this._nuevaEntrada = false;
      this._notify();
      return;
    }
    if (this._nuevaEntrada) {
      this._pantalla = '0.';
      this._nuevaEntrada = false;
    } else if (!this._pantalla.includes('.')) {
      this._pantalla += '.';
    }
    this._notify();
  }

  // Cambiar signo
  toggleSign() {
    if (this._pantalla === '0' || this._pantalla === 'ERROR') return;
    this._pantalla = this._pantalla.startsWith('-') ? this._pantalla.slice(1) : '-' + this._pantalla;
    this._notify();
  }

  // Porcentaje (divide por 100)
  percent() {
    try {
      const val = parseFloat(this._pantalla);
      if (Number.isNaN(val)) throw new Error('ERROR');
      this._pantalla = String(val / 100);
      this._nuevaEntrada = true;
      this._notify();
    } catch (e) {
      this._error();
    }
  }

  // Establecer operador y preparar acumulación
  setOperator(op) {
    try {
      if (this._pantalla === 'ERROR') return;
      if (!this._nuevaEntrada) {
        const current = parseFloat(this._pantalla);
        if (this._operando === null) {
          this._operando = current;
        } else {
          // si ya había operador pendiente, calcular antes
          this._compute();
        }
      }
      this._operador = op;
      this._nuevaEntrada = true;
    } catch (e) {
      this._error();
    }
  }

  // Realiza el cálculo pendiente
  _compute() {
    if (this._operador === null || this._operando === null) return;
    const a = this._operando;
    const b = parseFloat(this._pantalla);
    let res;
    switch (this._operador) {
      case '+':
        res = a + b;
        break;
      case '-':
        res = a - b;
        break;
      case 'x':
      case '*':
        res = a * b;
        break;
      case '÷':
      case '/':
        res = a / b;
        break;
      default:
        throw new Error('Operador desconocido');
    }
    if (!isFinite(res)) throw new Error('ERROR');

    // normalizar (quitar .0 innecesario)
    this._pantalla = String(res);
    this._operando = res;
    this._nuevaEntrada = true;
    this._notify();
  }

  // Igual -> calcular
  equals() {
    try {
      if (this._pantalla === 'ERROR') return;
      if (this._operador !== null) {
        this._compute();
        this._operador = null;
      }
    } catch (e) {
      this._error();
    }
  }

  _error() {
    this._pantalla = 'ERROR';
    this._operando = null;
    this._operador = null;
    this._nuevaEntrada = true;
    this._notify();
  }
}
