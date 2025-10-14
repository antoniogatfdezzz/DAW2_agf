function ejercicio0210() {
            let numeros = [];
            while (numeros.length < 3) {
                let numero = Math.floor(Math.random() * 99) + 1;
                if (!numeros.includes(numero)) {
                    numeros.push(numero);
                }
            }
            document.getElementById("resultado0210").innerHTML = `Números generados: ${numeros.join(", ")}`;
            document.getElementById("resultado0210").style.display = "block";
        }