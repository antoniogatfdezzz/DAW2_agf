document.addEventListener('DOMContentLoaded', function() {
	const tabla = document.getElementById('tablaFrutas').getElementsByTagName('tbody')[0];
	const inputFruta = document.getElementById('inputFruta');
	const inputColor = document.getElementById('inputColor');
	const btnAgregar = document.getElementById('btnAgregar');

	btnAgregar.addEventListener('click', function() {
		const fruta = inputFruta.value.trim();
		const color = inputColor.value.trim();
		if (fruta && color) {
			const nuevaFila = tabla.insertRow();
			const celdaFruta = nuevaFila.insertCell(0);
			const celdaColor = nuevaFila.insertCell(1);
			celdaFruta.textContent = fruta;
			celdaColor.textContent = color;
			inputFruta.value = '';
			inputColor.value = '';
			inputFruta.focus();
		} else {
			alert('Por favor, introduce el nombre de la fruta y el color.');
		}
	});
});
