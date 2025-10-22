document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('miFormulario');
	const nombre = document.getElementById('nombre');
	const numero = document.getElementById('numero');
	const errNombre = document.getElementById('error-nombre');
	const errNumero = document.getElementById('error-numero');

	const setError = (input, errorEl, message) => {
		input.classList.add('error');
		input.setAttribute('aria-invalid', 'true');
		errorEl.textContent = message;
	};

	const clearError = (input, errorEl) => {
		input.classList.remove('error');
		input.removeAttribute('aria-invalid');
		errorEl.textContent = '';
	};

	const isEvenInteger = (value) => {
		if (value === '' || value === null || value === undefined) return false;
		const n = Number(value);
		if (!Number.isFinite(n) || !Number.isInteger(n)) return false;
		return n % 2 === 0;
	};

	form.addEventListener('submit', (e) => {
		let hasErrors = false;

		// Validar nombre (no vacío, espacios cuentan como vacío)
		const nombreVal = nombre.value.trim();
		if (nombreVal.length === 0) {
			setError(nombre, errNombre, 'El nombre no puede estar vacío.');
			hasErrors = true;
		} else {
			clearError(nombre, errNombre);
		}

		// Validar número (entero par)
		const numeroVal = String(numero.value).trim();
		if (!isEvenInteger(numeroVal)) {
			setError(numero, errNumero, 'Introduce un número entero par.');
			hasErrors = true;
		} else {
			clearError(numero, errNumero);
		}

		if (hasErrors) {
			e.preventDefault();
			const firstInvalid = form.querySelector('input.error');
			if (firstInvalid) firstInvalid.focus();
		}
		// Si no hay errores, el formulario se enviará normalmente.
	});

	// UX: limpiar errores al escribir si el valor ya es válido
	nombre.addEventListener('input', () => {
		if (nombre.value.trim().length > 0) clearError(nombre, errNombre);
	});
	numero.addEventListener('input', () => {
		if (isEvenInteger(String(numero.value).trim())) clearError(numero, errNumero);
	});

	// Limpiar errores al resetear
	form.addEventListener('reset', () => {
		clearError(nombre, errNombre);
		clearError(numero, errNumero);
	});
});

