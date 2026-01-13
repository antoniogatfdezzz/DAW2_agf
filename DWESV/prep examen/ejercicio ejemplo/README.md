# Gestión de inventario de productos

Crea un programa en PHP que gestione un pequeño inventario de productos de una tienda. El programa debe cumplir los siguientes requisitos:

- Uso de **clases y objetos**.
- **Lectura y escritura de ficheros**.
- Uso de **estructuras de control** (decisiones y bucles) para resolver el algoritmo.
- Uso de **vectores y/o matrices**.

## Descripción del problema

La tienda dispone de un fichero de texto `inventario.txt` donde se guardan los productos. Cada línea del fichero representa un producto con el siguiente formato (campos separados por punto y coma `;`):

```text
nombre;precio;unidades
```

Por ejemplo:

```text
Manzanas;1.25;50
Peras;1.50;30
Leche;0.99;100
```

Se pide implementar una función llamada `gestionDeInventarioDeProductos` que haga lo siguiente:

1. Definir una clase `Producto` con las propiedades: `nombre`, `precio` y `unidades`.
2. Definir una clase `Inventario` que almacene un **vector (array) de objetos** `Producto`.
3. La clase `Inventario` debe poder:
   - Leer el fichero `inventario.txt` y cargar los productos en un array de objetos `Producto`.
   - Calcular y mostrar:
     - El **número total de productos diferentes**.
     - El **número total de unidades** disponibles (suma de todas las unidades).
     - El **valor total del inventario** (suma de `precio * unidades` de todos los productos).
     - El **producto con más unidades** en stock.
   - Permitir **añadir un nuevo producto** (si el producto ya existe por nombre, se suman las unidades; si no existe, se añade uno nuevo al array).
   - Guardar de nuevo el inventario actualizado en el fichero `inventario.txt`.

4. La función `gestionDeInventarioDeProductos` debe:
   - Asegurarse de que existe el fichero `inventario.txt`. Si no existe, crearlo con algunos productos de ejemplo.
   - Crear un objeto `Inventario` y cargar los productos desde el fichero.
   - Mostrar por pantalla un resumen con los datos calculados (totales, valor del inventario, producto con más unidades).
   - Añadir al menos **un nuevo producto** al inventario (puedes fijar los valores en el código para simplificar el ejercicio).
   - Guardar los cambios en el fichero y volver a mostrar el contenido completo del inventario leído del fichero.

## Pistas

- Usa `fopen`, `fgets`, `fwrite` y `fclose` (o funciones como `file`, `file_put_contents`, etc.) para trabajar con ficheros.
- Usa `explode` para separar los campos del fichero por `;`.
- Usa **arrays** de objetos para representar la lista de productos.
- Usa estructuras `if`, `foreach`, `for` o `while` según necesites.

## Entrega

Implementa la solución en una función PHP llamada `gestionDeInventarioDeProductos` y guarda la solución en el archivo `gestionDeInventarioDeProductos.php` en esta misma carpeta.

Crea también un archivo `main.php` que incluya el archivo de la función y la ejecute para probar el programa.
