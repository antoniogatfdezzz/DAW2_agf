//--------------------------------------------------------------
// Dependencias
//--------------------------------------------------------------
import * as validaciones from "./validaciones.mjs";

/**
 * La clase formulario implementa el control del formulario, las validaciones
 * y la gestión del envío de datos al servidor
 */
export class Formulario {


    //-----------------------------------------------
    // Variables globales
    //-----------------------------------------------
    #elementoFormulario;
    #onSubmit;
    
    //-----------------------------------------------
    // Inicialización del formulario
    //-----------------------------------------------
    
    /**
     * 
     * @param {*} elementoFormulario 
     * @param {*} elementoAyuda 
     * @param {*} onSubmit función que recibirá como parámetro un objeto con los datos en el formulario
     */
    constructor(elementoFormulario, onSubmit) {

        // Obtiene referencia a los elementos que necesita
        this.#elementoFormulario = $(elementoFormulario);
        this.#onSubmit = onSubmit;

        // Asigna los eventos
        this.#inicializarEventosFormulario(this.#elementoFormulario);        
    }

    //--------------------------------------------------
    // Eventos
    //-------------------------------------------------

    /**
     * Inicializa los eventos necesarios para procesar el formulario
     * 
     * @param {*} formulario 
     */
    #inicializarEventosFormulario(formulario) {

        // Gestión del evento submit
        $(formulario).on("submit", (e) => this.#onFormularioSubmit(e));

        // Gestión de la ayuda de los campos
        const camposAyuda = formulario.find("[data-ayuda]");
        camposAyuda.each((indice, elemento) => {
            
            elemento = $(elemento);

            // Asigna evento para mostrar la ayuda
            elemento.on("focusin", (e) => this.#onInputFocusIn(e));
            elemento.on("focusout", (e) => this.#onInputFocusOut(e));
        });

        // Asigna el evento para los campos que hay que validar
        const camposValidar = formulario.find("[data-validacion]");
        camposValidar.each((indice, elemento) => {
            
            $(elemento).on("change", (e) => this.#onInputChange(e));
        });
    }

    /**
     * Gestiona el evento submit del formulario
     * 
     * @param {*} evento 
     */
    #onFormularioSubmit(evento) {

        // El formulario no puede enviarse
        evento.preventDefault();

        // Obtener los campos que tenemos que validar
        const camposValidar = $(this.#elementoFormulario.find("[data-validacion]"));

        // Validar los campos que tienen validaciones
        const errores = this.#validarCampos(camposValidar);

        // Crea un objeto con los datos en el formulario
        const datos = {};
        $(this.#elementoFormulario).find('[name]').each((i, e) => {
            datos[e.name] = e.value;
        });

        // Si no hay errores se envía
        if(!errores) {
            this.#onSubmit(datos);
        }
    }

    /**
     * Cuando se entra en un campo se invoca a este método 
     * 
     * @param {*} evento 
     */
    #onInputFocusIn(evento) {
        
        // Obtengo el campo
        const campo = $(evento.target);

        // Muestra la ayuda del campo
        this.#mostrarAyudaCampo(campo);
    }

    /**
     * Gestiona el evento focus out. 
     * 
     * @param {*} evento 
     */
    #onInputFocusOut(evento) {

        // Obtengo el campo
        const campo = $(evento.target);
        
        // Limpia el mensaje de ayuda si lo hubiera
        this.#limpiarAyudaCampo(campo);    
    }

    /**
     * Invocado cuando hay cambios
     * 
     * @param {*} evento 
     */
    #onInputChange(evento) {
        
        // Obtengo el campo
        const campo = $(evento.target);
        
        // Valida el campo
        const resultado = this.#validarCampo(campo);
    }


    //--------------------------------------------------------------
    // Funciones de utilidad
    //--------------------------------------------------------------

    /**
     * Valida una lista de HTMLElement con los campos a los que aplicar validaciones
     * Retorna el número de errores que se han encontrado.
     * 
     * @param {*} listaCampos lista de campos a validar
     * @param {*} pararSiError para si hay errores. Por defecto es true
     */
    #validarCampos(listaCampos, pararSiError = true) {
        
        // Errores encontrados
        let errores = 0;

        // Recorre los campos
        for(let n = 0;n < listaCampos.length && (!pararSiError || !errores);n++) {
            
            // Obtiene el campo en la posición n
            const campo = $(listaCampos.get(n));

            // Si no se pasa la validación se incrementa el número de errores
            if(!this.#validarCampo(campo)) {
                errores++;
            }
        }

        return errores;
    }

    /**
     * Valida un campo pasado como argumento. 
     * 
     * @param {*} campo HTMLElement del campo al que aplicar la validación
     */
    #validarCampo(campo) {
        
        // Obtengo laq lista de validaciones
        // const listaValidaciones = campo.dataset.validacion.split(",");
        const listaValidaciones = campo.data("validacion").split(",");

        let errores = 0;    
        for(let n = 0;n < listaValidaciones.length && errores == 0;n++) {

            // Obtiene el nombre de la validación
            const nombreValidacion = listaValidaciones[n];

            // Obtiene la función de validación
            const funcionValidacion = validaciones["val_"+nombreValidacion];

            // Llamo a la funcion de validación
            if(!funcionValidacion(campo.val())) {

                this.#marcarErrorCampo(campo);
                errores++;            
            }        
        }

        // Si no hay errores, limpio el error
        if(!errores) {
            this.#limpiarErrorCampo(campo);
        }

        return !errores;
    }

    /**
     * Marca un error en un campo pasado como argumento
     * 
     * @param {*} campo 
     */
    #marcarErrorCampo(campo) {
        campo.addClass("error");   
    }

    /**
     * Limpia un error de un campo pasado como argumento
     * 
     * @param {*} campo 
     */
    #limpiarErrorCampo(campo) {
        campo.removeClass("error");   
    }

    /**
     * Muestra ayuda de un campo
     */
    #mostrarAyudaCampo(campo) {
        
        // Obtengo la ayuda a mostrar
        const ayuda = campo.data("ayuda");

        // Obtengo el mensaje de ayuda
        if(ayuda) {
            $(`#${campo.attr('name')}-ayuda`).text(ayuda);
        }
        
    }

    /**
     * Limpia el campo de ayuda
     */
    #limpiarAyudaCampo(campo) {

        // Obtengo el mensaje de ayuda
        $(`#${campo.attr('name')}-ayuda`).text("");
    }
}