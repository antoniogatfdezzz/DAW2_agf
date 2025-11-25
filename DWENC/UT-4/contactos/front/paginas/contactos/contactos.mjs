//--------------------------------------------------------------
// Dependencias
//--------------------------------------------------------------
import * as moduloTabla from "../../js/componentes/tabla.mjs";
import * as moduloPaginador from "../../js/componentes/paginador.mjs";
import * as moduloBuscador from "../../js/componentes/buscador.mjs";
import * as moduloToast from "../../js/componentes/toast.mjs";
import { Modal } from "../../js/componentes/modal.js";

//--------------------------------------------------------------
// Constantes
//--------------------------------------------------------------
const JSON2HTML_PLANTILLA_TABLA = {
      '<>': 'tr','html': [
        {'<>': 'td','html': '${nombre}'},
        {'<>': 'td','html': '${apellidos}'},
        {'<>': 'td','html': '${empresa}'},
        {'<>':'td','html':'<button name="btEditar" class="btn btn-info bi bi-pencil-fill" value="${id}"></button>'},
        {'<>':'td','html':'<button name="btEliminar" class="btn btn-danger bi bi-trash-fill" value="${id}"></button>'}
      ]
};

const TABLA_CONTACTOS = new moduloTabla.Tabla(URL_CONTACTOS, "#contactos", JSON2HTML_PLANTILLA_TABLA);
const PAGINADOR = new moduloPaginador.Paginador(
    "#paginador", 
    () => TABLA_CONTACTOS.navegarPaginaSiguiente(), 
    () => TABLA_CONTACTOS.navegarPaginaAnterior(), 
);
const BUSCADOR = new moduloBuscador.Buscador(
    "#buscador",
    (filtro) => { TABLA_CONTACTOS.añadirFiltro(filtro); }
);
const TOAST = new moduloToast.Toast();
const MODAL = new Modal();

//--------------------------------------------------------------
// Inicialización
//--------------------------------------------------------------
$(document).ready(() => {
    renderizarComponentes();
    
    $("#btAnadir").on("click", () => window.location = "contactos_edit.html");
    
    $("#btnModal").on("click", () => {
        MODAL.mostrar("Este es un mensaje de prueba del modal", "Información");
    });
});

//--------------------------------------------------
// Eventos
//-------------------------------------------------

//--------------------------------------------------------------
// Funciones de utilidad
//--------------------------------------------------------------

/**
 * Muestra el listado de contactos.
 */
function renderizarComponentes() {
  TABLA_CONTACTOS.renderizar();
  PAGINADOR.renderizar();
  BUSCADOR.renderizar();

  TOAST.mostrar("Mensade de PRUEBA");
}

  

