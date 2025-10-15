/**
 * Módulo principal del formulario de cliente
 * Utiliza jQuery para todas las operaciones con el DOM
 */

import { 
    ejecutarValidacion, 
    obtenerMensajeError 
} from './formulario-validaciones.mjs';

/**
 * Mapa de ayudas para cada campo del formulario
 * Asocia el id del campo con el texto de ayuda
 */
const ayudasCampos = {
    'nombre': 'Introduce tu nombre',
    'apellidos': 'Introduce tus apellidos completos',
    'dni': 'Introduce tu DNI con letra (ejemplo: 12345678Z)',
    'email': 'Introduce tu dirección de correo electrónico',
    'password1': 'Introduce una contraseña segura',
    'password2': 'Vuelve a introducir la misma contraseña'
};

/**
 * Mapa de validaciones para cada campo del formulario
 * Asocia el id del campo con el tipo de validación
 */
const validacionesCampos = {
    'nombre': 'obligatorio',
    'apellidos': 'obligatorio',
    'dni': 'dni',
    'email': 'email',
    'password1': 'obligatorio',
    'password2': 'passwordMatch'
};

/**
 * Muestra un mensaje de ayuda en el div de ayuda
 * @param {string} mensaje - El mensaje a mostrar
 */
function mostrarAyuda(mensaje) {
    const $ayudaContainer = $('#ayuda-container');
    const $ayudaTexto = $('#ayuda-texto');
    
    // Limpiar clase de error si existe
    $ayudaContainer.removeClass('error');
    $ayudaTexto.removeClass('error');
    
    // Si el mensaje está vacío, limpiar el contenido
    if (!mensaje || mensaje.trim() === '') {
        $ayudaTexto.text('');
        return;
    }
    
    // Mostrar el mensaje de ayuda
    $ayudaTexto.text(mensaje);
}

/**
 * Muestra un mensaje de error para un campo específico
 * @param {jQuery} $campo - El campo jQuery que tiene el error
 * @param {string} mensajeError - El mensaje de error a mostrar
 */
function mostrarError($campo, mensajeError) {
    const $ayudaContainer = $('#ayuda-container');
    const $ayudaTexto = $('#ayuda-texto');
    
    // Agregar clase de error al campo
    $campo.addClass('error');
    
    // Agregar clase de error al contenedor de ayuda
    $ayudaContainer.addClass('error');
    $ayudaTexto.addClass('error');
    
    // Mostrar el mensaje de error
    $ayudaTexto.text(mensajeError);
}

/**
 * Limpia el mensaje de error de un campo
 * @param {jQuery} $campo - El campo jQuery del que limpiar el error
 */
function limpiarError($campo) {
    const $ayudaContainer = $('#ayuda-container');
    const $ayudaTexto = $('#ayuda-texto');
    
    // Quitar clase de error del campo
    $campo.removeClass('error');
    
    // Quitar clase de error del contenedor de ayuda
    $ayudaContainer.removeClass('error');
    $ayudaTexto.removeClass('error');
    
    // Limpiar el texto de error
    $ayudaTexto.text('');
}

/**
 * Valida un campo individual
 * @param {jQuery} $campo - El campo a validar
 * @returns {boolean} - true si es válido, false si no lo es
 */
function validarCampo($campo) {
    const idCampo = $campo.attr('id');
    const valor = $campo.val();
    
    // Obtener el tipo de validación desde el atributo data-validacion
    let tipoValidacion = $campo.attr('data-validacion');
    
    // Si no tiene atributo data-validacion, buscar en el mapa
    if (!tipoValidacion) {
        tipoValidacion = validacionesCampos[idCampo];
    }
    
    // Si no hay tipo de validación, el campo es válido
    if (!tipoValidacion) {
        return true;
    }
    
    // Ejecutar la validación
    const esValido = ejecutarValidacion(tipoValidacion, valor, $campo);
    
    if (!esValido) {
        // Mostrar el error
        const mensajeError = obtenerMensajeError(tipoValidacion);
        mostrarError($campo, mensajeError);
        return false;
    } else {
        // Limpiar el error si existía
        limpiarError($campo);
        return true;
    }
}

