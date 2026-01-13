Problema n´umero 499
Modificaci´on de tablas

En ocasiones es necesario modificar los valores num´ericos de una tabla a
trav´es de un fichero que contiene instrucciones de c´omo modificar dicha tabla.
Por ejemplo, una tarea a realizar de la forma anterior ser´ıa la de aumentar o
disminuir el valor de un rango consecutivo de casillas que se encuentran dentro
de una misma columna de la tabla.
Entrada
La entrada est´a formada por distintos casos de prueba, y cada caso de prueba ocupa varias l´ıneas.
La primera l´ınea contiene tres n´umeros: el n´umero de filas (F ) y columnas (C ) de la tabla, y el n´umero
de modificaciones que se van a hacer sobre ella (N ).
A continuaci´on hay N l´ıneas, cada una de ellas con la descripci´on de una modificaci´on a la tabla.
Cada una de estas l´ıneas consta de cuatro n´umeros: I, A, B y M. El primer n´umero, I, es el ´ındice de
la columna que se va a modificar. Los n´umeros A y B son los ´ındices de la primera y la ´ultima fila a
modificar. El n´umero M es el valor a a˜nadir a las casillas anteriores.
Se garantiza que 1 ≤ F, 1 ≤ C, F ×C ≤ 1.000.000, 1 ≤ N ≤ 100.000 y –10 ≤ M ≤ 10. Los ´ındices
de las filas y las columnas empiezan en 0, y la tabla se supone inicialmente rellena de ceros.
La entrada termina con tres ceros, que no deben procesarse.
Salida
Para cada caso de prueba, se escribir´a una tabla con F filas y C columnas en la cual se han realizado
todas y cada una de las operaciones indicadas. Cada n´umero dentro de una misma fila se separar´a
mediante un espacio y al final de cada fila se insertar´a un salto de l´ınea.
Entrada de ejemplo
4 5 3
2 0 3 1
0 1 2 -1
2 1 1 2
0 0 0
Salida de ejemplo
0 0 1 0 0
-1 0 3 0 0
-1 0 1 0 0
0 0 1 0 0
