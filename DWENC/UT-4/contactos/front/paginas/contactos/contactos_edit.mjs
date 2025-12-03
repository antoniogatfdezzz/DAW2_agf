//--------------------------------------------------------------
// Dependencias
//--------------------------------------------------------------
import * as http from "../../js/lib/http.mjs";
import * as formularioModule from "../../js/lib/formulario.mjs";
import * as toastModule from "../../js/componentes/toast.mjs";

//--------------------------------------------------------------
// Constantes
//--------------------------------------------------------------
const toast = new toastModule.Toast();

//--------------------------------------------------------------
// Inicialización
//--------------------------------------------------------------
$(document).ready(() => {   
    
    // Evento para volver a la página con el listado de contactos
    $("#btnVolver").on("click", () => window.location = "contactos.html");

    // Inicializamos el formulario
    const formulario = new formularioModule.Formulario(
        "#formularioContactos",
        onContactoSubmit
    );
});


//--------------------------------------------------
// Eventos
//-------------------------------------------------
function onContactoSubmit(contacto) {
    crearContacto(contacto);
}

//--------------------------------------------------------------
// Funciones de utilidad
//--------------------------------------------------------------

/**
 * Almacena el contacto en el servidor
 */
function crearContacto(contacto) {
    
    // Muestra el contacto que se va a crear
    console.debug(contacto);

    // Guarda el contacto
    http.post(URL_CONTACTOS, contacto)
    .then(() => {
        toast.mostrar("Se ha creado el contacto correctamente");        
    });
}

  

