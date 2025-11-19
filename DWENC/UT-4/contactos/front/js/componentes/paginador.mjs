
export class Paginador {

    #elementoContenedor;
    #onPaginaSiguienteListener;
    #onPaginaAnteriorListener;

    /**
     * Crea un paginador y lo configura
     * 
     * @param {*} elementoContenedor Elemento en el que se va a renderizar el paginador
     * @param {*} onPaginaSiguienteListener Función invocada en caso de página siguiente
     * @param {*} onPaginaAnteriorListener Función invocada en caso de pulsar sobre página anterior
     */
    constructor(
        elementoContenedor,      
        onPaginaSiguienteListener,
        onPaginaAnteriorListener
    ) {
        this.#elementoContenedor = elementoContenedor;
        this.#onPaginaAnteriorListener = onPaginaAnteriorListener;
        this.#onPaginaSiguienteListener = onPaginaSiguienteListener;
    }

    /**
     * Renderiza el paginador
     */
    renderizar() {
     
        // Inserta el código HTML del paginador dentro del contenedor
        $(this.#elementoContenedor).load(
            getUrlComponenteHtml("paginador"), 
            () => {            
                
                // Asignamos los listeners
                $("#pagPaginaAnterior").on("click", this.#onPaginaAnteriorListener);
                $("#pagPaginaSiguiente").on("click", this.#onPaginaSiguienteListener);
            }
        );
    }
}