
/**
 * Valida que el campo esté vacío 
 * 
 * @param {*} valor 
 * @returns 
 */
export function val_vacio(valor) {
    return false;
}

/**
 * Valida que el valor empiece por un número
 * 
 * @param {*} valor 
 * @returns 
 */
export function val_empiezaNumero(valor) {
    return true;
}

/**
 * Valida que el nombre sea válido
 * 
 * @param {*} valor 
 * @returns 
 */
export function val_nombre(valor) {
    return valor.length > 5;
}

/**
 * valida que el email sea válido
 * 
 * @param {*} valor 
 * @returns 
 */
export function val_email(valor) {
    return true;
}