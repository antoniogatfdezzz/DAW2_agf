Problema n´umero 244
Reinas atacadas

En el ajedrez, la reina es la pieza m´as poderosa, al poderse mover cualquier n´umero de escaques en
vertical, horizontal, o diagonal.
Movimientos de la reina
En 1848, el alem´an Max Bezzel plante´o el puzzle de las 8 reinas, en el que ret´o a colocar 8 reinas
sobre un tablero sin que se atacaran entre s´ı. Dos a˜nos despu´es, se dieron algunas de las 92 soluciones.
Una de las soluciones posibles
Desde entonces, matem´aticos y aficionados de todo el mundo han estudiado el problema, generali-
z´andolo a tama˜nos de tableros de ajedrez de N×N. En 1972, Dijkstra, en plena crisis del software, us´o
el problema para demostrar el poder de la programaci´on estructurada, y desde entonces es un ejemplo
cl´asico de algoritmo de vuelta atr´as.
Para poder colocar las reinas, el primer paso es saber cu´ando un grupo de reinas sobre un tablero de
ajedrez se atacan entre s´ı, es decir cu´ando hay al menos una reina que podr´ıa comer a otra siguiendo las
reglas del movimiento del juego.
Entrada
La entrada consta de un conjunto de casos de prueba. Cada uno comienza con una l´ınea con dos
n´umeros. El primero indica el ancho y alto del tablero de ajedrez (siempre ser´a cuadrado de como mucho
2.000×2.000). El segundo indica el n´umero de reinas colocadas sobre ´el (entre 1 y 100).
A continuaci´on vendr´a una l´ınea con la posici´on de todas las reinas. Para cada una, se indicar´a
primero la coordenada X y luego la Y, separadas por espacio. Las posiciones de cada reina tambi´en se

separar´an por un ´unico espacio. Todas las posiciones ser´an v´alidas (cada coordenada estar´a entre 1 y el
tama˜no del tablero) y se garantiza que no habr´a dos reinas en la misma posici´on.
La entrada termina con un caso de prueba con un tablero de tama˜no 0×0 y sin reinas que no debe
procesarse.
Salida
Para cada caso de prueba, el programa escribir´a, en la salida est´andar, una l´ınea con el texto “SI” si
hay reinas atacadas en la configuraci´on dada, y “NO” en otro caso (sin las comillas).
Entrada de ejemplo
8 8
1 2 2 8 3 6 4 1 5 3 6 5 7 7 8 4
4 2
1 1 3 3
4 2
1 1 3 2
0 0
Salida de ejemplo
NO
SI
NO
