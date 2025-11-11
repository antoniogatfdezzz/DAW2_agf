"use strict";

/**
 * Comprueba que el valor no esté vacío (campo obligatorio).
 * @param {string} valor Texto a evaluar.
 * @returns {boolean} true si hay al menos un carácter no espacio; false en caso contrario.
 */
export function validarObligatorio(valor) {
    return valor.trim().length > 0;
}

/**
 * Valida un email con una comprobación básica: contiene "@" y no está vacío.
 * @param {string} email Email a evaluar.
 * @returns {boolean} true si contiene "@" y no está vacío; false en caso contrario.
 */
export function validarEmail(email) {
    return email.includes('@') && email.trim().length > 0;
}

/**
 * Valida un DNI español (formato clásico): 8 dígitos seguidos de una letra. Calcula la letra esperada y la compara con la aportada.
 * @param {string} dni DNI a evaluar (se toleran espacios o guiones intermedios).
 * @returns {boolean} true si el formato y la letra son correctos; false en caso contrario.
 */
export function validarDNI(dni) {
    // Elimina guiones/espacios y pasa a mayúsculas
    dni = dni.trim().replace(/[-\s]/g, '').toUpperCase();
    
    const letras = "QWERTYUIOPASDFGHJKLÑZXCVBNM";
    const dniRegex = /^\d{8}[A-Z]$/;
    
    // Formato inválido si no son 8 dígitos + 1 letra
    if (!dniRegex.test(dni)) {
        return false;
    }
    
    // Calcula la letra a partir del número y la compara con la introducida
    const numero = parseInt(dni.substring(0, 8), 10);
    const letra = dni.charAt(8);
    const letraCalculada = letras.charAt(numero % 23);
    
    return letra === letraCalculada;
}

/**
 * Comprueba que la contraseña no esté vacía.
 * @param {string} password Contraseña a evaluar.
 * @returns {boolean} true si no está vacía; false en caso contrario.
 */
export function validarPassword(password) {
    return password.trim().length > 0;
}

/**
 * Valida que dos contraseñas coincidan y no estén vacías.
 * @param {string} password1 Primera contraseña.
 * @param {string} password2 Segunda contraseña (confirmación).
 * @returns {boolean} true si son idénticas y no vacías; false en caso contrario.
 */
export function validarPasswordsIguales(password1, password2) {
    return password1 === password2 && password1.length > 0;
}

