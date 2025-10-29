"use strict";

export class Cuenta {
    constructor(titular, cantidad = 0) {
        if (!titular) {
            throw new Error("El titular es obligatorio");
        }
        this._titular = titular;
        this._cantidad = cantidad >= 0 ? cantidad : 0;
    }

    get titular() {
        return this._titular;
    }

    set titular(valor) {
        if (!valor) {
            throw new Error("El titular no puede estar vacío");
        }
        this._titular = valor;
    }

    get cantidad() {
        return this._cantidad;
    }

    set cantidad(valor) {
        this._cantidad = valor >= 0 ? valor : 0;
    }

    toString() {
        return `Cuenta de ${this._titular}: ${this._cantidad.toFixed(2)}€`;
    }

    ingresar(cantidad) {
        if (cantidad > 0) {
            this._cantidad += cantidad;
        }
    }

    retirar(cantidad) {
        if (cantidad > 0) {
            this._cantidad -= cantidad;
            if (this._cantidad < 0) {
                this._cantidad = 0;
            }
        }
    }
}
