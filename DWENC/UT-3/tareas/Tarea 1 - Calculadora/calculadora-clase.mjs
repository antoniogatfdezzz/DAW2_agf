"use strict";

export default function crearCalculadora() {
  // Estado interno de la calculadora
  let pantalla = '0';
  let operando = null;
  let operador = null;
  let nuevaEntrada = true;

  /**
   * Marca el estado como error, muestra "ERROR" en la pantalla y reinicia el operando/operador para comenzar una nueva entrada.
   * @returns {void}
   */
    function marcarError() {
    // Pone la pantalla en 'ERROR' y reinicia la operación
    pantalla = 'ERROR';
    operando = null;
    operador = null;
    nuevaEntrada = true;
  }

  /**
   * Obtiene el contenido actual mostrado en la pantalla de la calculadora.
   * @returns {string} Texto a mostrar en el display (número, "0" o "ERROR").
   */
    function obtenerPantalla() {
    return pantalla;
  }

  /**
   * Resetea completamente la calculadora a su estado inicial.
   * @returns {void}
   */
  function borrarTodo() {
    pantalla = '0';
    operando = null;
    operador = null;
    nuevaEntrada = true;
  }

  /**
   * Elimina el último carácter del display. Si solo queda un carácter (o es un negativo de un dígito), deja la pantalla en "0". Si el estado es de error o es una nueva entrada, resetea todo.
   * @returns {void}
   */
  function borrarUltimo() {
    if (pantalla === 'ERROR') {
      borrarTodo();
      return;
    }
    if (nuevaEntrada) {
      borrarTodo();
      return;
    }
    if (pantalla.length <= 1 || (pantalla.length === 2 && pantalla.startsWith('-'))) {
      pantalla = '0';
    } else {
      pantalla = pantalla.slice(0, -1);
    }
  }

  /**
   * Introduce un dígito en el display, gestionando los casos de nueva entrada y de "0" inicial.
   * @param {string} d Dígito a introducir (carácter '0'-'9').
   * @returns {void}
   */
  function introducirDigito(d) {
    if (pantalla === 'ERROR') {
      pantalla = d;
      nuevaEntrada = false;
      return;
    }
    if (nuevaEntrada) {
      pantalla = d;
      nuevaEntrada = false;
    } else {
      pantalla = (pantalla === '0' ? d : pantalla + d);
    }
  }

  /**
   * Inserta el separador decimal en el número mostrado si aún no existe. Si está en estado de error o nueva entrada, comienza con "0.".
   * @returns {void}
   */
  function introducirDecimal() {
    if (pantalla === 'ERROR') {
      pantalla = '0.';
      nuevaEntrada = false;
      return;
    }
    if (nuevaEntrada) {
      pantalla = '0.';
      nuevaEntrada = false;
    } else if (!pantalla.includes('.')) {
      pantalla += '.';
    }
  }

  /**
   * Cambia el signo del valor actual mostrado en pantalla. No realiza cambios si el valor es "0" o "ERROR".
   * @returns {void}
   */
  function cambiarSigno() {
    if (pantalla === '0' || pantalla === 'ERROR') return;
    pantalla = pantalla.startsWith('-') ? pantalla.slice(1) : '-' + pantalla;
  }

  /**
   * Convierte el valor actual a porcentaje, dividiéndolo entre 100. Si el valor no es numérico o el resultado no es finito, marca error.
   * @returns {void}
   */
  function convertirPorcentaje() {
    try {
      const val = parseFloat(pantalla);
      if (Number.isNaN(val)) throw new Error('ERROR');
      pantalla = String(val / 100);
      nuevaEntrada = true;
    } catch (e) {
  marcarError();
    }
  }

  /**
   * Establece el operador de la operación. Si ya existía un operando previo y no es nueva entrada, ejecuta la operación interna pendiente.
   * @param {'+'|'-'|'x'|'*'|'÷'|'/'} op Operador a aplicar.
   * @returns {void}
   */
  function establecerOperador(op) {
    try {
      if (pantalla === 'ERROR') return;
      if (!nuevaEntrada) {
        const current = parseFloat(pantalla);
        if (operando === null) {
          operando = current;
        } else {
          calcularOperacionInterna();
        }
      }
      operador = op;
      nuevaEntrada = true;
    } catch (e) {
  marcarError();
    }
  }

  /**
   * Calcula la operación pendiente entre el operando acumulado y el valor actual de pantalla utilizando el operador seleccionado.
   * Actualiza el display y el operando acumulado con el resultado. Lanza error si el resultado no es finito.
   * @returns {void}
   * @throws {Error} Si el operador es desconocido o el resultado es inválido.
   */
  function calcularOperacionInterna() {
    if (operador === null || operando === null) return;
    const a = operando;
    const b = parseFloat(pantalla);
    let res;
    switch (operador) {
      case '+':
        res = a + b;
        break;
      case '-':
        res = a - b;
        break;
      case 'x':
      case '*':
        res = a * b;
        break;
      case '÷':
      case '/':
        res = a / b;
        break;
      default:
        throw new Error('Operador desconocido');
    }
    if (!isFinite(res)) throw new Error('ERROR');

    pantalla = String(res);
    operando = res;
    nuevaEntrada = true;
  }

  /**
   * Finaliza la operación pendiente aplicando el operador sobre el valor actual. Limpia el operador tras calcular.
   * @returns {void}
   */
  function calcularResultado() {
    try {
      if (pantalla === 'ERROR') return;
      if (operador !== null) {
  calcularOperacionInterna();
        operador = null;
      }
    } catch (e) {
  marcarError();
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
    obtenerPantalla,
    borrarTodo,
    borrarUltimo,
    introducirDigito,
    introducirDecimal,
    cambiarSigno,
    convertirPorcentaje,
    establecerOperador,
    calcularResultado
  };
}
