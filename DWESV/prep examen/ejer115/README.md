Problema n´umero 115
N´umero de Kaprekar

El matem´atico indio Dattaraya Ramchandra Kaprekar trabaj´o en teor´ıa de n´umeros, realizando varios
descubrimientos a lo largo de su vida. Uno de ellos fue el conjunto de los que, desde entonces, se conocen
como n´umeros de Kaprekar, que son aquellos n´umeros enteros positivos que, al ser elevados al cuadrado,
pueden descomponerse (para una base dada, que asumiremos ser base 10) en dos enteros positivos cuya
suma es igual al n´umero original.
Por ejemplo, el n´umero 703 es un n´umero de Kaprekar, dado que 7032 es 494209 que puede descom-
ponerse en 494 y 209 cuya suma da, de nuevo, 703. Otro ejemplo es el 9 (92 = 81 y 8 + 1 = 9).
Hay que tener presente que ambos n´umeros en la descomposici´on no tienen por qu´e tener el mismo
n´umero de d´ıgitos. Por ejemplo en el caso del n´umero 2728 tenemos que 27282 = 7441984 que es n´umero
de Kaprekar porque 744 + 1984 = 2728. Tambi´en puede darse el caso de que el n´umero al cuadrado
contenga alg´un cero. Por ejemplo, con el 4879 tenemos que 48792 = 23804641, que es un n´umero de
Kaprekar porque 238 + 04641 = 4879.
Si bien se permite que el primero de los valores de la descomposici´on sea 0 (y as´ı por ejemplo 1 es
n´umero de Kaprekar), el segundo no puede serlo. Debido a ello, el 100 no es un n´umero de Kaprekar.
Fijate que 1002 es 10000, que podr´ıa descomponerse en 100 y 00 cuya suma es 100. Sin embargo, el
segundo n´umero deber´ıa ser 0, que no se considera v´alido.
Entrada
La entrada del programa consistir´a en varios casos de prueba. Cada caso de prueba ser´a un n´umero
mayor o igual que 1 y menor que 65536. Los casos de prueba terminar´an con un 0 que marcar´a el final
de la entrada y que no hay que procesar.
Salida
Para cada caso de prueba el programa mostrar´a “SI” si es un n´umero de Kaprekar, y “NO” en otro
caso.
Entrada de ejemplo
22222
75
99
100
504
0
Salida de ejemplo
SI
NO
SI
NO
NO