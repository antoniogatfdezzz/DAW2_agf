export class Buscador {
  
    #elementoContenedor;
    #onBuscar;    

    /**
     * Crea un paginador y lo configura
     * 
     * @param {*} elementoContenedor Elemento en el que se va a renderizar el paginador
     * @param {*} onBuscar Función para realizar una búsqueda. Recibirá como argumento el filtro
     */
    constructor(
        elementoContenedor,      
        onBuscar,
    ) {
        this.#elementoContenedor = elementoContenedor;
        this.#onBuscar = onBuscar;
    }

    /**
     * Renderiza el paginador
     */
    renderizar() {
     
        // Inserta el código HTML del paginador dentro del contenedor
        $(this.#elementoContenedor).load(
            getUrlComponenteHtml("buscador"), 
            () => {            
                
                // Asignamos los listeners
                $("#busBuscar").on("click", () => {

                    // Obtiene el texto a buscar
                    const filtro = $("#busFiltro").val();

                    // Realiza la búsqueda
                    this.#onBuscar(filtro)
                });
            }
        );
    }    
}