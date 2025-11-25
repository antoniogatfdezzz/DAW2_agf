
export class Modal {

    /**
     * Inicializa el modal. Permite a partir de ahora mostrar mensajes y diálogos
     */
    constructor() {

        // Añade al body un div con el contenido cargado desde el documento
        // html indicado
        $('body').append(
            $('<div>').load(
                getUrlComponenteHtml("modal")
            )
        );                
    }


    /**
     * Muestra un mensaje simple con el texto pasado como argumento
     * 
     * @param {string} texto - Contenido del modal
     * @param {string} titulo - Título del modal (opcional)
     */
    mostrar(texto, titulo = "Información") {
        
        // Asignamos el título
        $("#modal .modal-title").text(titulo);
        
        // Asignamos el texto al modal
        $("#modal .modal-body").html(texto);

        // Resetear el footer a un solo botón de cerrar
        $("#modal .modal-footer").html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        `);

        // Muestra el modal
        $("#modal").modal("show");
    }


    /**
     * Muestra un modal de confirmación con botones de aceptar y cancelar
     * 
     * @param {string} texto - Mensaje de confirmación
     * @param {function} onAceptar - Callback ejecutado al aceptar
     * @param {string} titulo - Título del modal (opcional)
     * @param {string} textoAceptar - Texto del botón aceptar (opcional)
     * @param {string} textoCancelar - Texto del botón cancelar (opcional)
     */
    confirmar(texto, onAceptar, titulo = "Confirmación", textoAceptar = "Aceptar", textoCancelar = "Cancelar") {
        
        // Asignamos el título
        $("#modal .modal-title").text(titulo);
        
        // Asignamos el texto al modal
        $("#modal .modal-body").html(texto);

        // Configurar los botones del footer
        $("#modal .modal-footer").html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${textoCancelar}</button>
            <button type="button" class="btn btn-primary" id="modal-btn-aceptar">${textoAceptar}</button>
        `);

        // Asignar el evento al botón aceptar
        $("#modal-btn-aceptar").off('click').on('click', () => {
            $("#modal").modal("hide");
            if (onAceptar && typeof onAceptar === 'function') {
                onAceptar();
            }
        });

        // Muestra el modal
        $("#modal").modal("show");
    }


    /**
     * Muestra un modal personalizado con opciones avanzadas
     * 
     * @param {object} opciones - Objeto con las opciones del modal
     * @param {string} opciones.titulo - Título del modal
     * @param {string} opciones.contenido - Contenido HTML del modal
     * @param {array} opciones.botones - Array de objetos con configuración de botones
     *   Cada botón: {texto: string, clase: string, callback: function}
     */
    mostrarPersonalizado(opciones) {
        
        // Asignamos el título
        $("#modal .modal-title").text(opciones.titulo || "Modal");
        
        // Asignamos el contenido
        $("#modal .modal-body").html(opciones.contenido || "");

        // Configurar botones personalizados
        let botonesHtml = '';
        
        if (opciones.botones && Array.isArray(opciones.botones)) {
            opciones.botones.forEach((boton, index) => {
                const clase = boton.clase || 'btn-secondary';
                const texto = boton.texto || 'Botón';
                botonesHtml += `<button type="button" class="btn ${clase}" id="modal-btn-custom-${index}">${texto}</button>`;
            });
        } else {
            // Botón por defecto
            botonesHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
        }

        $("#modal .modal-footer").html(botonesHtml);

        // Asignar eventos a los botones personalizados
        if (opciones.botones && Array.isArray(opciones.botones)) {
            opciones.botones.forEach((boton, index) => {
                $(`#modal-btn-custom-${index}`).off('click').on('click', () => {
                    if (boton.cerrarModal !== false) {
                        $("#modal").modal("hide");
                    }
                    if (boton.callback && typeof boton.callback === 'function') {
                        boton.callback();
                    }
                });
            });
        }

        // Muestra el modal
        $("#modal").modal("show");
    }


    /**
     * Oculta el modal
     */
    ocultar() {
        $("#modal").modal("hide");
    }

}