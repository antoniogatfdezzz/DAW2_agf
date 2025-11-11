"use strict";

import { 
    validarObligatorio, 
    validarEmail, 
    validarDNI, 
    validarPasswordsIguales, 
    validarPassword 
} from './formulario-validaciones.mjs';

// Mapa de tipos de validación a funciones concretas
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

// Mensajes de error que se mostrarán según el tipo de validación
const mensajesError = {
    'obligatorio': 'Este campo es obligatorio y no puede estar vacío.',
    'email': 'Debe introducir un email válido que contenga una @.',
    'dni': 'El DNI debe tener el formato correcto (8 dígitos + letra).',
    'password': 'La contraseña no puede estar vacía.',
    'password-confirm': 'Las contraseñas deben ser iguales.'
};


/**
 * Presenta un mensaje de ayuda contextual en el contenedor de mensajes.
 * @param {string} mensaje Texto explicativo asociado al campo enfocado.
 * @returns {void}
 */
function mostrarAyuda(mensaje) {
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    divMensajes.className = 'ayuda';
    contenido.textContent = mensaje;
}

/**
 * Aplica estilos de error al campo y muestra un mensaje descriptivo.
 * @param {HTMLInputElement} campo Campo que ha fallado la validación.
 * @param {string} mensaje Mensaje de error que se mostrará.
 * @returns {void}
 */
function mostrarError(campo, mensaje) {
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    campo.classList.add('error');
    
    divMensajes.className = 'error';
    contenido.textContent = mensaje;
}

/**
 * Elimina los estilos de error del campo y limpia el cuadro de mensajes.
 * @param {HTMLInputElement} campo Campo a limpiar.
 * @returns {void}
 */
function limpiarError(campo) {
    campo.classList.remove('error');
    
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    divMensajes.className = '';
    contenido.textContent = '';
}

/**
 * Valida un campo individual basándose en el atributo data-validacion. Si la validación falla, muestra el mensaje correspondiente.
 * @param {HTMLInputElement} campo Campo a validar.
 * @returns {boolean} true si el campo es válido o no requiere validación; false si falla.
 */
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

/**
 * Valida todos los campos del formulario y gestiona casos especiales como la coincidencia de contraseñas. Enfoca el primer campo inválido.
 * @returns {boolean} true si todo el formulario pasa la validación; false en caso contrario.
 */
function validarFormulario() {
    const campos = document.querySelectorAll('input[data-validacion]');
    let todosValidos = true;
    let primerError = null;
    
    const password1 = document.getElementById('password1');
    const password2 = document.getElementById('password2');
    
    // Comprobación manual adicional para las contraseñas
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

/**
 * Restablece el formulario completo: borra valores, limpia errores y establece el foco en el primer campo (nombre) si existe.
 * @returns {void}
 */
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

/**
 * Configura los manejadores de eventos del formulario tras cargar el DOM. Incluye validación en blur, mensajes de ayuda en focus, envío y limpieza.
 * @returns {void}
 */
document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formularioCliente');
    const botonLimpiar = document.getElementById('limpiar');
    const campos = document.querySelectorAll('input[data-validacion]');
    
    // Eventos de focus/blur para mostrar ayuda y validar
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
    
    // Gestión del envío del formulario
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormulario()) {
            alert('Formulario enviado correctamente!');
        }
    });
    
    // Botón para limpiar todos los campos
    botonLimpiar.addEventListener('click', limpiarFormulario);
    
    // Foco inicial en el primer campo
    const primerCampo = document.getElementById('nombre');
    if (primerCampo) {
        primerCampo.focus();
    }
});