const SEXO_HOMBRE = 'H';
        const SEXO_MUJER = 'M';
        const PESO_IDEAL_BAJO = -1;
        const PESO_IDEAL_NORMAL = 0;
        const PESO_IDEAL_SOBREPESO = 1;

        class Persona0605 {
            constructor(nombre, dni, sexo = SEXO_HOMBRE, edad = 0, peso = 0, altura = 0) {
                if (!nombre) throw new Error("El nombre es obligatorio");
                if (!dni) throw new Error("El DNI es obligatorio");
                if (!this.validaDNI(dni)) throw new Error("DNI incorrecto");

                this._nombre = nombre;
                this._dni = dni;
                this._sexo = sexo;
                this._edad = edad >= 0 ? edad : 0;
                this._peso = peso >= 0 ? peso : 0;
                this._altura = altura >= 0 ? altura : 0;
            }

            // Getters y Setters
            get nombre() { return this._nombre; }
            set nombre(valor) { 
                if (!valor) throw new Error("El nombre no puede estar vacío");
                this._nombre = valor; 
            }

            get edad() { return this._edad; }
            set edad(valor) { this._edad = valor >= 0 ? valor : 0; }

            get dni() { return this._dni; }
            set dni(valor) { 
                if (this.validaDNI(valor)) {
                    this._dni = valor;
                } else {
                    throw new Error("DNI incorrecto");
                }
            }

            get sexo() { return this._sexo; }
            set sexo(valor) { this._sexo = valor; }

            get peso() { return this._peso; }
            set peso(valor) { this._peso = valor >= 0 ? valor : 0; }

            get altura() { return this._altura; }
            set altura(valor) { this._altura = valor >= 0 ? valor : 0; }

            // Método toString
            toString() {
                return `Persona: ${this._nombre}, DNI: ${this._dni}, Sexo: ${this._sexo}, Edad: ${this._edad}, Peso: ${this._peso}kg, Altura: ${this._altura}m`;
            }

            // Método calcularIMC
            calcularIMC() {
                if (this._altura === 0) return PESO_IDEAL_BAJO;
                const imc = this._peso / Math.pow(this._altura, 2);
                
                if (imc < 20) return PESO_IDEAL_BAJO;
                if (imc >= 20 && imc <= 25) return PESO_IDEAL_NORMAL;
                return PESO_IDEAL_SOBREPESO;
            }

            // Método esMayorDeEdad
            esMayorDeEdad() {
                return this._edad >= 18;
            }

            // Método validaDNI
            validaDNI(dni) {
                if (!dni || dni.length !== 9) return false;
                
                const numero = dni.substring(0, 8);
                const letra = dni.substring(8).toUpperCase();
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                
                if (!/^\d{8}$/.test(numero)) return false;
                
                const letraCalculada = letras[parseInt(numero) % 23];
                return letra === letraCalculada;
            }

            // Método para obtener descripción del IMC
            getDescripcionIMC() {
                const imc = this.calcularIMC();
                switch (imc) {
                    case PESO_IDEAL_BAJO: return "Por debajo del peso ideal";
                    case PESO_IDEAL_NORMAL: return "Peso ideal";
                    case PESO_IDEAL_SOBREPESO: return "Sobrepeso";
                    default: return "No se puede calcular";
                }
            }
        }

        // Clase Menú para gestionar personas
        class MenuPersonas0605 {
            constructor() {
                this.personas = [];
            }

            crearPersona(nombre, dni, sexo, edad, peso, altura) {
                try {
                    const persona = new Persona0605(nombre, dni, sexo, edad, peso, altura);
                    this.personas.push(persona);
                    return `Persona creada exitosamente: ${persona.toString()}`;
                } catch (error) {
                    return `Error al crear persona: ${error.message}`;
                }
            }

            listarPersonas() {
                if (this.personas.length === 0) {
                    return "No hay personas registradas";
                }
                
                let resultado = "=== LISTA DE PERSONAS ===\n";
                this.personas.forEach((persona, index) => {
                    resultado += `${index + 1}. ${persona.toString()}\n`;
                });
                return resultado;
            }

            mostrarDetallesPersona(indice) {
                if (indice < 0 || indice >= this.personas.length) {
                    return "Índice de persona no válido";
                }
                
                const persona = this.personas[indice];
                let resultado = `=== DETALLES PERSONA ${indice + 1} ===\n`;
                resultado += `${persona.toString()}\n`;
                resultado += `Es mayor de edad: ${persona.esMayorDeEdad() ? 'Sí' : 'No'}\n`;
                resultado += `IMC: ${persona.getDescripcionIMC()}\n`;
                
                return resultado;
            }

            ejecutarDemo() {
                let resultado = "=== DEMOSTRACIÓN MENÚ PERSONAS ===\n\n";
                
                // Crear personas de prueba
                resultado += this.crearPersona("Ana García", "12345678Z", SEXO_MUJER, 25, 65, 1.70) + "\n";
                resultado += this.crearPersona("Carlos López", "87654321Y", SEXO_HOMBRE, 17, 80, 1.80) + "\n";
                resultado += this.crearPersona("Elena Ruiz", "11111111H", SEXO_MUJER, 30, 55, 1.65) + "\n";
                resultado += this.crearPersona("Persona Test", "12345678A", SEXO_HOMBRE, 35, 70, 1.75) + "\n\n";

                // Listar personas
                resultado += this.listarPersonas() + "\n\n";

                // Mostrar detalles de cada persona válida
                for (let i = 0; i < this.personas.length; i++) {
                    resultado += this.mostrarDetallesPersona(i) + "\n";
                }

                return resultado;
            }
        }

        function programaPrincipal0605() {
            const menu = new MenuPersonas0605();
            return menu.ejecutarDemo();
        }

        function ejercicio0605() {
            let resultado = programaPrincipal0605();
            document.getElementById("resultado0605").innerHTML = resultado;
            document.getElementById("resultado0605").style.display = "block";
        }