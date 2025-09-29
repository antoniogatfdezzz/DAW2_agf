class Cuenta {
    
    constructor(titular, cantidad) {
        this.titular = titular;
        this.cantidad = cantidad || 0;
    }

    getTitular() {
        return this.titular;
    }

    getCantidad() {
        return this.cantidad;
    }

    setTitular(titular) {
        this.titular = titular;
    }

    setCantidad(cantidad) {
        this.cantidad = cantidad;
    }

    ingresar(cantidad) {
        if (cantidad > 0) {
            this.cantidad += cantidad;
        }
    }

    retirar(cantidad) {
        if (cantidad > 0) {
            this.cantidad -= cantidad;
            if (this.cantidad < 0) {
                this.cantidad = 0;
            }
        }
    }

    toString() {
        return "Cuenta - Titular: " + this.titular + ", Cantidad: " + this.cantidad;
    }
}

var cuenta1 = new Cuenta("Juan Pérez", 1000.50);
console.log(cuenta1.toString());

var cuenta2 = new Cuenta("María García");
console.log(cuenta2.toString());

cuenta2.ingresar(500);
console.log(cuenta2.toString());

cuenta2.retirar(300);
console.log(cuenta2.toString());

cuenta2.retirar(500);
console.log(cuenta2.toString());
