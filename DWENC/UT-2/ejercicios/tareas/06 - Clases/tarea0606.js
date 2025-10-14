const PersonaModular = (function() {
            // Constantes privadas
            const SEXO_HOMBRE = 'H';
            const SEXO_MUJER = 'M';
            const PESO_IDEAL_BAJO = -1;
            const PESO_IDEAL_NORMAL = 0;
            const PESO_IDEAL_SOBREPESO = 1;

            // Función privada para validar DNI
            function validaDNI(dni) {
                if (!dni || dni.length !== 9) return false;
                
                const numero = dni.substring(0, 8);
                const letra = dni.substring(8).toUpperCase();
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                
                if (!/^\d{8}$/.test(numero)) return false;
                
                const letraCalculada = letras[parseInt(numero) % 23];
                return letra === letraCalculada;
            }

            // Constructor de persona
            function crearPersona(nombre, dni, sexo = SEXO_HOMBRE, edad = 0, peso = 0, altura = 0) {
                if (!nombre) throw new Error("El nombre es obligatorio");
                if (!dni) throw new Error("El DNI es obligatorio");
                if (!validaDNI(dni)) throw new Error("DNI incorrecto");

                return {
                    _nombre: nombre,
                    _dni: dni,
                    _sexo: sexo,
                    _edad: edad >= 0 ? edad : 0,
                    _peso: peso >= 0 ? peso : 0,
                    _altura: altura >= 0 ? altura : 0,

                    // Getters
                    getNombre() { return this._nombre; },
                    getDNI() { return this._dni; },
                    getSexo() { return this._sexo; },
                    getEdad() { return this._edad; },
                    getPeso() { return this._peso; },
                    getAltura() { return this._altura; },

                    // Setters
                    setNombre(valor) { 
                        if (!valor) throw new Error("El nombre no puede estar vacío");
                        this._nombre = valor; 
                    },
                    setEdad(valor) { this._edad = valor >= 0 ? valor : 0; },
                    setSexo(valor) { this._sexo = valor; },
                    setPeso(valor) { this._peso = valor >= 0 ? valor : 0; },
                    setAltura(valor) { this._altura = valor >= 0 ? valor : 0; },

                    toString() {
                        return `Persona: ${this._nombre}, DNI: ${this._dni}, Sexo: ${this._sexo}, Edad: ${this._edad}, Peso: ${this._peso}kg, Altura: ${this._altura}m`;
                    },

                    calcularIMC() {
                        if (this._altura === 0) return PESO_IDEAL_BAJO;
                        const imc = this._peso / Math.pow(this._altura, 2);
                        
                        if (imc < 20) return PESO_IDEAL_BAJO;
                        if (imc >= 20 && imc <= 25) return PESO_IDEAL_NORMAL;
                        return PESO_IDEAL_SOBREPESO;
                    },

                    esMayorDeEdad() {
                        return this._edad >= 18;
                    },

                    getDescripcionIMC() {
                        const imc = this.calcularIMC();
                        switch (imc) {
                            case PESO_IDEAL_BAJO: return "Por debajo del peso ideal";
                            case PESO_IDEAL_NORMAL: return "Peso ideal";
                            case PESO_IDEAL_SOBREPESO: return "Sobrepeso";
                            default: return "No se puede calcular";
                        }
                    }
                };
            }

            // Gestor de personas
            function crearGestorPersonas() {
                let personas = [];

                return {
                    crearPersona(nombre, dni, sexo, edad, peso, altura) {
                        try {
                            const persona = crearPersona(nombre, dni, sexo, edad, peso, altura);
                            personas.push(persona);
                            return `Persona creada exitosamente: ${persona.toString()}`;
                        } catch (error) {
                            return `Error al crear persona: ${error.message}`;
                        }
                    },

                    listarPersonas() {
                        if (personas.length === 0) {
                            return "No hay personas registradas";
                        }
                        
                        let resultado = "=== LISTA DE PERSONAS ===\n";
                        personas.forEach((persona, index) => {
                            resultado += `${index + 1}. ${persona.toString()}\n`;
                        });
                        return resultado;
                    },

                    mostrarDetallesPersona(indice) {
                        if (indice < 0 || indice >= personas.length) {
                            return "Índice de persona no válido";
                        }
                        
                        const persona = personas[indice];
                        let resultado = `=== DETALLES PERSONA ${indice + 1} ===\n`;
                        resultado += `${persona.toString()}\n`;
                        resultado += `Es mayor de edad: ${persona.esMayorDeEdad() ? 'Sí' : 'No'}\n`;
                        resultado += `IMC: ${persona.getDescripcionIMC()}\n`;
                        
                        return resultado;
                    },

                    getPersonas() {
                        return personas;
                    }
                };
            }

            // API pública
            return {
                crearGestorPersonas,
                SEXO_HOMBRE,
                SEXO_MUJER
            };
        })();

        function programaPrincipal0606() {
            let resultado = "=== PERSONA MODULAR ===\n\n";
            
            const gestor = PersonaModular.crearGestorPersonas();
            
            // Crear personas de prueba
            resultado += gestor.crearPersona("Luis Martín", "12345678Z", PersonaModular.SEXO_HOMBRE, 28, 75, 1.78) + "\n";
            resultado += gestor.crearPersona("Sara González", "87654321Y", PersonaModular.SEXO_MUJER, 16, 55, 1.62) + "\n";
            resultado += gestor.crearPersona("Roberto Silva", "11111111H", PersonaModular.SEXO_HOMBRE, 45, 90, 1.85) + "\n";
            resultado += gestor.crearPersona("Test Error", "12345678A", PersonaModular.SEXO_HOMBRE, 25, 70, 1.75) + "\n\n";

            // Listar personas
            resultado += gestor.listarPersonas() + "\n\n";

            // Mostrar detalles
            const personas = gestor.getPersonas();
            for (let i = 0; i < personas.length; i++) {
                resultado += gestor.mostrarDetallesPersona(i) + "\n";
            }

            return resultado;
        }

        function ejercicio0606() {
            let resultado = programaPrincipal0606();
            document.getElementById("resultado0606").innerHTML = resultado;
            document.getElementById("resultado0606").style.display = "block";
        }