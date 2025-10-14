function ejercicio0106() {
            let inicio = new Date();
            
            let nombre = prompt("Introduce tu nombre:");
            if (nombre === null) return;
            
            let apellido1 = prompt("Introduce tu primer apellido:");
            if (apellido1 === null) return;
            
            let apellido2 = prompt("Introduce tu segundo apellido:");
            if (apellido2 === null) return;
            
            let fin = new Date();
            let tiempoTranscurrido = Math.round((fin - inicio) / 1000);
            
            let resultado = `En introducir ${nombre} ${apellido1} ${apellido2} ha tardado ${tiempoTranscurrido} segundos.`;
            
            document.getElementById("resultado0106").innerHTML = `<strong>${resultado}</strong>`;
            document.getElementById("resultado0106").style.display = "block";
        }