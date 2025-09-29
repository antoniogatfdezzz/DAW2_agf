class CuentaGallifantes extends Cuenta {
            constructor(titular, saldoInicial = 0, valorGallifante = 0.5) {
                super(titular, saldoInicial);
                this._valorGallifante = valorGallifante > 0 ? valorGallifante : 0.5;
            }

            get valorGallifante() {
                return this._valorGallifante;
            }

            set valorGallifante(valor) {
                this._valorGallifante = valor > 0 ? valor : 0.5;
            }

            // Método ingresar: recibe gallifantes y los convierte a euros
            ingresarGallifantes(gallifantes) {
                if (gallifantes > 0) {
                    const euros = gallifantes * this._valorGallifante;
                    super.ingresar(euros);
                    return euros;
                }
                return 0;
            }

            // Método retirar: recibe gallifantes y los convierte a euros para retirar
            retirarGallifantes(gallifantes) {
                if (gallifantes > 0) {
                    const euros = gallifantes * this._valorGallifante;
                    const saldoAnterior = this._cantidad;
                    super.retirar(euros);
                    const eurosRetirados = saldoAnterior - this._cantidad;
                    return eurosRetirados / this._valorGallifante;
                }
                return 0;
            }

            // Método para obtener saldo en gallifantes
            getSaldoEnGallifantes() {
                return this._cantidad / this._valorGallifante;
            }

            // Override toString para mostrar también gallifantes
            toString() {
                return `${super.toString()} (${this.getSaldoEnGallifantes().toFixed(2)} gallifantes)`;
            }
        }

        function ejercicio0604() {
            let resultado = "=== CUENTA GALLIFANTES ===\n\n";

            try {
                resultado += "=== Creando cuentas gallifantes ===\n";
                
                // Crear cuenta con diferentes valores de gallifante
                const cuenta1 = new CuentaGallifantes("Pedro Sánchez");
                resultado += `Cuenta 1 (valor gallifante por defecto 0.5€): ${cuenta1.toString()}\n`;

                const cuenta2 = new CuentaGallifantes("Laura Jiménez", 100, 0.75);
                resultado += `Cuenta 2 (saldo inicial 100€, gallifante=0.75€): ${cuenta2.toString()}\n`;

                const cuenta3 = new CuentaGallifantes("Miguel Torres", 0, 1.25);
                resultado += `Cuenta 3 (gallifante=1.25€): ${cuenta3.toString()}\n\n`;

                resultado += "=== Operaciones con gallifantes ===\n";
                
                // Ingresar gallifantes
                const eurosIngresados1 = cuenta1.ingresarGallifantes(200);
                resultado += `Ingresar 200 gallifantes en cuenta1: ${eurosIngresados1}€ ingresados\n`;
                resultado += `Estado cuenta1: ${cuenta1.toString()}\n`;

                const eurosIngresados2 = cuenta3.ingresarGallifantes(80);
                resultado += `Ingresar 80 gallifantes en cuenta3: ${eurosIngresados2}€ ingresados\n`;
                resultado += `Estado cuenta3: ${cuenta3.toString()}\n`;

                // Retirar gallifantes
                const gallifantesRetirados1 = cuenta1.retirarGallifantes(50);
                resultado += `Retirar 50 gallifantes de cuenta1: ${gallifantesRetirados1.toFixed(2)} gallifantes retirados\n`;
                resultado += `Estado cuenta1: ${cuenta1.toString()}\n`;

                // Intentar retirar más gallifantes de los que hay
                const gallifantesRetirados2 = cuenta2.retirarGallifantes(200);
                resultado += `Intentar retirar 200 gallifantes de cuenta2: ${gallifantesRetirados2.toFixed(2)} gallifantes retirados\n`;
                resultado += `Estado cuenta2: ${cuenta2.toString()}\n`;

                resultado += "\n=== Conversiones ===\n";
                resultado += `Saldo cuenta1 en gallifantes: ${cuenta1.getSaldoEnGallifantes().toFixed(2)}\n`;
                resultado += `Valor del gallifante cuenta1: ${cuenta1.valorGallifante}€\n`;
                resultado += `Valor del gallifante cuenta3: ${cuenta3.valorGallifante}€\n`;

                // Cambiar valor del gallifante
                cuenta1.valorGallifante = 0.8;
                resultado += `Después de cambiar valor gallifante a 0.8€: ${cuenta1.toString()}\n`;

            } catch (error) {
                resultado += `Error: ${error.message}\n`;
            }

            document.getElementById("resultado0604").innerHTML = resultado;
            document.getElementById("resultado0604").style.display = "block";
        }