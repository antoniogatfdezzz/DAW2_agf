document.addEventListener('DOMContentLoaded', function() {
    const texto = document.getElementById('texto');
    const inputTamano = document.getElementById('tamano');
    const selectFuente = document.getElementById('fuente');
    
    const estiloInicial = window.getComputedStyle(texto);
    const tamanoInicial = parseInt(estiloInicial.fontSize);
    
    inputTamano.value = tamanoInicial;
    
    texto.style.fontSize = tamanoInicial + 'px';
    
    function cambiarTamano() {
        const nuevoTamano = inputTamano.value;
        texto.style.fontSize = nuevoTamano + 'px';
    }

    function cambiarFuente() {
        const nuevaFuente = selectFuente.value;
        texto.style.fontFamily = nuevaFuente;
    }
    
    inputTamano.addEventListener('input', cambiarTamano);
    inputTamano.addEventListener('change', cambiarTamano);
    selectFuente.addEventListener('change', cambiarFuente);
});
