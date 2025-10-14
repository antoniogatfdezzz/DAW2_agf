
export function validarObligatorio(valor) {
    return valor.trim().length > 0;
}

export function validarEmail(email) {
    return email.includes('@') && email.trim().length > 0;
}

export function validarDNI(dni) {
    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    const dniRegex = /^\d{8}[A-Za-z]$/;
    
    if (!dniRegex.test(dni)) {
        return false;
    }
    
    const numero = parseInt(dni.substring(0, 8));
    const letra = dni.charAt(8).toUpperCase();
    const letraCalculada = letras.charAt(numero % 23);
    
    return letra === letraCalculada;
}

export function validarPasswordsIguales(password1, password2) {
    return password1 === password2 && password1.length > 0;
}

export function validarPassword(password) {
    return password.trim().length > 0;
}