"use strict";

const calculadora0501 = {
            pantalla: 0,
            memoria: 0,
            eventListener: null,

            // Método para establecer el listener de eventos
            setEventListener(listener) {
                this.eventListener = listener;
            },

            // Método para disparar eventos
            emitirEvento(operacion, resultado) {
                if (this.eventListener) {
                    this.eventListener(operacion, resultado);
                }
            },

            // Operaciones con dos operandos
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

            // Operaciones con un operando
            factorialOp() {
                this.pantalla = factorial(this.pantalla);
                this.emitirEvento('factorial', this.pantalla);
                return this.pantalla;
            },

            // Operaciones de memoria
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
            },

            // Método para establecer valor inicial
            setPantalla(valor) {
                this.pantalla = valor;
                this.emitirEvento('setPantalla', this.pantalla);
            }
        };

        function menuPrincipal0501() {
            let resultado = "=== CALCULADORA (LITERAL DE OBJETO) ===\n\n";
            
            // Configurar el listener de eventos
            calculadora0501.setEventListener((operacion, valor) => {
                resultado += `Evento: ${operacion} -> Resultado: ${valor}\n`;
            });

            // Programa de prueba
            resultado += "Ejecutando programa de prueba:\n\n";
            
            calculadora0501.setPantalla(10);
            calculadora0501.sumar(5);
            calculadora0501.multiplicar(2);
            calculadora0501.guardarMemoria();
            calculadora0501.dividir(5);
            calculadora0501.recuperarMemoria();
            calculadora0501.setPantalla(5);
            calculadora0501.factorialOp();
            calculadora0501.potencia(2);
            calculadora0501.modulo(100);
            calculadora0501.limpiar();

            return resultado;
        }

        function ejercicio0501() {
            let resultado = menuPrincipal0501();
            document.getElementById("resultado0501").innerHTML = resultado;
            document.getElementById("resultado0501").style.display = "block";
        }