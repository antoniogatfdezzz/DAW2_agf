Problema n´umero 134
Escalera de color

La llamada baraja inglesa es una modificaci´on menor de la baraja francesa. Sus similitudes son tan
grandes, que es habitual considerarlas la misma. Contiene 52 cartas, distribu´ıdas en 4 palos diferentes.
Los palos se conocen con el nombre de picas (♠), diamantes (♦), tr´eboles (♣) y corazones (♥). De cada
uno, hay trece cartas, con valores del 1 (al que se conoce como As) hasta el 10, m´as las tres figuras,
Jack (J ), Queen (Q) y King (K ), que, num´ericamente, ser´ıan los valores 11, 12 y 13. Las diferencias
m´as notables entre la baraja francesa y la inglesa est´an en el nombre de la carta Jack (conocida en la
francesa como Valet, V ), y el As, nombre espec´ıfico de la baraja inglesa que, adem´as, desplaza su valor
en muchos juegos del 1 al 14, convirti´endola en una carta m´as poderosa que la K.
La baraja inglesa se utiliza en juegos mundialmente conocidos, como el bridge, la canasta o el p´oquer.
En este ´ultimo, la jugada m´as valiosa es una escalera de color, que se forma cuando un mismo jugador
consigue una mano de 5 cartas del mismo palo con valores consecutivos.
Entrada
La entrada estar´a compuesta por sucesivos casos de prueba. Cada uno ocupar´a una l´ınea, y estar´a
compuesto por una mano de cuatro cartas del juego del p´oquer.
Cada carta se representar´a indicando primero su n´umero (2, 3, 4, 5, 6, 7, 8, 9, 10, J, Q, K, A) y luego
su palo (P para picas, D para diamantes, T para tr´eboles y C para corazones) separados por un espacio.
Las cuatro cartas de cada caso de prueba estar´an tambi´en separadas por un espacio.
La entrada finalizar´a cuando se reciba un 0 en lugar del valor de la primera carta de la mano.
Salida
Para cada caso de prueba el programa escribir´a la carta necesaria que habr´ıa que a˜nadir a las cuatro
recibidas para obtener la escalera de color m´as alta posible. La salida vendr´a dada por un valor y un
palo, con el mismo formato que en la entrada. Si con las cartas del caso de prueba resultase imposible
crear una escalera, se escribir´a NADA.
Aunque en el desarrollo de una partida de p´oquer para crear una escalera el As puede utilizarse tanto
al principio de los n´umeros (valor 1) como al final (valor 14), el programa considerar´a que s´olo puede
colocarse despu´es de una K.
Entrada de ejemplo
2 C 3 C 4 C 5 C
Q P 9 P 7 P 6 P
Q P K P 9 P 10 P
0
Salida de ejemplo
6 C
NADA
J P
