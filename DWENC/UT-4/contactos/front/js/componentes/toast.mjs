
export class Toast {

    /**
     * Inicializa el toast. Permite a partir de ahora mostrar mensajes
     */
    constructor() {
    }


    /**
     * Muestra un mensaje con el texto pasado como argumento
     * 
     * @param {*} texto 
     */
    mostrar(texto) {
        
        // Si el toast está insertado en la página
        if($("#toast").length) {
            this.#mostrarToast(texto);
        } else {
            $('body').append(
                $('<div>').load(
                    getUrlComponenteHtml("toast"),
                () => this.#mostrarToast(texto)
                )
            );                
        }

    }

    #mostrarToast(mensaje) {
        // Asignamos el texto al toast 
        $("#toast .toast-body").text(mensaje);

        // Muestra el toast
        $("#toast").toast("show");        
    }

}