
const zona = document.getElementById('zona');
const cosa = document.getElementById('cosa');
const coordsGlobal = document.getElementById('coords-global');
const coordsZona = document.getElementById('coords-zona');

function colorAleatorio() {
	const h = Math.floor(Math.random() * 360); // tono
	const s = 85;
	const l = 55;
	return `hsl(${h} ${s}% ${l}%)`;
}

document.addEventListener('mousemove', (e) => {
	coordsGlobal.textContent = `X: ${e.clientX}, Y: ${e.clientY}`;
});

zona.addEventListener('mousemove', (e) => {
	const rect = zona.getBoundingClientRect();
	const x = e.clientX - rect.left;
	const y = e.clientY - rect.top;

	coordsZona.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;

	cosa.style.left = `${x}px`;
	cosa.style.top = `${y}px`;
});

zona.addEventListener('mouseleave', () => {
	coordsZona.textContent = 'fuera';
});

cosa.addEventListener('click', (e) => {
	e.stopPropagation();
	cosa.style.backgroundColor = colorAleatorio();
});

