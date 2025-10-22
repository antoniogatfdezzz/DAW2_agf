const campoNombre = document.getElementById('nombre');
const campoNumero = document.getElementById('numero');
const errorNombre = document.getElementById('errorNombre');
const errorNumero = document.getElementById('errorNumero');


function validarNombre(valor) {
    return valor.trim() !== '';
}

function validarNumeroPar(valor) {
    const numero = Number(valor);
    
    if (isNaN(numero) || valor.trim() === '') {
        return false;
    }
    
    return numero % 2 === 0;
}

function marcarError(campo, mensajeError, mensaje) {
    campo.classList.add('input-error');
    mensajeError.textContent = mensaje;
}

function limpiarError(campo, mensajeError) {
    campo.classList.remove('input-error');
    mensajeError.textContent = '';
}

campoNombre.addEventListener('blur', function() {
    const valor = this.value;
    
    if (!validarNombre(valor)) {
        marcarError(campoNombre, errorNombre, 'El nombre no puede estar vacío');
    } else {
        limpiarError(campoNombre, errorNombre);
    }
});

campoNumero.addEventListener('blur', function() {
    const valor = this.value;
    
    if (!validarNumeroPar(valor)) {
        marcarError(campoNumero, errorNumero, 'Debe introducir un número par');
    } else {
        limpiarError(campoNumero, errorNumero);
    }
});

campoNombre.addEventListener('input', function() {
    if (this.classList.contains('input-error') && validarNombre(this.value)) {
        limpiarError(campoNombre, errorNombre);
    }
});

campoNumero.addEventListener('input', function() {
    if (this.classList.contains('input-error') && validarNumeroPar(this.value)) {
        limpiarError(campoNumero, errorNumero);
    }
});