/**
 * Valida todos los campos del formulario
 * @returns {Object} - {valido: boolean, primerCampoError: jQuery|null}
 */
function validarFormulario() {
    let formularioValido = true;
    let primerCampoError = null;
    
    // Obtener todos los campos con validación
    const $campos = $('input[data-validacion]');
    
    // Validar cada campo
    $campos.each(function() {
        const $campo = $(this);
        const esValido = validarCampo($campo);
        
        if (!esValido) {
            formularioValido = false;
            
            // Guardar el primer campo con error
            if (primerCampoError === null) {
                primerCampoError = $campo;
            }
        }
    });
    
    // Validación especial: contraseñas iguales
    const password1 = $('#password1').val();
    const password2 = $('#password2').val();
    
    if (password1 !== password2 || password1.length === 0) {
        formularioValido = false;
        
        // Marcar error en password1 y limpiar ambos campos
        const $password1 = $('#password1');
        const $password2 = $('#password2');
        
        $password1.addClass('error');
        $password2.addClass('error');
        
        mostrarError($password1, 'Las contraseñas no coinciden. Debes introducir la misma contraseña en ambos campos');
        
        // Si no hay primer campo con error, poner el foco en password1
        if (primerCampoError === null) {
            primerCampoError = $password1;
        }
        
        // Limpiar los campos de contraseña
        $password1.val('');
        $password2.val('');
    }
    
    return {
        valido: formularioValido,
        primerCampoError: primerCampoError
    };
}

/**
 * Limpia todo el formulario
 */
function limpiarFormulario() {
    // Limpiar todos los campos
    $('#formulario-cliente')[0].reset();
    
    // Limpiar todos los errores
    $('input').removeClass('error');
    
    // Limpiar el mensaje de ayuda/error
    mostrarAyuda('');
    
    // Poner el foco en el primer campo
    $('#nombre').focus();
}

/**
 * Inicializa los eventos del formulario
 */
function inicializarEventos() {
    // Evento focus: mostrar ayuda al entrar en un campo
    $('input').on('focus', function() {
        const $campo = $(this);
        const idCampo = $campo.attr('id');
        
        // Limpiar error si existía
        limpiarError($campo);
        
        // Obtener el texto de ayuda desde el atributo data-ayuda
        let textoAyuda = $campo.attr('data-ayuda');
        
        // Si no tiene atributo data-ayuda, buscar en el mapa
        if (!textoAyuda) {
            textoAyuda = ayudasCampos[idCampo];
        }
        
        // Mostrar la ayuda (o limpiar si no hay ayuda)
        mostrarAyuda(textoAyuda || '');
    });
    
    // Evento blur: validar al salir de un campo
    $('input').on('blur', function() {
        const $campo = $(this);
        validarCampo($campo);
    });
    
    // Evento submit: validar el formulario completo
    $('#formulario-cliente').on('submit', function(e) {
        e.preventDefault();
        
        const resultado = validarFormulario();
        
        if (!resultado.valido) {
            // Si hay errores, poner el foco en el primer campo con error
            if (resultado.primerCampoError) {
                resultado.primerCampoError.focus();
            }
        } else {
            // Si todo es válido, mostrar mensaje de éxito
            alert('¡Formulario enviado correctamente!\n\nDatos:\n' +
                'Nombre: ' + $('#nombre').val() + '\n' +
                'Apellidos: ' + $('#apellidos').val() + '\n' +
                'DNI: ' + $('#dni').val() + '\n' +
                'Email: ' + $('#email').val()
            );
            
            // Limpiar el formulario después del envío exitoso
            limpiarFormulario();
        }
    });
    
    // Evento click del botón limpiar
    $('#btn-limpiar').on('click', function() {
        limpiarFormulario();
    });
}

/**
 * Inicialización cuando el DOM está listo
 */
$(document).ready(function() {
    inicializarEventos();
    
    // Poner el foco en el primer campo al cargar
    $('#nombre').focus();
});
