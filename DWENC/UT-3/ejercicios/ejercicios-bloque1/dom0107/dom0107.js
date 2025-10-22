const btnAnadir = document.getElementById('btnAnadir');
const inputNombre = document.getElementById('nombreFruta');
const inputColor = document.getElementById('colorFruta');
const tabla = document.getElementById('tablaFrutas');
const tbody = tabla.querySelector('tbody');

function eliminarFila(event) {
	const btn = event.currentTarget;
	const fila = btn.closest('tr');
	const indice = fila.rowIndex; // Índice dentro de la tabla (incluye thead)
	tabla.deleteRow(indice);
}

function crearCeldaAcciones(tr) {
	const td = document.createElement('td');
	const btn = document.createElement('button');
	btn.type = 'button';
	btn.textContent = 'Eliminar';
	btn.className = 'btn-eliminar';
	btn.addEventListener('click', eliminarFila);
	td.appendChild(btn);
	tr.appendChild(td);
}

function anadirFruta() {
	const nombreFruta = inputNombre.value.trim();
	const colorFruta = inputColor.value.trim();

	if (nombreFruta === '' || colorFruta === '') {
		alert('Por favor, completa ambos campos');
		return;
	}

	const tr = document.createElement('tr');
	const tdNombre = document.createElement('td');
	tdNombre.textContent = nombreFruta;
	const tdColor = document.createElement('td');
	tdColor.textContent = colorFruta;

	tr.appendChild(tdNombre);
	tr.appendChild(tdColor);
	crearCeldaAcciones(tr);

	tbody.appendChild(tr);

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

tbody.querySelectorAll('tr').forEach(tr => {
	if (tr.cells.length < 3) {
		crearCeldaAcciones(tr);
	}
});

