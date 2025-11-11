import { 
    ejecutarValidacion, 
    obtenerMensajeError 
} from './formulario-validaciones.mjs';

// Mapa de ayudas para cada campo del formulario
const ayudasCampos = {
    'nombre': 'Introduce tu nombre',
    'apellidos': 'Introduce tus apellidos completos',
    'dni': 'Introduce tu DNI con letra (ejemplo: 12345678Z)',
    'email': 'Introduce tu dirección de correo electrónico',
    'password1': 'Introduce una contraseña segura',
    'password2': 'Vuelve a introducir la misma contraseña'
};

// Mapa de validaciones para cada campo del formulario
const validacionesCampos = {
    'nombre': 'obligatorio',
    'apellidos': 'obligatorio',
    'dni': 'dni',
    'email': 'email',
    'password1': 'obligatorio',
    'password2': 'passwordMatch'
};

/**
 * Muestra un mensaje de ayuda contextual en el contenedor de ayuda.
 * @param {string} mensaje Texto a mostrar al usuario.
 * @returns {void}
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
 * Marca el campo con error y muestra un mensaje descriptivo en el área de ayuda.
 * @param {JQuery<HTMLInputElement>} $campo Campo que ha fallado la validación.
 * @param {string} mensajeError Mensaje a mostrar.
 * @returns {void}
 */
function mostrarError($campo, mensajeError) {
    const $ayudaContainer = $('#ayuda-container');
    const $ayudaTexto = $('#ayuda-texto');
    
    $campo.addClass('error');
    
    $ayudaContainer.addClass('error');
    $ayudaTexto.addClass('error');
    
    $ayudaTexto.text(mensajeError);
}

/**
 * Elimina los estilos de error del campo y limpia el texto de ayuda.
 * @param {JQuery<HTMLInputElement>} $campo Campo a limpiar.
 * @returns {void}
 */
function limpiarError($campo) {
    const $ayudaContainer = $('#ayuda-container');
    const $ayudaTexto = $('#ayuda-texto');
    
    $campo.removeClass('error');
    
    $ayudaContainer.removeClass('error');
    $ayudaTexto.removeClass('error');
    
    $ayudaTexto.text('');
}

/**
 * Valida un campo individual basado en data-validacion o el mapa por id. Si falla, muestra el mensaje de error correspondiente.
 * @param {JQuery<HTMLInputElement>} $campo Campo a validar.
 * @returns {boolean} true si es válido o no requiere validación; false si falla.
 */
function validarCampo($campo) {
    const idCampo = $campo.attr('id');
    const valor = $campo.val();
    
    let tipoValidacion = $campo.attr('data-validacion');
    
    if (!tipoValidacion) {
        tipoValidacion = validacionesCampos[idCampo];
    }
    
    if (!tipoValidacion) {
        return true;
    }
    
    const esValido = ejecutarValidacion(tipoValidacion, valor, $campo);
    
    if (!esValido) {
        const mensajeError = obtenerMensajeError(tipoValidacion);
        mostrarError($campo, mensajeError);
        return false;
    } else {
        limpiarError($campo);
        return true;
    }
}

/**
 * Valida el formulario completo recorriendo los campos con data-validacion y comprobando la coincidencia de contraseñas.
 * @returns {{valido:boolean, primerCampoError:JQuery<HTMLInputElement>|null}}
 */
function validarFormulario() {
    let formularioValido = true;
    let primerCampoError = null;
    
    const $campos = $('input[data-validacion]');
    
    $campos.each(function() {
        const $campo = $(this);
        const esValido = validarCampo($campo);
        
        if (!esValido) {
            formularioValido = false;
            
            if (primerCampoError === null) {
                primerCampoError = $campo;
            }
        }
    });
    
    const password1 = $('#password1').val();
    const password2 = $('#password2').val();
    
    if (password1 !== password2 || password1.length === 0) {
        formularioValido = false;
        
        const $password1 = $('#password1');
        const $password2 = $('#password2');
        
        $password1.addClass('error');
        $password2.addClass('error');
        
        mostrarError($password1, 'Las contraseñas no coinciden. Debes introducir la misma contraseña en ambos campos');
        
        if (primerCampoError === null) {
            primerCampoError = $password1;
        }
        
        $password1.val('');
        $password2.val('');
    }
    
    return {
        valido: formularioValido,
        primerCampoError: primerCampoError
    };
}

/**
 * Restablece el formulario a su estado inicial, elimina errores y enfoca el campo nombre.
 * @returns {void}
 */
function limpiarFormulario() {
    $('#formulario-cliente')[0].reset();
    
    $('input').removeClass('error');
    
    mostrarAyuda('');
    
    $('#nombre').focus();
}

/**
 * Configura todos los manejadores de eventos del formulario: focus/blur, submit y limpiar. También establece el foco inicial.
 * @returns {void}
 */
function inicializarEventos() {
    $('input').on('focus', function() {
        const $campo = $(this);
        const idCampo = $campo.attr('id');
        
        limpiarError($campo);
        
        let textoAyuda = $campo.attr('data-ayuda');
        
        if (!textoAyuda) {
            textoAyuda = ayudasCampos[idCampo];
        }
        
        mostrarAyuda(textoAyuda || '');
    });
    
    $('input').on('blur', function() {
        const $campo = $(this);
        validarCampo($campo);
    });
    
    $('#formulario-cliente').on('submit', function(e) {
        e.preventDefault();
        
        const resultado = validarFormulario();
        
        if (!resultado.valido) {
            if (resultado.primerCampoError) {
                resultado.primerCampoError.focus();
            }
        } else {
            alert('¡Formulario enviado correctamente!\n\nDatos:\n' +
                'Nombre: ' + $('#nombre').val() + '\n' +
                'Apellidos: ' + $('#apellidos').val() + '\n' +
                'DNI: ' + $('#dni').val() + '\n' +
                'Email: ' + $('#email').val()
            );
            
            limpiarFormulario();
        }
    });
    
    $('#btn-limpiar').on('click', function() {
        limpiarFormulario();
    });
}

$(document).ready(function() {
    inicializarEventos();
    
    $('#nombre').focus();
});
