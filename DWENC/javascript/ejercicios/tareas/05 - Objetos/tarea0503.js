function Calculadora0503() {
            this.pantalla = 0;
            this.memoria = 0;
            this.eventListener = null;
        }

        Calculadora0503.prototype.setEventListener = function(listener) {
            this.eventListener = listener;
        };

        Calculadora0503.prototype.emitirEvento = function(operacion, resultado) {
            if (this.eventListener) {
                this.eventListener(operacion, resultado);
            }
        };

        Calculadora0503.prototype.setPantalla = function(valor) {
            this.pantalla = valor;
            this.emitirEvento('setPantalla', this.pantalla);
        };

        Calculadora0503.prototype.sumar = function(operando) {
            this.pantalla = this.pantalla + operando;
            this.emitirEvento('sumar', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.restar = function(operando) {
            this.pantalla = this.pantalla - operando;
            this.emitirEvento('restar', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.multiplicar = function(operando) {
            this.pantalla = this.pantalla * operando;
            this.emitirEvento('multiplicar', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.dividir = function(operando) {
            if (operando === 0) {
                this.emitirEvento('error', 'División por cero');
                return this.pantalla;
            }
            this.pantalla = this.pantalla / operando;
            this.emitirEvento('dividir', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.modulo = function(operando) {
            this.pantalla = this.pantalla % operando;
            this.emitirEvento('modulo', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.potencia = function(operando) {
            this.pantalla = Math.pow(this.pantalla, operando);
            this.emitirEvento('potencia', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.factorialOp = function() {
            this.pantalla = factorial(this.pantalla);
            this.emitirEvento('factorial', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.guardarMemoria = function() {
            this.memoria = this.pantalla;
            this.emitirEvento('guardarMemoria', this.memoria);
            return this.memoria;
        };

        Calculadora0503.prototype.recuperarMemoria = function() {
            this.pantalla = this.memoria;
            this.emitirEvento('recuperarMemoria', this.pantalla);
            return this.pantalla;
        };

        Calculadora0503.prototype.limpiar = function() {
            this.pantalla = 0;
            this.memoria = 0;
            this.emitirEvento('limpiar', 0);
        };

        function menuPrincipal0503() {
            let resultado = "=== CALCULADORA (NEW) ===\n\n";
            
            const calc = new Calculadora0503();
            
            // Configurar el listener de eventos
            calc.setEventListener((operacion, valor) => {
                resultado += `Evento: ${operacion} -> Resultado: ${valor}\n`;
            });

            // Programa de prueba
            resultado += "Ejecutando programa de prueba:\n\n";
            
            calc.setPantalla(6);
            calc.multiplicar(7);
            calc.restar(2);
            calc.guardarMemoria();
            calc.dividir(8);
            calc.recuperarMemoria();
            calc.setPantalla(3);
            calc.factorialOp();
            calc.potencia(2);
            calc.modulo(20);
            calc.limpiar();

            return resultado;
        }

        function ejercicio0503() {
            let resultado = menuPrincipal0503();
            document.getElementById("resultado0503").innerHTML = resultado;
            document.getElementById("resultado0503").style.display = "block";
        }