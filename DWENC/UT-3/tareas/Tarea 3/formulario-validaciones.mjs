/**
 * Valida que un campo no esté vacío y no contenga solo espacios
 * @param {string} valor - El valor a validar
 * @returns {boolean} - true si es válido, false si no lo es
 */
export function validarObligatorio(valor) {
    return valor.trim().length > 0;
}

/**
 * Valida que el DNI sea válido (8 números y una letra)
 * @param {string} dni - El DNI a validar
 * @returns {boolean} - true si es válido, false si no lo es
 */
export function validarDNI(dni) {
    dni = dni.trim().toUpperCase();
    
    const dniRegex = /^[0-9]{8}[A-Z]$/;
    
    if (!dniRegex.test(dni)) {
        return false;
    }
    
    const numero = parseInt(dni.substring(0, 8), 10);
    const letraIntroducida = dni.charAt(8);
    
    const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
    const letraCalculada = letras.charAt(numero % 23);
    
    return letraIntroducida === letraCalculada;
}

/**
 * Valida que el email contenga una arroba
 * @param {string} email - El email a validar
 * @returns {boolean} - true si es válido, false si no lo es
 */
export function validarEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email.trim()) && email.includes('@');
}

/**
 * Valida que dos contraseñas sean iguales
 * @param {string} password1 - Primera contraseña
 * @param {string} password2 - Segunda contraseña
 * @returns {boolean} - true si son iguales, false si no lo son
 */
export function validarPasswordsIguales(password1, password2) {
    return password1 === password2 && password1.length > 0;
}

/**
 * Obtiene el mensaje de error según el tipo de validación
 * @param {string} tipoValidacion - El tipo de validación que falló
 * @returns {string} - El mensaje de error
 */
export function obtenerMensajeError(tipoValidacion) {
    const mensajes = {
        'obligatorio': 'Este campo es obligatorio y no puede estar vacío',
        'dni': 'El DNI introducido no es válido. Debe tener 8 números y una letra válida',
        'email': 'El email debe contener una arroba (@) y tener un formato válido',
        'passwordMatch': 'Las contraseñas no coinciden. Por favor, verifica que ambas sean iguales'
    };
    
    return mensajes[tipoValidacion] || 'El valor introducido no es válido';
}

/**
 * Ejecuta la validación correspondiente según el tipo
 * @param {string} tipoValidacion - El tipo de validación a ejecutar
 * @param {string} valor - El valor a validar
 * @param {jQuery} $campo - El campo jQuery (opcional, para validaciones especiales)
 * @returns {boolean} - true si es válido, false si no lo es
 */
export function ejecutarValidacion(tipoValidacion, valor, $campo = null) {
    switch (tipoValidacion) {
        case 'obligatorio':
            return validarObligatorio(valor);
        
        case 'dni':
            return validarDNI(valor);
        
        case 'email':
            return validarEmail(valor);
        
        case 'passwordMatch':
            const password1 = $('#password1').val();
            return validarPasswordsIguales(password1, valor);
        
        default:
            return true;
    }
}
