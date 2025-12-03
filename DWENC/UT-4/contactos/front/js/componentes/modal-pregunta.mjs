
export class ModalPregunta {

    /**
     * Inicializa el modal. Permite a partir de ahora mostrar mensajes
     */
    constructor() {
    }        

    /**
     * Muestra el cuadro de dialogo y acepta la entrada del usuario
     * 
     * @param {} titulo Titulo a mostrar en el cuadro de diálogo
     * @param {} cuerpo Mensaje a mostrar dentro del modal
     * @param {} onAceptar función callback a invocar cuando se pulse en aceptar
     */
    preguntar(titulo, cuerpo, onAceptar) {

        // Si el cuadro de diálogo está insertado en la página
        if($("#modal-pregunta").length) {
            this.#mostrarModal(titulo, cuerpo, onAceptar);
        } else {
            $('body').append(
                $('<div>').load(
                    getUrlComponenteHtml("modal-pregunta"),
                    () => this.#mostrarModal(titulo, cuerpo, onAceptar)
                )
            );                
        }        
    }

    /**
     * Mostramos el cuadro de diálogo modal
     * 
     * @param {*} titulo 
     * @param {*} cuerpo 
     */
    #mostrarModal(titulo, cuerpo, onAceptar) {
        
        // Asignamos el texto al título y el cuerpo
        $("#modal-pregunta .modal-title").text(titulo);
        $("#modal-pregunta .modal-body").text(cuerpo);        

        // Asignamos los eventos para gestionar la respuesta del usuario
        $("#modal-pregunta .btn-primary").on("click", () => {
                    
            // Desactiva el evento click del botón
            $("#modal-pregunta .btn-primary").off("click");

            // Oculta el cuadro de dialogo
            $('#modal-pregunta').modal('hide');

            // Invoca a la función para notificar que se ha aceptado el diálogo
            onAceptar();
        });


        // Muestra la ventana
        $('#modal-pregunta').modal('show');
    }
}