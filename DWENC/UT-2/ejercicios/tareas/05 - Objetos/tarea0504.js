"use strict";

const CalculadoraPrototipo = {
            init() {
                this.pantalla = 0;
                this.memoria = 0;
                this.eventListener = null;
                return this;
            },

            setEventListener(listener) {
                this.eventListener = listener;
            },

            emitirEvento(operacion, resultado) {
                if (this.eventListener) {
                    this.eventListener(operacion, resultado);
                }
            },

            setPantalla(valor) {
                this.pantalla = valor;
                this.emitirEvento('setPantalla', this.pantalla);
            },

            sumar(operando) {
                this.pantalla = this.pantalla + operando;
                this.emitirEvento('sumar', this.pantalla);
                return this.pantalla;
            },

            restar(operando) {
                this.pantalla = this.pantalla - operando;
                this.emitirEvento('restar', this.pantalla);
                return this.pantalla;
            },

            multiplicar(operando) {
                this.pantalla = this.pantalla * operando;
                this.emitirEvento('multiplicar', this.pantalla);
                return this.pantalla;
            },

            dividir(operando) {
                if (operando === 0) {
                    this.emitirEvento('error', 'División por cero');
                    return this.pantalla;
                }
                this.pantalla = this.pantalla / operando;
                this.emitirEvento('dividir', this.pantalla);
                return this.pantalla;
            },

            modulo(operando) {
                this.pantalla = this.pantalla % operando;
                this.emitirEvento('modulo', this.pantalla);
                return this.pantalla;
            },

            potencia(operando) {
                this.pantalla = Math.pow(this.pantalla, operando);
                this.emitirEvento('potencia', this.pantalla);
                return this.pantalla;
            },

            factorialOp() {
                this.pantalla = factorial(this.pantalla);
                this.emitirEvento('factorial', this.pantalla);
                return this.pantalla;
            },

            guardarMemoria() {
                this.memoria = this.pantalla;
                this.emitirEvento('guardarMemoria', this.memoria);
                return this.memoria;
            },

            recuperarMemoria() {
                this.pantalla = this.memoria;
                this.emitirEvento('recuperarMemoria', this.pantalla);
                return this.pantalla;
            },

            limpiar() {
                this.pantalla = 0;
                this.memoria = 0;
                this.emitirEvento('limpiar', 0);
            }
        };

        function menuPrincipal0504() {
            let resultado = "=== CALCULADORA (Object.create) ===\n\n";
            
            const calc = Object.create(CalculadoraPrototipo).init();
            
            // Configurar el listener de eventos
            calc.setEventListener((operacion, valor) => {
                resultado += `Evento: ${operacion} -> Resultado: ${valor}\n`;
            });

            // Programa de prueba
            resultado += "Ejecutando programa de prueba:\n\n";
            
            calc.setPantalla(9);
            calc.sumar(6);
            calc.dividir(3);
            calc.guardarMemoria();
            calc.multiplicar(4);
            calc.recuperarMemoria();
            calc.setPantalla(6);
            calc.factorialOp();
            calc.potencia(0.5);
            calc.modulo(25);
            calc.limpiar();

            return resultado;
        }

        function ejercicio0504() {
            let resultado = menuPrincipal0504();
            document.getElementById("resultado0504").innerHTML = resultado;
            document.getElementById("resultado0504").style.display = "block";
        }