// Activa el modo estricto de JavaScript para ayudar a detectar errores comunes
"use strict";

/**
 * Comprueba que el valor no esté vacío (campo obligatorio).
 * @param {string} valor Texto a evaluar.
 * @returns {boolean} true si hay al menos un carácter no espacio; false en caso contrario.
 */
export function validarObligatorio(valor) {
    // Elimina espacios al principio y al final y comprueba que haya longitud > 0
    return valor.trim().length > 0;
}

/**
 * Valida un email con una comprobación básica: contiene "@" y no está vacío.
 * @param {string} email Email a evaluar.
 * @returns {boolean} true si contiene "@" y no está vacío; false en caso contrario.
 */
export function validarEmail(email) {
    // Revisa que exista el carácter '@' y que, quitando espacios, no esté vacío
    return email.includes('@') && email.trim().length > 0;
}

/**
 * Valida un DNI español (formato clásico): 8 dígitos seguidos de una letra. Calcula la letra esperada y la compara con la aportada.
 * @param {string} dni DNI a evaluar (se toleran espacios o guiones intermedios).
 * @returns {boolean} true si el formato y la letra son correctos; false en caso contrario.
 */
export function validarDNI(dni) {
    // Elimina guiones/espacios y pasa a mayúsculas para normalizar
    dni = dni.trim().replace(/[-\s]/g, '').toUpperCase();
    
    // Conjunto de letras para el cálculo de la letra del DNI (ejemplo didáctico)
    const letras = "QWERTYUIOPASDFGHJKLÑZXCVBNM";
    // Expresión regular: 8 dígitos seguidos de una letra en mayúscula
    const dniRegex = /^\d{8}[A-Z]$/;
    
    // Comprueba formato: si no coincide, no es válido
    if (!dniRegex.test(dni)) {
        return false;
    }
    
    // Extrae la parte numérica (primeros 8 caracteres) y la convierte a entero
    const numero = parseInt(dni.substring(0, 8), 10);
    // Toma la letra introducida (último carácter)
    const letra = dni.charAt(8);
    // Calcula la letra esperada usando el resto de dividir entre 23
    const letraCalculada = letras.charAt(numero % 23);
    
    // Devuelve true solo si la letra calculada coincide con la introducida
    return letra === letraCalculada;
}

/**
 * Comprueba que la contraseña no esté vacía.
 * @param {string} password Contraseña a evaluar.
 * @returns {boolean} true si no está vacía; false en caso contrario.
 */
export function validarPassword(password) {
    // Comprueba que la contraseña tenga contenido ignorando espacios de los extremos
    return password.trim().length > 0;
}

/**
 * Valida que dos contraseñas coincidan y no estén vacías.
 * @param {string} password1 Primera contraseña.
 * @param {string} password2 Segunda contraseña (confirmación).
 * @returns {boolean} true si son idénticas y no vacías; false en caso contrario.
 */
export function validarPasswordsIguales(password1, password2) {
    // Comprueba igualdad estricta y que no sea cadena vacía
    return password1 === password2 && password1.length > 0;
}

