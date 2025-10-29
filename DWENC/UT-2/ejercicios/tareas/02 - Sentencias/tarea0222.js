"use strict";

function ejercicio0222() {
            let pantalla = 0;
            let memoria = 0;
            let resultado = "=== CALCULADORA ===\n";
            resultado += "Operaciones: +, -, *, /\n";
            resultado += "C: limpiar pantalla, M: guardar en memoria\n";
            resultado += "R: recuperar memoria, q: salir\n\n";

            while (true) {
                resultado += `Pantalla: ${pantalla}\n`;
                let operacion = prompt(`Pantalla: ${pantalla}\nIntroduce operación (+, -, *, /, C, M, R, q):`);
                
                if (operacion === null || operacion.toLowerCase() === 'q') {
                    resultado += "Calculadora cerrada";
                    break;
                }

                if (operacion.toLowerCase() === 'c') {
                    pantalla = 0;
                    resultado += "Pantalla limpiada\n";
                    continue;
                }

                if (operacion.toLowerCase() === 'm') {
                    memoria = pantalla;
                    resultado += `Valor ${pantalla} guardado en memoria\n`;
                    continue;
                }

                if (operacion.toLowerCase() === 'r') {
                    pantalla = memoria;
                    resultado += `Valor ${memoria} recuperado de memoria\n`;
                    continue;
                }

                if (['+', '-', '*', '/'].includes(operacion)) {
                    let operando = prompt("Introduce el segundo operando (R para usar memoria):");
                    
                    if (operando === null) continue;
                    
                    if (operando.toLowerCase() === 'r') {
                        operando = memoria;
                    } else {
                        operando = parseFloat(operando);
                        if (isNaN(operando)) {
                            resultado += "Error: operando no válido\n";
                            continue;
                        }
                    }

                    let valorAnterior = pantalla;
                    
                    switch (operacion) {
                        case '+':
                            pantalla += operando;
                            break;
                        case '-':
                            pantalla -= operando;
                            break;
                        case '*':
                            pantalla *= operando;
                            break;
                        case '/':
                            if (operando === 0) {
                                resultado += "Error: división por cero\n";
                                continue;
                            }
                            pantalla /= operando;
                            break;
                    }
                    
                    resultado += `${valorAnterior} ${operacion} ${operando} = ${pantalla}\n`;
                } else {
                    resultado += "Error: operación no válida\n";
                }
            }

            document.getElementById("resultado0222").innerHTML = resultado;
            document.getElementById("resultado0222").style.display = "block";
        }