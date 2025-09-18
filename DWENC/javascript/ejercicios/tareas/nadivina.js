// Constantes del juego
const NUM_INTENTOS = 5;
const VALOR_MAXIMO = 20;

// Número secreto aleatorio entre 0 y VALOR_MAXIMO
const numeroSecreto = Math.floor(Math.random() * (VALOR_MAXIMO + 1));

let adivinado = false;

for (let i = 1; i <= NUM_INTENTOS; i++) {
  let intento = parseInt(prompt(`Intento ${i}/${NUM_INTENTOS}: Adivina el número (0 - ${VALOR_MAXIMO})`));

  if (isNaN(intento)) {
    alert("⚠️ Debes introducir un número válido.");
    i--; // No cuenta como intento
    continue;
  }

  if (intento === numeroSecreto) {
    alert(`🎉 ¡Felicidades! Has adivinado el número secreto (${numeroSecreto}) en el intento ${i}.`);
    adivinado = true;
    break;
  } else if (intento > numeroSecreto) {
    alert("El número secreto es menor.");
  } else {
    alert("El número secreto es mayor.");
  }
}

if (!adivinado) {
  alert(`😢 Lo siento, se acabaron los intentos. El número secreto era ${numeroSecreto}.`);
}
