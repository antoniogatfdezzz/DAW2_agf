const EstadoModule = (() => {
    let pantalla = 0;
    let memoria = 0;
    
    return {
        getPantalla: () => pantalla,
        setPantalla: (valor) => pantalla = valor,
        getMemoria: () => memoria,
        setMemoria: (valor) => memoria = valor,
        reset: () => { pantalla = 0; memoria = 0; }
    };
})();

const OperacionesModule = (() => {
    const sumar = (a, b) => a + b;
    const restar = (a, b) => a - b;
    const multiplicar = (a, b) => a * b;
    const dividir = (a, b) => b === 0 ? a : a / b;
    const modulo = (a, b) => b === 0 ? a : a % b;
    const potencia = (base, exp) => Math.pow(base, exp);
    
    const factorial = (n) => {
        if (n < 0 || !Number.isInteger(n)) return n;
        let resultado = 1;
        for (let i = 2; i <= n; i++) resultado *= i;
        return resultado;
    };
    
    return { sumar, restar, multiplicar, dividir, modulo, potencia, factorial };
})();

const EventosModule = (() => {
    let callback = null;
    
    const configurar = (fn) => callback = fn;
    
    const emitir = (operacion, operando, resultado, memoria) => {
        if (callback) {
            callback({ operacion, operando, resultado, memoria });
        }
    };
    
    return { configurar, emitir };
})();

const CalculadoraModule = (() => {
    const calculadora = {
        sumar(operando) {
            const resultado = OperacionesModule.sumar(EstadoModule.getPantalla(), operando);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('suma', operando, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        restar(operando) {
            const resultado = OperacionesModule.restar(EstadoModule.getPantalla(), operando);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('resta', operando, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        multiplicar(operando) {
            const resultado = OperacionesModule.multiplicar(EstadoModule.getPantalla(), operando);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('multiplicación', operando, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        dividir(operando) {
            const resultado = OperacionesModule.dividir(EstadoModule.getPantalla(), operando);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('división', operando, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        modulo(operando) {
            const resultado = OperacionesModule.modulo(EstadoModule.getPantalla(), operando);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('módulo', operando, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        elevarA(exponente) {
            const resultado = OperacionesModule.potencia(EstadoModule.getPantalla(), exponente);
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('potencia', exponente, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        factorial() {
            const resultado = OperacionesModule.factorial(EstadoModule.getPantalla());
            EstadoModule.setPantalla(resultado);
            EventosModule.emitir('factorial', null, resultado, EstadoModule.getMemoria());
            return resultado;
        },
        
        guardarEnMemoria() {
            EstadoModule.setMemoria(EstadoModule.getPantalla());
            EventosModule.emitir('M', null, EstadoModule.getPantalla(), EstadoModule.getMemoria());
            return EstadoModule.getMemoria();
        },
        
        recuperarDeMemoria() {
            EstadoModule.setPantalla(EstadoModule.getMemoria());
            EventosModule.emitir('R', null, EstadoModule.getPantalla(), EstadoModule.getMemoria());
            return EstadoModule.getPantalla();
        },
        
        limpiarTodo() {
            EstadoModule.reset();
            EventosModule.emitir('AC', null, EstadoModule.getPantalla(), EstadoModule.getMemoria());
            return EstadoModule.getPantalla();
        },
        
        establecerValor(valor) {
            EstadoModule.setPantalla(valor);
            EventosModule.emitir('=', valor, EstadoModule.getPantalla(), EstadoModule.getMemoria());
            return EstadoModule.getPantalla();
        }
    };
    
    return { obtener: () => calculadora };
})();

const MenuModule = (() => {
    const mostrar = () => {
        console.log("\n=== CALCULADORA ===");
        console.log("1. Suma  2. Resta  3. Multiplicar  4. Dividir");
        console.log("5. Módulo  6. Factorial  7. Potencia");
        console.log("8. M (memoria)  9. R (recuperar)  0. Limpiar");
    };
    
    return { mostrar };
})();

const LoggerModule = (() => {
    const log = (evento) => {
        const { operacion, operando, resultado, memoria } = evento;
        let mensaje = operacion;
        if (operando !== null) mensaje += ` ${operando}`;
        console.log(`${mensaje} -> Pantalla: ${resultado}, Memoria: ${memoria}`);
    };
    
    return { log };
})();

const TestModule = (() => {
    const ejecutar = () => {
        const calc = CalculadoraModule.obtener();
        EventosModule.configurar(LoggerModule.log);
        
        calc.establecerValor(10);
        calc.sumar(5);
        calc.restar(3);
        calc.multiplicar(2);
        calc.dividir(4);
        calc.modulo(4);
        calc.elevarA(3);
        calc.guardarEnMemoria();
        calc.establecerValor(5);
        calc.factorial();
        calc.recuperarDeMemoria();
        calc.limpiarTodo();
    };
    
    return { ejecutar };
})();

const AppModule = (() => {
    const iniciar = () => {
        console.log("TAREA 0502 - CALCULADORA MODULAR");
        TestModule.ejecutar();
        MenuModule.mostrar();
    };
    
    return { iniciar };
})();

AppModule.iniciar();
