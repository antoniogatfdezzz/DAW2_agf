
/**
 * Renderiza una tabla pasados los datos
 */
export class Tabla {

    // Url al origen de datos
    #url_recurso;

    // Elemento objetivo donde se va a renderizar la tabla. Debería
    // ser un elemento de tipo tbody
    #elementoObjetivo;
    
    // Plantilla para json2html
    #plantilla;

    // Página actual
    #pagina;

    // Número de registros por página
    #regPorPagina;

    // Filtro
    #filtroRegistro = null;

    // Última página
    #ultimaPagina = false;

    /**
     * Crea una tabla y la inicializa con los elementos requeridos
     * 
     * @param {*} url_recurso 
     * @param {*} elementoObjetivo 
     * @param {*} plantilla 
     */
    constructor(
        url_recurso, 
        elementoObjetivo, 
        plantilla, 
        pagina = TABLA_PRIMERA_PAGINA, 
        regPorPagina = TABLA_REGISTROS_POR_PAGINA) {

            this.#url_recurso = url_recurso;        
            this.#elementoObjetivo = elementoObjetivo;        
            this.#plantilla = plantilla;

            this.#pagina = pagina;
            this.#regPorPagina = regPorPagina;
    }

    /**
     * Renderiza la tabla en el elemento objetivo
     */
    renderizar() {
        // Carga los contactos
        fetch(this.#urlRecurso)
            .then(response => response.json())
            .then(datos => {              

                // Comprobar si es ultima página
                this.#ultimaPagina = this.#esUltimaPagina(datos);

                // Genera el HTML
                const html = json2html.render(datos, this.#plantilla);

                // Asigna el contenido 
                $(this.#elementoObjetivo).html(html);
            });  
    }

    //-----------------------------------------------------------
    // Funciones de navegación por la tabla
    //-----------------------------------------------------------

    /**
     * Pasa a la siguiente página de la tabla
     */
    navegarPaginaSiguiente() {

        // Pasa a la página siguiente
        this.#pagina++;

        // Refresca la tabla
        this.renderizar();
    }

    /**
     * Pasa a la página anterior
     */
    navegarPaginaAnterior() {

        if(this.#pagina > 1) {

            // Pasa a la página anterior
            this.#pagina--;

            // Refresca la tabla
            this.renderizar();
        }
    }

    /**
     * Añade un filtro a la tabla para búsqueda de texto completa
     */
    añadirFiltro(filtro) {

        // Asigna el filtro
        this.#filtroRegistro = (filtro && filtro.length > 0)?filtro:null;

        // Lo movemos a la página 1
        this.#pagina = TABLA_PRIMERA_PAGINA;

        // Renderiza el nuevo resultado
        this.renderizar();
    }

    //--------------------------------------------------------
    // Funciones de utilidad
    //--------------------------------------------------------
    
    /**
     * Calcula la URL del recurso añadiendo información de paginación, filtros,
     * etc.
     */
    get #urlRecurso() {

        const filtro = (this.#filtroRegistro != null)?`&q=${this.#filtroRegistro}`:"";

        return `${this.#url_recurso}?_page=${this.#pagina}&_limit=${this.#regPorPagina}${filtro}`
    }

    #esUltimaPagina(datos) {
        return datos.length < this.#regPorPagina;
    }
}


