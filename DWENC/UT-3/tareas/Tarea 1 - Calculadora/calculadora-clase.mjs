"use strict";
// Activa el modo estricto de JavaScript para evitar errores silenciosos y malas prácticas.

export default function crearCalculadora() {
  // Función fábrica que devuelve un objeto calculadora con estado encapsulado.
  // Estado interno de la calculadora
  let pantalla = '0';        // Texto mostrado actualmente en el display de la calculadora
  let operando = null;       // Acumula el primer operando o el resultado parcial de operaciones encadenadas
  let operador = null;       // Operador aritmético pendiente ('+', '-', 'x', '÷')
  let nuevaEntrada = true;   // Si es true, el siguiente dígito reemplaza el contenido de pantalla

  /**
   * Marca el estado como error, muestra "ERROR" en la pantalla y reinicia el operando/operador para comenzar una nueva entrada.
   * @returns {void}
   */
    function marcarError() {               // Función de utilidad para centralizar el manejo de errores
    // Pone la pantalla en 'ERROR' y reinicia la operación
    pantalla = 'ERROR';                    // Muestra mensaje de error
    operando = null;                       // Limpia el operando almacenado
    operador = null;                       // Limpia el operador pendiente
    nuevaEntrada = true;                   // Prepara para empezar a escribir un nuevo número
  }

  /**
   * Obtiene el contenido actual mostrado en la pantalla de la calculadora.
   * @returns {string} Texto a mostrar en el display (número, "0" o "ERROR").
   */
    function obtenerPantalla() {           // Devuelve el texto tal cual está en el estado
    return pantalla;                       // Cadena con el número o 'ERROR'
  }

  /**
   * Resetea completamente la calculadora a su estado inicial.
   * @returns {void}
   */
  function borrarTodo() {
    pantalla = '0';                        // Vuelve el display a 0
    operando = null;                       // Sin operando almacenado
    operador = null;                       // Sin operador pendiente
    nuevaEntrada = true;                   // La próxima tecla empezará un número nuevo
  }

  /**
   * Elimina el último carácter del display. Si solo queda un carácter (o es un negativo de un dígito), deja la pantalla en "0". Si el estado es de error o es una nueva entrada, resetea todo.
   * @returns {void}
   */
  function borrarUltimo() {
    if (pantalla === 'ERROR') {            // Si hay un error, no se puede borrar un carácter: resetea
      borrarTodo();                        // Resetea completamente
      return;                              // Termina la función
    }
    if (nuevaEntrada) {                    // Si aún no se ha empezado a escribir (p. ej. tras operador)
      borrarTodo();                        // Considera borrar como reset general
      return;                              // Termina la función
    }
    if (pantalla.length <= 1 || (pantalla.length === 2 && pantalla.startsWith('-'))) {
      pantalla = '0';                      // Si queda un solo dígito (o '-X'), sustitúyelo por 0
    } else {
      pantalla = pantalla.slice(0, -1);    // Elimina el último carácter del string
    }
  }

  /**
   * Introduce un dígito en el display, gestionando los casos de nueva entrada y de "0" inicial.
   * @param {string} d Dígito a introducir (carácter '0'-'9').
   * @returns {void}
   */
  function introducirDigito(d) {
    if (pantalla === 'ERROR') {            // Si estaba en error, empezar a escribir reemplaza el error
      pantalla = d;                        // Coloca el dígito como nuevo contenido
      nuevaEntrada = false;                // Ya no es nueva entrada
      return;                              // Termina
    }
    if (nuevaEntrada) {                    // Si se esperaba nueva entrada (tras operador o AC)
      pantalla = d;                        // Reemplaza el 0 o contenido previo por el nuevo dígito
      nuevaEntrada = false;                // A partir de ahora añadirá al final
    } else {
      pantalla = (pantalla === '0' ? d : pantalla + d); // Sustituye 0 por d o concatena
    }
  }

  /**
   * Inserta el separador decimal en el número mostrado si aún no existe. Si está en estado de error o nueva entrada, comienza con "0.".
   * @returns {void}
   */
  function introducirDecimal() {
    if (pantalla === 'ERROR') {            // En caso de error, empezar desde 0.
      pantalla = '0.';                     // Coloca 0.
      nuevaEntrada = false;                // Ya se está escribiendo
      return;                              // Termina
    }
    if (nuevaEntrada) {                    // Si se va a empezar un número nuevo
      pantalla = '0.';                     // Comienza con 0.
      nuevaEntrada = false;                // A partir de ahora, añadir dígitos
    } else if (!pantalla.includes('.')) {  // Si todavía no hay un punto decimal
      pantalla += '.';                     // Añade el separador decimal
    }
  }

  /**
   * Cambia el signo del valor actual mostrado en pantalla. No realiza cambios si el valor es "0" o "ERROR".
   * @returns {void}
   */
  function cambiarSigno() {
    if (pantalla === '0' || pantalla === 'ERROR') return; // No tiene sentido para 0 o en error
    pantalla = pantalla.startsWith('-') ? pantalla.slice(1) : '-' + pantalla; // Alterna el signo
  }

  /**
   * Convierte el valor actual a porcentaje, dividiéndolo entre 100. Si el valor no es numérico o el resultado no es finito, marca error.
   * @returns {void}
   */
  function convertirPorcentaje() {
    try {                                   // Intenta realizar la operación de porcentaje
      const val = parseFloat(pantalla);     // Convierte el texto de pantalla a número
      if (Number.isNaN(val)) throw new Error('ERROR'); // Si no es número, dispara error
      pantalla = String(val / 100);         // Divide por 100 y muestra como string
      nuevaEntrada = true;                  // Tras porcentaje, la siguiente entrada reemplaza
    } catch (e) {
  marcarError();                            // Cualquier problema pone el estado en ERROR
    }
  }

  /**
   * Establece el operador de la operación. Si ya existía un operando previo y no es nueva entrada, ejecuta la operación interna pendiente.
   * @param {'+'|'-'|'x'|'*'|'÷'|'/'} op Operador a aplicar.
   * @returns {void}
   */
  function establecerOperador(op) {
    try {                                   // Intenta preparar/encadenar una operación
      if (pantalla === 'ERROR') return;     // No hacer nada si el estado es ERROR
      if (!nuevaEntrada) {                  // Si el usuario acaba de introducir un número
        const current = parseFloat(pantalla); // Obtén el valor actual
        if (operando === null) {            // Si no hay acumulado previo
          operando = current;               // Guarda el actual como primer operando
        } else {                            // Si ya hay uno
          calcularOperacionInterna();       // Encadena: calcula con el operador previo
        }
      }
      operador = op;                        // Establece el nuevo operador
      nuevaEntrada = true;                  // Prepara para introducir el siguiente número
    } catch (e) {
  marcarError();                            // Cualquier error pone la calculadora en ERROR
    }
  }

  /**
   * Calcula la operación pendiente entre el operando acumulado y el valor actual de pantalla utilizando el operador seleccionado.
   * Actualiza el display y el operando acumulado con el resultado. Lanza error si el resultado no es finito.
   * @returns {void}
   * @throws {Error} Si el operador es desconocido o el resultado es inválido.
   */
  function calcularOperacionInterna() {
    if (operador === null || operando === null) return; // Si falta algo, no hay nada que calcular
    const a = operando;                   // Primer operando (acumulado)
    const b = parseFloat(pantalla);       // Segundo operando (valor actual del display)
    let res;                              // Resultado de la operación
    switch (operador) {                   // Selecciona operación según el operador
      case '+':
        res = a + b;                      // Suma
        break;
      case '-':
        res = a - b;                      // Resta
        break;
      case 'x':
      case '*':
        res = a * b;                      // Multiplicación
        break;
      case '÷':
      case '/':
        res = a / b;                      // División
        break;
      default:
        throw new Error('Operador desconocido'); // Operador no contemplado
    }
    if (!isFinite(res)) throw new Error('ERROR'); // Evita Infinity o NaN

    pantalla = String(res);               // Muestra el resultado en pantalla
    operando = res;                       // Guarda el resultado para operaciones encadenadas
    nuevaEntrada = true;                  // La siguiente entrada empezará un número nuevo
  }

  /**
   * Finaliza la operación pendiente aplicando el operador sobre el valor actual. Limpia el operador tras calcular.
   * @returns {void}
   */
  function calcularResultado() {
    try {                                  // Intenta resolver la operación pendiente
      if (pantalla === 'ERROR') return;    // No hace nada si hay error
      if (operador !== null) {             // Solo si hay un operador pendiente
  calcularOperacionInterna();              // Calcula a partir del operando acumulado y pantalla
        operador = null;                   // Limpia el operador: la operación ha concluido
      }
    } catch (e) {
  marcarError();                           // Cualquier problema: 'ERROR' en pantalla
    }
  }

  /**
   * Crea una calculadora con estado interno y API para operar sobre ella.
   * Métodos disponibles:
   * @returns {{
   *   obtenerPantalla: ()=>string,
   *   borrarTodo: ()=>void,
   *   borrarUltimo: ()=>void,
   *   introducirDigito: (d:string)=>void,
   *   introducirDecimal: ()=>void,
   *   cambiarSigno: ()=>void,
   *   convertirPorcentaje: ()=>void,
   *   establecerOperador: (op:string)=>void,
   *   calcularResultado: ()=>void
   * }} 
   */
  return {
    obtenerPantalla,          // Devuelve el texto que se mostrará en el display
    borrarTodo,               // Resetea la calculadora
    borrarUltimo,             // Borra el último carácter del número actual
    introducirDigito,         // Introduce un dígito (0-9)
    introducirDecimal,        // Introduce el separador decimal
    cambiarSigno,             // Alterna el signo del número actual
    convertirPorcentaje,      // Divide el valor actual entre 100
    establecerOperador,       // Define el operador para la siguiente operación
    calcularResultado         // Ejecuta la operación pendiente y muestra el resultado
  };
}
