"use strict";

const PersonaObservable = {
            _nombre: '',
            _edad: 0,
            _dni: '',
            _sexo: SEXO_HOMBRE,
            _peso: 0,
            _altura: 0,
            listener: undefined,

            // Setter y getter para nombre
            set nombre(n) {
                this._nombre = n;
                if (this.listener) this.listener('nombre', n);
            },

            get nombre() {
                return this._nombre;
            },

            // Setter y getter para edad
            set edad(e) {
                this._edad = e;
                if (this.listener) this.listener('edad', e);
            },

            get edad() {
                return this._edad;
            },

            // Setter y getter para DNI
            set dni(d) {
                this._dni = d;
                if (this.listener) this.listener('dni', d);
            },

            get dni() {
                return this._dni;
            },

            // Setter y getter para sexo
            set sexo(s) {
                this._sexo = s;
                if (this.listener) this.listener('sexo', s);
            },

            get sexo() {
                return this._sexo;
            },

            // Setter y getter para peso
            set peso(p) {
                this._peso = p;
                if (this.listener) this.listener('peso', p);
            },

            get peso() {
                return this._peso;
            },

            // Setter y getter para altura
            set altura(a) {
                this._altura = a;
                if (this.listener) this.listener('altura', a);
            },

            get altura() {
                return this._altura;
            },

            // Método para asignar listener
            setListener(listenerFunc) {
                this.listener = listenerFunc;
            },

            // Métodos adicionales
            toString() {
                return `PersonaObservable: ${this._nombre}, DNI: ${this._dni}, Sexo: ${this._sexo}, Edad: ${this._edad}, Peso: ${this._peso}kg, Altura: ${this._altura}m`;
            }
        };

        function ejercicio0507() {
            let resultado = "=== PERSONA OBSERVABLE ===\n\n";
            
            // Crear una copia del objeto para este ejercicio
            const persona = Object.create(PersonaObservable);
            
            // Asignar listener que captura cambios
            persona.setListener((propiedad, valor) => {
                resultado += `Cambio detectado -> ${propiedad}: ${valor}\n`;
            });

            resultado += "Asignando propiedades (se detectarán los cambios):\n\n";
            
            // Asignar valores y capturar eventos
            persona.nombre = "Pedro Martínez";
            persona.edad = 28;
            persona.dni = "12345678Z";
            persona.sexo = SEXO_HOMBRE;
            persona.peso = 75;
            persona.altura = 1.78;

            resultado += `\nEstado final:\n${persona.toString()}`;

            document.getElementById("resultado0507").innerHTML = resultado;
            document.getElementById("resultado0507").style.display = "block";
        }
