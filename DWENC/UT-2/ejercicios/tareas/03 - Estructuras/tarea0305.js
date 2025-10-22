
const readline = require('readline');

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

let letrasProhibidas = '';
let palabras = [];

function contieneLetrasProhibidas(palabra, letrasProhibidas) {
    const palabraMinuscula = palabra.toLowerCase();
    const prohibidasMinuscula = letrasProhibidas.toLowerCase();
    
    for (let letra of prohibidasMinuscula) {
        if (palabraMinuscula.includes(letra)) {
            return true;
        }
    }
    return false;
}

function pedirPalabra() {
    rl.question('Introduce una palabra (o cadena vacía para terminar): ', (palabra) => {
        if (palabra === '') {
            // Mostrar resumen
            console.log('\n=== RESUMEN ===');
            if (palabras.length === 0) {
                console.log('No se introdujeron palabras.');
            } else {
                for (let item of palabras) {
                    if (item.valida) {
                        console.log(`✓ "${item.palabra}" - VÁLIDA`);
                    } else {
                        console.log(`✗ "${item.palabra}" - NO VÁLIDA (contiene letras prohibidas)`);
                    }
                }
            }
            rl.close();
        } else {
            const esValida = !contieneLetrasProhibidas(palabra, letrasProhibidas);
            
            if (esValida) {
                console.log(`✓ La palabra "${palabra}" es VÁLIDA`);
            } else {
                console.log(`✗ ERROR: La palabra "${palabra}" contiene letras prohibidas`);
            }
            
            palabras.push({ palabra: palabra, valida: esValida });
            
            pedirPalabra();
        }
    });
}

console.log('=== FILTRO DE LETRAS ===\n');
rl.question('Introduce las letras prohibidas: ', (respuesta) => {
    letrasProhibidas = respuesta;
    
    if (letrasProhibidas === '') {
        console.log('No se definieron letras prohibidas. Todas las palabras serán válidas.');
    } else {
        console.log(`Letras prohibidas: ${letrasProhibidas}\n`);
    }
    
    pedirPalabra();
});
