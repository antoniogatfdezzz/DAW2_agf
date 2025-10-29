"use strict";

function ejercicio0107() {
            let mes = parseInt(prompt("Introduce el número del mes (1-12):"));
            let año = parseInt(prompt("Introduce el año:"));
            
            if (isNaN(mes) || isNaN(año) || mes < 1 || mes > 12) {
                alert("Por favor, introduce valores válidos");
                return;
            }
            
            let meses = ["", "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", 
                        "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"];
            
            let diasSemana = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
            
            let nombreMes = meses[mes];
            let diasEnMes = new Date(año, mes, 0).getDate();
            
            let calendario = `<strong>${nombreMes} - ${año}</strong><br><br>`;
            
            for (let dia = 1; dia <= diasEnMes; dia++) {
                let fecha = new Date(año, mes - 1, dia);
                let diaSemana = diasSemana[fecha.getDay()];
                calendario += `${dia} (${diaSemana})`;
                if (dia < diasEnMes) {
                    calendario += ", ";
                }
                // Salto de línea cada 7 días para mejor legibilidad
                if (dia % 7 === 0) {
                    calendario += "<br>";
                }
            }
            
            document.getElementById("resultado0107").innerHTML = calendario;
            document.getElementById("resultado0107").style.display = "block";
        }