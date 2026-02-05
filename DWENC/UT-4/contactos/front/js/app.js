//-----------------------------------------------------------------
// Funciones generales de la aplicación
//-----------------------------------------------------------------

/**
 * Calcula la ruta a una página html de un componete dado el nombre de
 * la página
 * 
 * @param {*} pagina 
 */
function getUrlComponenteHtml(pagina) {
    return `${URL_BASE_COMPONENTES}/${pagina}.html`; 
}

/**
 * Cierra sesión
 */
function cerrarSesion() {

    // Limpia todas las claves en el almacenamiento
    localStorage.clear();

    // Redirige a la página de login
    window.location.href = "/paginas/login/login.html";
}