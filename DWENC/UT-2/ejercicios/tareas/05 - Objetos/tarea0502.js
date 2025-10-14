const CalculadoraModular = (function() {
            // Variables privadas
            let pantalla = 0;
            let memoria = 0;
            let eventListener = null;

            // Función privada para emitir eventos
            function emitirEvento(operacion, resultado) {
                if (eventListener) {
                    eventListener(operacion, resultado);
                }
            }

            // API pública
            return {
                setEventListener(listener) {
                    eventListener = listener;
                },

                getPantalla() {
                    return pantalla;
                },

                getMemoria() {
                    return memoria;
                },

                setPantalla(valor) {
                    pantalla = valor;
                    emitirEvento('setPantalla', pantalla);
                },

                sumar(operando) {
                    pantalla = pantalla + operando;
                    emitirEvento('sumar', pantalla);
                    return pantalla;
                },

                restar(operando) {
                    pantalla = pantalla - operando;
                    emitirEvento('restar', pantalla);
                    return pantalla;
                },

                multiplicar(operando) {
                    pantalla = pantalla * operando;
                    emitirEvento('multiplicar', pantalla);
                    return pantalla;
                },

                dividir(operando) {
                    if (operando === 0) {
                        emitirEvento('error', 'División por cero');
                        return pantalla;
                    }
                    pantalla = pantalla / operando;
                    emitirEvento('dividir', pantalla);
                    return pantalla;
                },

                modulo(operando) {
                    pantalla = pantalla % operando;
                    emitirEvento('modulo', pantalla);
                    return pantalla;
                },

                potencia(operando) {
                    pantalla = Math.pow(pantalla, operando);
                    emitirEvento('potencia', pantalla);
                    return pantalla;
                },

                factorialOp() {
                    pantalla = factorial(pantalla);
                    emitirEvento('factorial', pantalla);
                    return pantalla;
                },

                guardarMemoria() {
                    memoria = pantalla;
                    emitirEvento('guardarMemoria', memoria);
                    return memoria;
                },

                recuperarMemoria() {
                    pantalla = memoria;
                    emitirEvento('recuperarMemoria', pantalla);
                    return pantalla;
                },

                limpiar() {
                    pantalla = 0;
                    memoria = 0;
                    emitirEvento('limpiar', 0);
                }
            };
        })();

        function menuPrincipal0502() {
            let resultado = "=== CALCULADORA MODULAR ===\n\n";
            
            // Configurar el listener de eventos
            CalculadoraModular.setEventListener((operacion, valor) => {
                resultado += `Evento: ${operacion} -> Resultado: ${valor}\n`;
            });

            // Programa de prueba
            resultado += "Ejecutando programa de prueba:\n\n";
            
            CalculadoraModular.setPantalla(8);
            CalculadoraModular.sumar(12);
            CalculadoraModular.dividir(4);
            CalculadoraModular.guardarMemoria();
            CalculadoraModular.multiplicar(3);
            CalculadoraModular.recuperarMemoria();
            CalculadoraModular.setPantalla(4);
            CalculadoraModular.factorialOp();
            CalculadoraModular.potencia(3);
            CalculadoraModular.modulo(50);
            CalculadoraModular.limpiar();

            return resultado;
        }

        function ejercicio0502() {
            let resultado = menuPrincipal0502();
            document.getElementById("resultado0502").innerHTML = resultado;
            document.getElementById("resultado0502").style.display = "block";
        }