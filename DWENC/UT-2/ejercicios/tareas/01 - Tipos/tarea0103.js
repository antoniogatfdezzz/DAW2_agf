function ejercicio0103() {
            let texto = prompt("Introduce un texto para contar vocales:");
            if (texto !== null) {
                let contador = 0;
                let vocales = "aeiouAEIOU";
                let vocalesEncontradas = "";
                
                for (let i = 0; i < texto.length; i++) {
                    if (vocales.includes(texto[i])) {
                        contador++;
                        vocalesEncontradas += texto[i] + " ";
                    }
                }
                
                document.getElementById("resultado0103").innerHTML = `
                    <strong>Texto:</strong> ${texto}<br>
                    <strong>Número de vocales:</strong> ${contador}<br>
                    <strong>Vocales encontradas:</strong> ${vocalesEncontradas}
                `;
                document.getElementById("resultado0103").style.display = "block";
            }
        }