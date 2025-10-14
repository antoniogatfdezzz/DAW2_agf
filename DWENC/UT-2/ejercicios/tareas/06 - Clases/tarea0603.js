class Cuenta {
            constructor(titular, cantidad = 0) {
                if (!titular) {
                    throw new Error("El titular es obligatorio");
                }
                this._titular = titular;
                this._cantidad = cantidad >= 0 ? cantidad : 0;
            }

            // Getters y Setters
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

            // Método toString
            toString() {
                return `Cuenta de ${this._titular}: ${this._cantidad.toFixed(2)}€`;
            }

            // Método ingresar
            ingresar(cantidad) {
                if (cantidad > 0) {
                    this._cantidad += cantidad;
                }
            }

            // Método retirar
            retirar(cantidad) {
                if (cantidad > 0) {
                    this._cantidad -= cantidad;
                    if (this._cantidad < 0) {
                        this._cantidad = 0;
                    }
                }
            }
        }

        function ejercicio0603() {
            let resultado = "=== CLASE CUENTA ===\n\n";

            try {
                // Crear cuentas con diferentes constructores
                resultado += "=== Creando cuentas ===\n";
                
                // Constructor solo con titular
                const cuenta1 = new Cuenta("Ana García");
                resultado += `Cuenta 1: ${cuenta1.toString()}\n`;

                // Constructor con titular y cantidad
                const cuenta2 = new Cuenta("Carlos López", 1500.75);
                resultado += `Cuenta 2: ${cuenta2.toString()}\n`;

                // Constructor con cantidad negativa (debería ser 0)
                const cuenta3 = new Cuenta("María Ruiz", -100);
                resultado += `Cuenta 3 (cantidad negativa): ${cuenta3.toString()}\n\n`;

                // Pruebas de métodos
                resultado += "=== Operaciones ===\n";
                
                cuenta1.ingresar(500);
                resultado += `Después de ingresar 500€ en cuenta1: ${cuenta1.toString()}\n`;
                
                cuenta1.ingresar(-100); // No debería hacer nada
                resultado += `Después de intentar ingresar -100€: ${cuenta1.toString()}\n`;
                
                cuenta1.retirar(200);
                resultado += `Después de retirar 200€: ${cuenta1.toString()}\n`;
                
                cuenta2.retirar(2000); // Debería quedar en 0
                resultado += `Después de retirar 2000€ de cuenta2 (más del saldo): ${cuenta2.toString()}\n`;
                
                cuenta1.retirar(-50); // No debería hacer nada
                resultado += `Después de intentar retirar -50€: ${cuenta1.toString()}\n\n`;

                // Prueba de getters y setters
                resultado += "=== Getters y Setters ===\n";
                resultado += `Titular cuenta1: ${cuenta1.titular}\n`;
                cuenta1.titular = "Ana García Martínez";
                resultado += `Nuevo titular: ${cuenta1.titular}\n`;
                
                cuenta1.cantidad = 750.25;
                resultado += `Nueva cantidad: ${cuenta1.cantidad}€\n`;
                
                cuenta1.cantidad = -100; // Debería ser 0
                resultado += `Intentar asignar cantidad negativa (-100): ${cuenta1.cantidad}€\n`;

            } catch (error) {
                resultado += `Error: ${error.message}\n`;
            }

            document.getElementById("resultado0603").innerHTML = resultado;
            document.getElementById("resultado0603").style.display = "block";
        }