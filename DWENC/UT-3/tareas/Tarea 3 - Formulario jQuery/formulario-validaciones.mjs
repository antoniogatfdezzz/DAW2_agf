/**
 * Valida que un campo no esté vacío y no contenga solo espacios.
 * @param {string} valor Texto a evaluar.
 * @returns {boolean} true si hay al menos un carácter no espacio.
 */
export function validarObligatorio(valor) {
    // Quita espacios al principio y al final y comprueba que quede algún carácter
    return valor.trim().length > 0;
}

/**
 * Valida que el DNI sea válido (8 números y una letra correcta).
 * @param {string} dni Cadena con el DNI a comprobar.
 * @returns {boolean} true si el formato y la letra son válidos; false en caso contrario.
 */
export function validarDNI(dni) {
    // Elimina espacios y pone la letra en mayúsculas
    dni = dni.trim().toUpperCase();
    
    // Debe ser 8 dígitos seguidos de 1 letra
    const dniRegex = /^[0-9]{8}[A-Z]$/;
    
    if (!dniRegex.test(dni)) {
        return false;
    }
    
    // Separa número y letra
    const numero = parseInt(dni.substring(0, 8), 10);
    const letraIntroducida = dni.charAt(8);
    
    // Tabla de letras válidas y cálculo de la esperada
    const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
    const letraCalculada = letras.charAt(numero % 23);
    
    // Compara la letra calculada con la introducida
    return letraIntroducida === letraCalculada;
}

/**
 * Valida que el email tenga formato básico correcto.
 * @param {string} email Email a validar.
 * @returns {boolean} true si cumple el patrón básico y contiene '@'.
 */
export function validarEmail(email) {
    // Comprobación de formato básico de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Asegura que cumple el patrón y contiene una @
    return emailRegex.test(email.trim()) && email.includes('@');
}

/**
 * Valida que dos contraseñas sean iguales y no estén vacías.
 * @param {string} password1 Primera contraseña.
 * @param {string} password2 Segunda contraseña.
 * @returns {boolean} true si coinciden y no están vacías.
 */
export function validarPasswordsIguales(password1, password2) {
    // Deben ser idénticas y no estar vacías
    return password1 === password2 && password1.length > 0;
}

/**
 * Devuelve el mensaje de error asociado a un tipo de validación.
 * @param {string} tipoValidacion Clave del tipo de validación.
 * @returns {string} Mensaje de error legible por el usuario.
 */
export function obtenerMensajeError(tipoValidacion) {
    // Mapa de mensajes por tipo de validación
    const mensajes = {
        'obligatorio': 'Este campo es obligatorio y no puede estar vacío',
        'dni': 'El DNI introducido no es válido. Debe tener 8 números y una letra válida',
        'email': 'El email debe contener una arroba (@) y tener un formato válido',
        'passwordMatch': 'Las contraseñas no coinciden. Por favor, verifica que ambas sean iguales'
    };
    
    // Devuelve el mensaje correspondiente o uno genérico por defecto
    return mensajes[tipoValidacion] || 'El valor introducido no es válido';
}

/**
 * Ejecuta la validación correspondiente según el tipo indicado.
 * @param {'obligatorio'|'dni'|'email'|'passwordMatch'} tipoValidacion Tipo a evaluar.
 * @param {string} valor Valor del campo.
 * @param {JQuery<HTMLElement>|null} $campo Campo asociado (opcional, para casos especiales).
 * @returns {boolean} true si pasa la validación; false en caso contrario.
 */
export function ejecutarValidacion(tipoValidacion, valor, $campo = null) {
    // Despacha la validación según el tipo indicado
    switch (tipoValidacion) {
        case 'obligatorio':
            return validarObligatorio(valor);
        
        case 'dni':
            return validarDNI(valor);
        
        case 'email':
            return validarEmail(valor);
        
        case 'passwordMatch':
            // Recupera la primera contraseña para compararla con el valor actual
            const password1 = $('#password1').val();
            return validarPasswordsIguales(password1, valor);
        
        default:
            // Si no hay validación definida, se considera válido
            return true;
    }
}
