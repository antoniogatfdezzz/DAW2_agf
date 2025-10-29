"use strict";

function ejercicio0105() {
            let fecha = new Date();
            
            let diasSemana = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
            let meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            
            let diaSemana = diasSemana[fecha.getDay()];
            let dia = fecha.getDate();
            let mes = meses[fecha.getMonth()];
            let año = fecha.getFullYear();
            let horas = fecha.getHours().toString().padStart(2, '0');
            let minutos = fecha.getMinutes().toString().padStart(2, '0');
            
            let resultado = `Hoy es ${diaSemana}, ${dia} de ${mes} de ${año} y son las ${horas}:${minutos} horas.`;
            
            document.getElementById("resultado0105").innerHTML = `<strong>${resultado}</strong>`;
            document.getElementById("resultado0105").style.display = "block";
        }