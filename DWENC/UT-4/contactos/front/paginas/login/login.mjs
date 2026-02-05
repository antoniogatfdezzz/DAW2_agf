//--------------------------------------------------------------
// Dependencias
//--------------------------------------------------------------
import * as moduloToast from "../../js/componentes/toast.mjs";
import * as http from "../../js/lib/http.mjs";
import * as moduloModalMensaje from "../../js/componentes/modal-mensaje.mjs";

//--------------------------------------------------------------
// Constantes
//--------------------------------------------------------------
const TOAST = new moduloToast.Toast();
const MODAL_MENSAJE = new moduloModalMensaje.ModalMensaje();


//--------------------------------------------------------------
// Inicialización
//--------------------------------------------------------------
$(document).ready(() => {   

    $("#frmlogin").on("submit", onLogin);
});

//--------------------------------------------------
// Eventos
//-------------------------------------------------

/**
 * Evento invocado cuando se hace login
 */
function onLogin(e) {
    // Evita que se envíe el form
    e.preventDefault();

    // Obtiene los valores de usuario y contraseña
    const login = $("[name=login]").val();
    const pass = $("[name=password]").val();

    // Crea el objeto para hacer login
    const objeto = {
        email: login,
        password : pass
    };

    // Envia la petición de login al servidor
    http.post(URL_LOGIN, objeto)
    .then(respuesta => { 
        
        // Comprueba si la respuesta es correcta
        if(respuesta.ok) {
               respuesta.json().then(resultado => {

                    const token = resultado.accessToken;

                    // Almacena el token en el almacenamiento local
                    localStorage.setItem("jwtToken", token);

                    // Redije a la página de contactos
                    window.location = "/paginas/contactos/contactos.html";
               });
        } else {
            respuesta.text().then(mensaje => {
                TOAST.mostrar("Error de autenticación: "+mensaje);
            })            
        }
    })    
    .catch(() => {
        // Detectamos cuando el servidor no esté disponible
        MODAL_MENSAJE.mostrarMensaje("Atención!!!", "El servidor no está disponible en estos momentos. Intentalo de nuevo en unso minutos");
    });
;
    
}




//--------------------------------------------------------------
// Funciones de utilidad
//--------------------------------------------------------------

