const PersonaObservable0607 = {
            _nombre: '',
            _edad: 0,
            _dni: '',
            _sexo: 'H',
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
                this._edad = e >= 0 ? e : 0;
                if (this.listener) this.listener('edad', this._edad);
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
                this._peso = p >= 0 ? p : 0;
                if (this.listener) this.listener('peso', this._peso);
            },

            get peso() {
                return this._peso;
            },

            // Setter y getter para altura
            set altura(a) {
                this._altura = a >= 0 ? a : 0;
                if (this.listener) this.listener('altura', this._altura);
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
            },

            calcularIMC() {
                if (this._altura === 0) return -1;
                const imc = this._peso / Math.pow(this._altura, 2);
                
                if (imc < 20) return -1;
                if (imc >= 20 && imc <= 25) return 0;
                return 1;
            },

            esMayorDeEdad() {
                return this._edad >= 18;
            },

            getDescripcionIMC() {
                const imc = this.calcularIMC();
                switch (imc) {
                    case -1: return "Por debajo del peso ideal";
                    case 0: return "Peso ideal";
                    case 1: return "Sobrepeso";
                    default: return "No se puede calcular";
                }
            }
        };

        function ejercicio0607() {
            let resultado = "=== PERSONA OBSERVABLE ===\n\n";
            
            // Crear una copia del objeto para este ejercicio
            const persona = Object.create(PersonaObservable0607);
            
            // Asignar listener que captura cambios
            persona.setListener((propiedad, valor) => {
                resultado += `Cambio detectado -> ${propiedad}: ${valor}\n`;
            });

            resultado += "Asignando propiedades (se detectarán los cambios):\n\n";
            
            // Asignar valores y capturar eventos
            persona.nombre = "Carmen Jiménez";
            persona.edad = 32;
            persona.dni = "12345678Z";
            persona.sexo = "M";
            persona.peso = 62;
            persona.altura = 1.68;

            // Probar validaciones
            persona.edad = -5; // Debería convertirse a 0
            persona.peso = -10; // Debería convertirse a 0
            persona.altura = -1.5; // Debería convertirse a 0

            resultado += `\nEstado final:\n${persona.toString()}\n`;
            resultado += `Es mayor de edad: ${persona.esMayorDeEdad()}\n`;
            resultado += `IMC: ${persona.getDescripcionIMC()}`;

            document.getElementById("resultado0607").innerHTML = resultado;
            document.getElementById("resultado0607").style.display = "block";
        }