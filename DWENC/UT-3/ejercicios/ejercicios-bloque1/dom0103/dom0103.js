const botones = document.querySelectorAll('.botones-container button');
const parrafo = document.getElementById('parrafo');
const btnLimpiar = document.getElementById('limpiar');
        
botones.forEach(boton => {
     boton.addEventListener('click', function() {
        parrafo.textContent += this.textContent + ' ';
    });
});

btnLimpiar.addEventListener('click', function() {
    parrafo.textContent = '';
});