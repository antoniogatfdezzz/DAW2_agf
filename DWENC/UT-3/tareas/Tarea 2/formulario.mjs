import { 
    validarObligatorio, 
    validarEmail, 
    validarDNI, 
    validarPasswordsIguales, 
    validarPassword 
} from './formulario-validaciones.mjs';

const validaciones = {
    'obligatorio': validarObligatorio,
    'email': validarEmail,
    'dni': validarDNI,
    'password': validarPassword,
    'password-confirm': (valor, elemento) => {
        const password1 = document.getElementById('password1').value;
        return validarPasswordsIguales(password1, valor);
    }
};

const mensajesError = {
    'obligatorio': 'Este campo es obligatorio y no puede estar vacío.',
    'email': 'Debe introducir un email válido que contenga una @.',
    'dni': 'El DNI debe tener el formato correcto (8 dígitos + letra).',
    'password': 'La contraseña no puede estar vacía.',
    'password-confirm': 'Las contraseñas deben ser iguales.'
};


function mostrarAyuda(mensaje) {
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    divMensajes.className = 'ayuda';
    contenido.textContent = mensaje;
}

function mostrarError(campo, mensaje) {
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    // Marcar el campo con error
    campo.classList.add('error');
    
    // Mostrar mensaje de error
    divMensajes.className = 'error';
    contenido.textContent = mensaje;
}

function limpiarError(campo) {
    campo.classList.remove('error');
    
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    divMensajes.className = '';
    contenido.textContent = '';
}

function validarCampo(campo) {
    const tipoValidacion = campo.getAttribute('data-validacion');
    const valor = campo.value;
    
    if (!tipoValidacion || !validaciones[tipoValidacion]) {
        return true;
    }
    
    const esValido = validaciones[tipoValidacion](valor, campo);
    
    if (!esValido) {
        const mensaje = mensajesError[tipoValidacion];
        mostrarError(campo, mensaje);
        return false;
    } else {
        limpiarError(campo);
        return true;
    }
}

function validarFormulario() {
    const campos = document.querySelectorAll('input[data-validacion]');
    let todosValidos = true;
    let primerError = null;
    
    const password1 = document.getElementById('password1');
    const password2 = document.getElementById('password2');
    
    if (password1.value !== password2.value) {
        mostrarError(password1, 'Las contraseñas deben ser iguales. Introduzca la contraseña en ambos campos nuevamente.');
        password1.classList.add('error');
        password2.classList.add('error');
        password1.value = '';
        password2.value = '';
        if (!primerError) primerError = password1;
        todosValidos = false;
    }
    
    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            todosValidos = false;
            if (!primerError) {
                primerError = campo;
            }
        }
    });
    
    if (!todosValidos && primerError) {
        primerError.focus();
    }
    
    return todosValidos;
}

function limpiarFormulario() {
    const formulario = document.getElementById('formularioCliente');
    const campos = formulario.querySelectorAll('input');
    
    campos.forEach(campo => {
        campo.value = '';
        limpiarError(campo);
    });
    
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    divMensajes.className = '';
    contenido.textContent = '';
    
    const primerCampo = document.getElementById('nombre');
    if (primerCampo) {
        primerCampo.focus();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formularioCliente');
    const botonLimpiar = document.getElementById('limpiar');
    const campos = document.querySelectorAll('input[data-validacion]');
    
    campos.forEach(campo => {
        campo.addEventListener('focus', function() {
            const ayuda = this.getAttribute('data-ayuda');
            if (ayuda) {
                mostrarAyuda(ayuda);
            } else {
                const divMensajes = document.getElementById('mensajes');
                const contenido = document.getElementById('mensaje-contenido');
                divMensajes.className = '';
                contenido.textContent = '';
            }
        });
        
        campo.addEventListener('blur', function() {
            validarCampo(this);
        });
    });
    
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormulario()) {
            alert('Formulario enviado correctamente!');
        }
    });
    
    botonLimpiar.addEventListener('click', limpiarFormulario);
    
    const primerCampo = document.getElementById('nombre');
    if (primerCampo) {
        primerCampo.focus();
    }
});