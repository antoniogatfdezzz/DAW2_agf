// Activa el modo estricto para aplicar reglas más estrictas del lenguaje
"use strict";

// Importa las funciones de validación desde el módulo de validaciones
import { 
    validarObligatorio, 
    validarEmail, 
    validarDNI, 
    validarPasswordsIguales, 
    validarPassword 
} from './formulario-validaciones.mjs';

// Mapa de tipos de validación a funciones concretas
const validaciones = {
    // Reglas simples que referencian funciones importadas
    'obligatorio': validarObligatorio,
    'email': validarEmail,
    'dni': validarDNI,
    'password': validarPassword,
    // Regla especial: compara contra el valor del campo con id 'password1'
    'password-confirm': (valor, elemento) => {
        // Obtiene el valor de la primera contraseña
        const password1 = document.getElementById('password1').value;
        // Verifica que coincidan y no estén vacías
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
    // Localiza el contenedor general de mensajes
    const divMensajes = document.getElementById('mensajes');
    // Localiza el nodo donde se inserta el texto
    const contenido = document.getElementById('mensaje-contenido');
    
    // Aplica la clase 'ayuda' para estilos informativos
    divMensajes.className = 'ayuda';
    // Inserta el texto de ayuda
    contenido.textContent = mensaje;
}

/**
 * Aplica estilos de error al campo y muestra un mensaje descriptivo.
 * @param {HTMLInputElement} campo Campo que ha fallado la validación.
 * @param {string} mensaje Mensaje de error que se mostrará.
 * @returns {void}
 */
function mostrarError(campo, mensaje) {
    // Contenedor de mensajes
    const divMensajes = document.getElementById('mensajes');
    // Nodo de texto del mensaje
    const contenido = document.getElementById('mensaje-contenido');
    
    // Marca el campo con la clase 'error'
    campo.classList.add('error');
    
    // Cambia el estilo del área de mensajes a modo error
    divMensajes.className = 'error';
    // Muestra el mensaje específico para la validación fallida
    contenido.textContent = mensaje;
}

/**
 * Elimina los estilos de error del campo y limpia el cuadro de mensajes.
 * @param {HTMLInputElement} campo Campo a limpiar.
 * @returns {void}
 */
function limpiarError(campo) {
    // Quita la clase 'error' del campo
    campo.classList.remove('error');
    
    // Recupera el contenedor y su contenido
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    
    // Elimina clases de estado y borra el texto de mensajes
    divMensajes.className = '';
    contenido.textContent = '';
}

/**
 * Valida un campo individual basándose en el atributo data-validacion. Si la validación falla, muestra el mensaje correspondiente.
 * @param {HTMLInputElement} campo Campo a validar.
 * @returns {boolean} true si el campo es válido o no requiere validación; false si falla.
 */
function validarCampo(campo) {
    // Lee el tipo de validación declarada en el atributo data-validacion
    const tipoValidacion = campo.getAttribute('data-validacion');
    // Toma el valor actual del campo
    const valor = campo.value;
    
    // Si no hay validación definida o no existe en el mapa, se considera válido
    if (!tipoValidacion || !validaciones[tipoValidacion]) {
        return true;
    }
    
    // Ejecuta la función de validación correspondiente
    const esValido = validaciones[tipoValidacion](valor, campo);
    
    // Si no valida, muestra el mensaje asociado
    if (!esValido) {
        // Recupera el texto de error apropiado
        const mensaje = mensajesError[tipoValidacion];
        // Muestra error visual y textual
        mostrarError(campo, mensaje);
        return false;
    } else {
        // Limpia cualquier estado de error si la validación es correcta
        limpiarError(campo);
        return true;
    }
}

/**
 * Valida todos los campos del formulario y gestiona casos especiales como la coincidencia de contraseñas. Enfoca el primer campo inválido.
 * @returns {boolean} true si todo el formulario pasa la validación; false en caso contrario.
 */
function validarFormulario() {
    // Selecciona todos los inputs con atributo data-validacion
    const campos = document.querySelectorAll('input[data-validacion]');
    // Bandera para saber si todo es válido
    let todosValidos = true;
    // Referencia al primer campo con error para enfocar al final
    let primerError = null;
    
    // Referencias a los campos de contraseña
    const password1 = document.getElementById('password1');
    const password2 = document.getElementById('password2');
    
    // Comprobación manual adicional para las contraseñas
    if (password1.value !== password2.value) {
        // Muestra error genérico de contraseñas no coincidentes
        mostrarError(password1, 'Las contraseñas deben ser iguales. Introduzca la contraseña en ambos campos nuevamente.');
        // Marca ambos campos como erróneos
        password1.classList.add('error');
        password2.classList.add('error');
        // Limpia los valores para forzar nueva introducción
        password1.value = '';
        password2.value = '';
        // Guarda el primer campo con error si no estaba establecido
        if (!primerError) primerError = password1;
        // Señala que el formulario no es válido
        todosValidos = false;
    }
    
    // Recorre cada campo aplicando su validación específica
    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            todosValidos = false;
            if (!primerError) {
                primerError = campo;
            }
        }
    });
    
    // Si hubo errores, lleva el foco al primer campo inválido
    if (!todosValidos && primerError) {
        primerError.focus();
    }
    
    // Devuelve el estado global de validación
    return todosValidos;
}

/**
 * Restablece el formulario completo: borra valores, limpia errores y establece el foco en el primer campo (nombre) si existe.
 * @returns {void}
 */
function limpiarFormulario() {
    // Obtiene el formulario por su id
    const formulario = document.getElementById('formularioCliente');
    // Selecciona todos los inputs del formulario
    const campos = formulario.querySelectorAll('input');
    
    // Recorre y limpia valores y estados de error
    campos.forEach(campo => {
        campo.value = '';
        limpiarError(campo);
    });
    
    // Limpia el área de mensajes
    const divMensajes = document.getElementById('mensajes');
    const contenido = document.getElementById('mensaje-contenido');
    divMensajes.className = '';
    contenido.textContent = '';
    
    // Devuelve el foco al primer campo lógico (nombre), si existe
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
    // Referencia al formulario principal
    const formulario = document.getElementById('formularioCliente');
    // Botón de limpiar
    const botonLimpiar = document.getElementById('limpiar');
    // Lista de campos con validación declarada
    const campos = document.querySelectorAll('input[data-validacion]');
    
    // Eventos de focus/blur para mostrar ayuda y validar
    campos.forEach(campo => {
        campo.addEventListener('focus', function() {
            // Lee el mensaje de ayuda del atributo data-ayuda
            const ayuda = this.getAttribute('data-ayuda');
            if (ayuda) {
                // Muestra el texto de ayuda contextual
                mostrarAyuda(ayuda);
            } else {
                // Si no hay ayuda, limpia el área de mensajes
                const divMensajes = document.getElementById('mensajes');
                const contenido = document.getElementById('mensaje-contenido');
                divMensajes.className = '';
                contenido.textContent = '';
            }
        });
        
        campo.addEventListener('blur', function() {
            // Valida el campo cuando pierde el foco
            validarCampo(this);
        });
    });
    
    // Gestión del envío del formulario
    formulario.addEventListener('submit', function(e) {
        // Evita el envío real para realizar validaciones en cliente
        e.preventDefault();
        
        if (validarFormulario()) {
            // Si todo es válido, informa al usuario (aquí podría enviarse al servidor)
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