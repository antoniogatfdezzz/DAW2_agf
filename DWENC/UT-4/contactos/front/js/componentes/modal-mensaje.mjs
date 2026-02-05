
export class ModalMensaje {

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
     */
    mostrarMensaje(titulo, cuerpo) {

        // Si el cuadro de diálogo está insertado en la página
        if($("#modal-mensaje").length) {
            this.#mostrarModal(titulo, cuerpo);
        } else {
            $('body').append(
                $('<div>').load(
                    getUrlComponenteHtml("modal-mensaje"),
                    () => this.#mostrarModal(titulo, cuerpo)
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
    #mostrarModal(titulo, cuerpo) {
        
        // Asignamos el texto al título y el cuerpo
        $("#modal-mensaje .modal-title").text(titulo);
        $("#modal-mensaje .modal-body").text(cuerpo);        

        // Muestra la ventana
        $('#modal-mensaje').modal('show');
    }
}