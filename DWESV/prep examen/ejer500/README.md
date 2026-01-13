Problema n´umero 500
Votaciones capic´ua

Ruritania tiene N senadores, con N entre 1000 y 9999, n´umero que suele
cambiar seg´un la voluntad del presidente ruritano, que es bastante veleta.
Tambi´en cambia a voluntad del presidente el quorum Q con el m´ınimo n´umero
de senadores que deben participar en una votaci´on para que se considere v´alida,
aunque siempre se ha de cumplir que 1000 ≤ Q.
En el senado s´olo se puede votar s´ı o no, y por supuesto no todos los
senadores votan siempre. Pero cuando el n´umero concatenado de las cuatro
cifras con los votos a favor y las cuatro cifras con los votos en contra es capic´ua, tras la votaci´on todos
se van al bar del senado para celebrarlo.
Para la concatenaci´on se cuentan los ceros a la izquierda; esto es una votaci´on 1000 – 1 es capic´ua,
pues la concatenaci´on es 10000001, y tambi´en lo es una votaci´on 1 – 1000, pues la concatenaci´on es
00011000.
La pregunta es: si en esta legislatura Ruritania tiene N senadores (1000 ≤ N ≤ 9999) y el quorum
es Q (1000 ≤Q ≤ N ), ¿cu´antas votaciones capic´ua son posibles?
Entrada
La entrada est´a formada por varias l´ıneas con dos enteros N, Q. Se garantiza 1000 ≤ Q ≤ N ≤ 9999.
El final de la entrada se indica con una l´ınea con dos ceros que no se debe procesar.
Salida
Para cada caso de prueba N, Q debe escribirse una l´ınea con los n´umeros N, Q y M, donde M es el
n´umero de posibles votaciones capic´ua con N senadores y un quorum de Q.
Entrada de ejemplo
1001 1000
2100 2000
5324 4999
0 0
Salida de ejemplo
1001 1000 2
2100 2000 3
5324 4999 156
