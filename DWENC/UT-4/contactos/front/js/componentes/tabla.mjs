
/**
 * Renderiza una tabla pasados los datos
 */
export class Tabla {

    #url_recurso;
    #elementoObjetivo;
    #plantilla;

    /**
     * Crea una tabla y la inicializa con los elementos requeridos
     * 
     * @param {*} url_recurso 
     * @param {*} elementoObjetivo 
     * @param {*} plantilla 
     */
    constructor(url_recurso, elementoObjetivo, plantilla) {
        this.#url_recurso = url_recurso;
        this.#elementoObjetivo = elementoObjetivo;
        this.#plantilla = plantilla;
    }

    /**
     * Renderiza la tabla en el elemento objetivo
     */
    renderizar() {
        // Carga los contactos
        fetch(this.#url_recurso)
            .then(response => response.json())
            .then(datos => {              
                const html = json2html.render(datos, this.#plantilla);

                $(this.#elementoObjetivo).html(html);
            });  
    }
}


