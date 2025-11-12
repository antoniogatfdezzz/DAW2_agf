// Importa funciones de validación y de obtención de mensajes de error
import { 
    ejecutarValidacion, 
    obtenerMensajeError 
} from './formulario-validaciones.mjs';

// Mapa de ayudas para cada campo del formulario
const ayudasCampos = {
    'nombre': 'Introduce tu nombre', // ayuda para el campo nombre
    'apellidos': 'Introduce tus apellidos completos', // ayuda apellidos
    'dni': 'Introduce tu DNI con letra (ejemplo: 12345678Z)', // ayuda DNI
    'email': 'Introduce tu dirección de correo electrónico', // ayuda email
    'password1': 'Introduce una contraseña segura', // ayuda primera contraseña
    'password2': 'Vuelve a introducir la misma contraseña' // ayuda confirmación contraseña
};

// Mapa de validaciones para cada campo del formulario
const validacionesCampos = {
    'nombre': 'obligatorio', // no vacío
    'apellidos': 'obligatorio', // no vacío
    'dni': 'dni', // formato DNI y letra
    'email': 'email', // formato email
    'password1': 'obligatorio', // no vacío
    'password2': 'passwordMatch' // debe coincidir con password1
};

/**
 * Muestra un mensaje de ayuda contextual en el contenedor de ayuda.
 * @param {string} mensaje Texto a mostrar al usuario.
 * @returns {void}
 */
function mostrarAyuda(mensaje) {
    const $ayudaContainer = $('#ayuda-container'); // contenedor visual de ayuda
    const $ayudaTexto = $('#ayuda-texto'); // elemento donde se muestra el texto
    
    // Limpiar clase de error si existe
    $ayudaContainer.removeClass('error'); // quitar modo error del contenedor
    $ayudaTexto.removeClass('error'); // quitar estilo error del texto
    
    // Si el mensaje está vacío, limpiar el contenido
    if (!mensaje || mensaje.trim() === '') {
        $ayudaTexto.text(''); // no mostrar nada si no hay mensaje
        return; // salir sin más cambios
    }
    
    // Mostrar el mensaje de ayuda
    $ayudaTexto.text(mensaje); // inyectar texto de ayuda
}

/**
 * Marca el campo con error y muestra un mensaje descriptivo en el área de ayuda.
 * @param {JQuery<HTMLInputElement>} $campo Campo que ha fallado la validación.
 * @param {string} mensajeError Mensaje a mostrar.
 * @returns {void}
 */
function mostrarError($campo, mensajeError) {
    const $ayudaContainer = $('#ayuda-container'); // contenedor de ayuda
    const $ayudaTexto = $('#ayuda-texto'); // texto de ayuda
    
    $campo.addClass('error'); // aplica estilo de error al campo
    
    $ayudaContainer.addClass('error'); // activa modo error en contenedor
    $ayudaTexto.addClass('error'); // activa estilo de error en el texto
    
    $ayudaTexto.text(mensajeError); // muestra el mensaje correspondiente
}

/**
 * Elimina los estilos de error del campo y limpia el texto de ayuda.
 * @param {JQuery<HTMLInputElement>} $campo Campo a limpiar.
 * @returns {void}
 */
function limpiarError($campo) {
    const $ayudaContainer = $('#ayuda-container'); // contenedor de ayuda
    const $ayudaTexto = $('#ayuda-texto'); // texto de ayuda
    
    $campo.removeClass('error'); // quita la clase de error del campo
    
    $ayudaContainer.removeClass('error'); // desactiva el modo error
    $ayudaTexto.removeClass('error'); // quita estilo de error
    
    $ayudaTexto.text(''); // limpia el texto de ayuda
}

/**
 * Valida un campo individual basado en data-validacion o el mapa por id. Si falla, muestra el mensaje de error correspondiente.
 * @param {JQuery<HTMLInputElement>} $campo Campo a validar.
 * @returns {boolean} true si es válido o no requiere validación; false si falla.
 */
function validarCampo($campo) {
    const idCampo = $campo.attr('id'); // id del input
    const valor = $campo.val(); // valor actual del campo
    
    let tipoValidacion = $campo.attr('data-validacion'); // prioridad: atributo data-validacion
    
    if (!tipoValidacion) {
        tipoValidacion = validacionesCampos[idCampo]; // fallback al mapa por id
    }
    
    if (!tipoValidacion) {
        return true; // si no hay regla, se considera válido
    }
    
    const esValido = ejecutarValidacion(tipoValidacion, valor, $campo); // ejecuta la regla
    
    if (!esValido) {
        const mensajeError = obtenerMensajeError(tipoValidacion); // obtiene mensaje por tipo
        mostrarError($campo, mensajeError); // muestra error y estilos
        return false; // indica fallo
    } else {
        limpiarError($campo); // limpia posibles errores previos
        return true; // indica éxito
    }
}

