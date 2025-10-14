const ValorObservable0608 = {
            _valor: undefined,
            _listener: undefined,

            // Setter para el valor
            set valor(nuevoValor) {
                this._valor = nuevoValor;
                if (this._listener) {
                    this._listener(nuevoValor);
                }
            },

            // Getter para el valor
            get valor() {
                return this._valor;
            },

            // Función para asignar listener
            setListener(listener) {
                this._listener = listener;
            },

            // Función para obtener el listener actual
            getListener() {
                return this._listener;
            },

            // Función para remover el listener
            removeListener() {
                this._listener = undefined;
            }
        };

        function ejercicio0608() {
            let resultado = "=== VALOR OBSERVABLE ===\n\n";
            
            // Crear una instancia del valor observable
            const observable = Object.create(ValorObservable0608);
            
            // Asignar listener que muestra el nuevo valor por consola (simulado en resultado)
            observable.setListener((nuevoValor) => {
                resultado += `Listener ejecutado: Nuevo valor = ${JSON.stringify(nuevoValor)}\n`;
            });

            resultado += "Programa de prueba - Asignando diferentes valores:\n\n";
            
            // Asignar varios valores y observar los cambios
            observable.valor = "Hola Mundo";
            observable.valor = 42;
            observable.valor = true;
            observable.valor = false;
            observable.valor = null;
            observable.valor = undefined;
            observable.valor = { nombre: "Objeto", tipo: "test", numero: 123 };
            observable.valor = [1, 2, 3, "cuatro", 5];
            observable.valor = new Date();
            observable.valor = Math.PI;

            resultado += `\nValor final almacenado: ${JSON.stringify(observable.valor)}\n\n`;

            // Cambiar listener por uno diferente
            resultado += "=== Cambiando listener ===\n";
            
            observable.setListener((nuevoValor) => {
                resultado += `[NUEVO OBSERVADOR] Detectado cambio -> ${typeof nuevoValor}: ${JSON.stringify(nuevoValor)}\n`;
            });

            observable.valor = "Prueba con nuevo listener";
            observable.valor = 999;
            observable.valor = { test: "objeto final" };

            // Remover listener
            resultado += "\n=== Removiendo listener ===\n";
            observable.removeListener();
            observable.valor = "Sin listener"; // No debería generar evento
            
            resultado += "Valor asignado sin listener (no se detecta cambio)\n";
            resultado += `Valor actual: ${JSON.stringify(observable.valor)}`;

            document.getElementById("resultado0608").innerHTML = resultado;
            document.getElementById("resultado0608").style.display = "block";
        }