
export class Toast {

    /**
     * Inicializa el toast. Permite a partir de ahora mostrar mensajes
     */
    constructor() {

        // Añade al body un div con el contenido cargado desde el documento
        // html indicado
        $('body').append(
            $('<div>').load(
                getUrlComponenteHtml("toast")
            )
        );                
    }


    /**
     * Muestra un mensaje con el texto pasado como argumento
     * 
     * @param {*} texto 
     */
    mostrar(texto) {
        
        // Asignamos el texto al toast 
        $("#toast .toast-body").text(texto);

        // Muestra el toast
        $("#toast").toast("show");
    }

}