/**
 * Valida el formulario completo recorriendo los campos con data-validacion y comprobando la coincidencia de contraseñas.
 * @returns {{valido:boolean, primerCampoError:JQuery<HTMLInputElement>|null}}
 */
function validarFormulario() {
    let formularioValido = true; // estado global de validez
    let primerCampoError = null; // para enfocar el primer error
    
    const $campos = $('input[data-validacion]'); // inputs con reglas de validación
    
    $campos.each(function() {
        const $campo = $(this); // convierte el elemento actual a jQuery
        const esValido = validarCampo($campo); // valida cada campo
        
        if (!esValido) {
            formularioValido = false; // marca formulario como inválido
            
            if (primerCampoError === null) {
                primerCampoError = $campo; // guarda el primero que falla
            }
        }
    });
    
    const password1 = $('#password1').val(); // valor de primera contraseña
    const password2 = $('#password2').val(); // valor de confirmación
    
    if (password1 !== password2 || password1.length === 0) {
        formularioValido = false; // regla extra: ambas deben coincidir y no estar vacías
        
        const $password1 = $('#password1'); // cachea jQuery objects
        const $password2 = $('#password2');
        
        $password1.addClass('error'); // marca campo 1 con error
        $password2.addClass('error'); // marca campo 2 con error
        
        mostrarError($password1, 'Las contraseñas no coinciden. Debes introducir la misma contraseña en ambos campos'); // muestra mensaje
        
        if (primerCampoError === null) {
            primerCampoError = $password1; // establece foco en el primer campo de password
        }
        
        $password1.val(''); // limpia ambos campos para reintroducción
        $password2.val('');
    }
    
    return {
        valido: formularioValido, // estado final
        primerCampoError: primerCampoError // referencia para enfocar
    };
}

/**
 * Restablece el formulario a su estado inicial, elimina errores y enfoca el campo nombre.
 * @returns {void}
 */
function limpiarFormulario() {
    $('#formulario-cliente')[0].reset(); // resetea el formulario nativo (primer nodo del jQuery set)
    
    $('input').removeClass('error'); // elimina clases de error de todos los inputs
    
    mostrarAyuda(''); // limpia el área de ayuda
    
    $('#nombre').focus(); // vuelve a enfocar el primer campo
}

/**
 * Configura todos los manejadores de eventos del formulario: focus/blur, submit y limpiar. También establece el foco inicial.
 * @returns {void}
 */
function inicializarEventos() {
    $('input').on('focus', function() { // al enfocar cualquier input
        const $campo = $(this); // referencia al input enfocado
        const idCampo = $campo.attr('id'); // id del input
        
        limpiarError($campo); // quita errores previos del campo
        
        let textoAyuda = $campo.attr('data-ayuda'); // intenta leer ayuda del data-* del input
        
        if (!textoAyuda) {
            textoAyuda = ayudasCampos[idCampo]; // si no hay, usa el mapa por id
        }
        
        mostrarAyuda(textoAyuda || ''); // pinta la ayuda contextual
    });
    
    $('input').on('blur', function() { // al perder el foco
        const $campo = $(this); // referencia al input
        validarCampo($campo); // valida el campo
    });
    
    $('#formulario-cliente').on('submit', function(e) { // al enviar el formulario
        e.preventDefault(); // evita envío real para validar primero
        
        const resultado = validarFormulario(); // valida todo el formulario
        
        if (!resultado.valido) { // si hay errores
            if (resultado.primerCampoError) {
                resultado.primerCampoError.focus(); // enfoca el primero con error
            }
        } else {
            alert('¡Formulario enviado correctamente!\n\nDatos:\n' + // muestra resumen
                'Nombre: ' + $('#nombre').val() + '\n' +
                'Apellidos: ' + $('#apellidos').val() + '\n' +
                'DNI: ' + $('#dni').val() + '\n' +
                'Email: ' + $('#email').val()
            );
            
            limpiarFormulario(); // limpia tras el envío exitoso
        }
    });
    
    $('#btn-limpiar').on('click', function() { // botón de limpiar
        limpiarFormulario(); // restablece todo
    });
}

// Ejecuta la inicialización cuando el DOM esté listo
$(document).ready(function() {
    inicializarEventos(); // configura manejadores
    
    $('#nombre').focus(); // pone el foco inicial en nombre
});
