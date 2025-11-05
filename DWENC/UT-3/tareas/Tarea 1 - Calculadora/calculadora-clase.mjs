"use strict";


export default class Calculadora {
  constructor() {
    this._pantalla = '0';
    this._operando = null;
    this._operador = null;
    this._nuevaEntrada = true;
    this._listener = null;
  }

  setPantallaActualidadListener(fn) {
    this._listener = fn;
  }

  getPantalla() {
    return this._pantalla;
  }

  _notify() {
    if (typeof this._listener === 'function') this._listener(this._pantalla);
  }

  clearAll() {
    this._pantalla = '0';
    this._operando = null;
    this._operador = null;
    this._nuevaEntrada = true;
    this._notify();
  }

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

  toggleSign() {
    if (this._pantalla === '0' || this._pantalla === 'ERROR') return;
    this._pantalla = this._pantalla.startsWith('-') ? this._pantalla.slice(1) : '-' + this._pantalla;
    this._notify();
  }

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

  setOperator(op) {
    try {
      if (this._pantalla === 'ERROR') return;
      if (!this._nuevaEntrada) {
        const current = parseFloat(this._pantalla);
        if (this._operando === null) {
          this._operando = current;
        } else {
          this._compute();
        }
      }
      this._operador = op;
      this._nuevaEntrada = true;
    } catch (e) {
      this._error();
    }
  }

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

    this._pantalla = String(res);
    this._operando = res;
    this._nuevaEntrada = true;
    this._notify();
  }

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
