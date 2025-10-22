
const campo = document.getElementById('campo');
const btnRojo = document.getElementById('btnRojo');
const btnAzul = document.getElementById('btnAzul');
const btnVerde = document.getElementById('btnVerde');

function setCampoColor(bgCssColor, textLabel) {
	if (!campo) return;
	campo.style.backgroundColor = bgCssColor;
	campo.style.color = '#fff';
	campo.style.borderColor = bgCssColor;
	if (!campo.value) {
		campo.value = `Color: ${textLabel}`;
	}
}

function onRojoClick() {
	setCampoColor('#e53935', 'rojo');
}

function onAzulClick() {
	setCampoColor('#1e88e5', 'azul');
}

function onVerdeClick() {
	setCampoColor('#43a047', 'verde');
}

btnRojo?.addEventListener('click', onRojoClick);
btnAzul?.addEventListener('click', onAzulClick);
btnVerde?.addEventListener('click', onVerdeClick);
