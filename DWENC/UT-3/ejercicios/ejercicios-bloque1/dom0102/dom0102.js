
document.addEventListener('DOMContentLoaded', function() {
	const campo = document.getElementById('campo-color');
	const botones = document.querySelectorAll('.color-btn');

	botones.forEach(boton => {
		boton.addEventListener('click', function() {
			const color = window.getComputedStyle(boton).backgroundColor;
			campo.style.backgroundColor = color;
		});
	});
});
