const prompt = require('prompt-sync')();

class Persona {
    static SEXO_HOMBRE = 'H';
    static SEXO_MUJER = 'M';
    static IMC_BAJO = -1;
    static IMC_NORMAL = 0;
    static IMC_SOBREPESO = 1;

    constructor(nombre, dni, sexo, edad = 0, peso = 0, altura = 0) {
        this.nombre = nombre || '';
        this.edad = edad;
        this.dni = this.validaDNI(dni) ? dni : '';
        this.sexo = sexo || Persona.SEXO_HOMBRE;
        this.peso = peso;
        this.altura = altura;
    }

    calcularIMC() {
        if (this.peso <= 0 || this.altura <= 0) {
            return Persona.IMC_BAJO;
        }
        
        const imc = this.peso / (this.altura * this.altura);
        
        if (imc < 20) {
            return Persona.IMC_BAJO;
        } else if (imc >= 20 && imc <= 25) {
            return Persona.IMC_NORMAL;
        } else {
            return Persona.IMC_SOBREPESO;
        }
    }

    esMayorDeEdad() {
        return this.edad >= 18;
    }

    validaDNI(dni) {
        if (!dni || dni.length !== 9) {
            return false;
        }

        const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        const numero = dni.substring(0, 8);
        const letra = dni.charAt(8).toUpperCase();

        if (!/^\d{8}$/.test(numero)) {
            return false;
        }

        const resto = parseInt(numero) % 23;
        return letras.charAt(resto) === letra;
    }

    toString() {
        const estadoIMC = this.calcularIMC() === Persona.IMC_BAJO ? 'Bajo peso' :
                         this.calcularIMC() === Persona.IMC_NORMAL ? 'Peso normal' : 'Sobrepeso';
        const mayorEdad = this.esMayorDeEdad() ? 'Sí' : 'No';
        
        return `Nombre: ${this.nombre}, DNI: ${this.dni}, Sexo: ${this.sexo}, Edad: ${this.edad}, Peso: ${this.peso}kg, Altura: ${this.altura}m, IMC: ${estadoIMC}, Mayor de edad: ${mayorEdad}`;
    }
}

class Menu {
    constructor() {
        this.personas = [];
    }

    mostrarMenu() {
        console.log('\n=== GESTIÓN DE PERSONAS ===');
        console.log('1. Crear persona');
        console.log('2. Mostrar todas las personas');
        console.log('3. Buscar persona por DNI');
        console.log('4. Calcular IMC de una persona');
        console.log('5. Verificar mayoría de edad');
        console.log('6. Salir');
        console.log('============================');
    }

    crearPersona() {
        const nombre = this.solicitarInput('Ingrese el nombre: ');
        const dni = this.solicitarInput('Ingrese el DNI: ');
        const sexo = this.solicitarInput('Ingrese el sexo (H/M): ').toUpperCase();
        const edad = parseInt(this.solicitarInput('Ingrese la edad (opcional, presione Enter para 0): ') || '0');
        const peso = parseFloat(this.solicitarInput('Ingrese el peso en kg (opcional, presione Enter para 0): ') || '0');
        const altura = parseFloat(this.solicitarInput('Ingrese la altura en metros (opcional, presione Enter para 0): ') || '0');

        try {
            const persona = new Persona(nombre, dni, sexo, edad, peso, altura);
            if (persona.dni === '') {
                console.log('Error: DNI inválido. No se pudo crear la persona.');
                return;
            }
            this.personas.push(persona);
            console.log('Persona creada correctamente.');
        } catch (error) {
            console.log('Error al crear la persona: ' + error.message);
        }
    }

    mostrarPersonas() {
        if (this.personas.length === 0) {
            console.log('No hay personas registradas.');
            return;
        }
        
        console.log('\n=== LISTADO DE PERSONAS ===');
        this.personas.forEach((persona, index) => {
            console.log(`${index + 1}. ${persona.toString()}`);
        });
    }

    buscarPersonaPorDNI() {
        const dni = this.solicitarInput('Ingrese el DNI a buscar: ');
        const persona = this.personas.find(p => p.dni === dni);
        
        if (persona) {
            console.log('Persona encontrada:');
            console.log(persona.toString());
        } else {
            console.log('Persona no encontrada.');
        }
    }

    calcularIMCPersona() {
        const dni = this.solicitarInput('Ingrese el DNI de la persona: ');
        const persona = this.personas.find(p => p.dni === dni);
        
        if (persona) {
            const imc = persona.calcularIMC();
            let estado;
            switch (imc) {
                case Persona.IMC_BAJO:
                    estado = 'Bajo peso';
                    break;
                case Persona.IMC_NORMAL:
                    estado = 'Peso normal';
                    break;
                case Persona.IMC_SOBREPESO:
                    estado = 'Sobrepeso';
                    break;
            }
            console.log(`IMC de ${persona.nombre}: ${estado}`);
        } else {
            console.log('Persona no encontrada.');
        }
    }

    verificarMayoriaEdad() {
        const dni = this.solicitarInput('Ingrese el DNI de la persona: ');
        const persona = this.personas.find(p => p.dni === dni);
        
        if (persona) {
            const esMayor = persona.esMayorDeEdad();
            console.log(`${persona.nombre} ${esMayor ? 'es' : 'no es'} mayor de edad.`);
        } else {
            console.log('Persona no encontrada.');
        }
    }

    solicitarInput(mensaje) {
        return prompt(mensaje);
    }

    ejecutar() {
        let continuar = true;
        
        while (continuar) {
            this.mostrarMenu();
            const opcion = this.solicitarInput('Seleccione una opción: ');
            
            switch (opcion) {
                case '1':
                    this.crearPersona();
                    break;
                case '2':
                    this.mostrarPersonas();
                    break;
                case '3':
                    this.buscarPersonaPorDNI();
                    break;
                case '4':
                    this.calcularIMCPersona();
                    break;
                case '5':
                    this.verificarMayoriaEdad();
                    break;
                case '6':
                    console.log('¡Hasta luego!');
                    continuar = false;
                    break;
                default:
                    console.log('Opción no válida. Intente de nuevo.');
            }
        }
    }
}

function main() {
    const menu = new Menu();
    menu.ejecutar();
}

main();
