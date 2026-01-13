Problema n´umero 514
El mejor dato del paro

Cada vez que el Instituto Nacional de Estad´ıstica publica los datos del
paro del ´ultimo mes, comienzan las distintas interpretaciones de las tasas.
Si el paro sube, los pol´ıticos en el gobierno buscar´an la forma de maquillar
el mal dato compar´andolo, por ejemplo, con la subida producida el mismo
mes del a˜no anterior. Si el paro baja, los grupos de la oposici´on buscar´an
malos datos en tasas secundarias como la calidad de los nuevos contratos.
Dejando a un margen ese tipo de estrategias, una forma f´acil de en-
tender si el dato es bueno o no es la comparaci´on con la llamada “serie
hist´orica”: ¿c´omo de buena es la tasa compar´andola con los ´ultimos meses? Es innegable que si la tasa
es la m´as baja de los ´ultimos 15 meses, la econom´ıa no est´a tan mal.
Entrada
La entrada estar´a compuesta de distintos casos de prueba, cada uno representando la serie hist´orica
de la tasa del paro de un pa´ıs.
Para cada serie, aparecer´an dos l´ıneas. La primera tiene el n´umero 1 ≤ n ≤ 300.000 de meses a
considerar y la segunda la tasa del paro de cada uno de los meses, separadas por espacios. Todas las
tasas estar´an entre 0 y 107.
La entrada termina con una serie hist´orica de 0 meses, que no deber´a procesarse.
Salida
Por cada caso de prueba se escribir´a una ´unica l´ınea con n n´umeros, uno por mes, separados por un
espacio. El valor asociado al mes m indicar´a cu´antos meses consecutivos llevaba ininterrumpidamente la
tasa del paro con un valor peor (por encima) que el alcanzado ese mes.
El dato del mes m nunca podr´a ser mayor que m - 1, pues ese es el n´umero de valores anteriores. En
ese caso la tasa es la mejor de toda la serie hist´orica hasta ese momento.
Entrada de ejemplo
3
1 2 3
3
3 2 1
5
5 7 6 3 4
3
10 10 10
0
Salida de ejemplo
0 0 0
0 1 2
0 0 1 3 0
0 0 0
