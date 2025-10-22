const btnAnadir = document.getElementById('btnAnadir');
const inputNombre = document.getElementById('nombreFruta');
const inputColor = document.getElementById('colorFruta');
const tbody = document.querySelector('#tablaFrutas tbody');

function anadirFruta() {
    const nombreFruta = inputNombre.value.trim();
    const colorFruta = inputColor.value.trim();

    if (nombreFruta === '' || colorFruta === '') {
        alert('Por favor, completa ambos campos');
        return;
    }

    const nuevaFila = `
        <tr>
            <td>${nombreFruta}</td>
            <td>${colorFruta}</td>
        </tr>
    `;

    tbody.innerHTML += nuevaFila;

    inputNombre.value = '';
    inputColor.value = '';

    inputNombre.focus();
}

btnAnadir.addEventListener('click', anadirFruta);

inputColor.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        anadirFruta();
    }
});

inputNombre.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        anadirFruta();
    }
});
