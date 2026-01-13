Problema n´umero 122
Avituallamiento en las etapas ciclistas

En toda etapa ciclista que se precie hay un momento en el que se permite a los participantes recuperar
fuerzas recogiendo comida que voluntarios apostados en los m´argenes de la carretera les dan dentro de
unas bolsas.
La organizaci´on de esas etapas debe decidir en qu´e punto kilom´etrico colocan la zona de avitualla-
miento (el lugar en el que se les proporciona las bolsas). El sentido com´un indica que:
• Debe ser en una zona llana (si es subida los ciclistas estar´an m´as concentrados en pedalear que en
comer; si es bajada estar´an atentos a no caerse con el incremento de velocidad).
• La zona llana debe ser la m´as larga que haya en toda la etapa, para darles tiempo a comer con
calma.
Para tomar esta decisi´on, te han pedido ayuda. Ellos te dar´an la altura sobre el nivel del mar a la
que empieza cada uno de los kil´ometros de la etapa, y tendr´as que decidir en qu´e punto kilom´etrico se
deber´an colocar los encargados del avituallamiento.
Como ejemplo, supongamos una etapa de 4 kil´ometros, en la que la salida, situada en el punto
kilom´etrico (P.K.) 0, est´e sobre el nivel del mar, los P.P.K.K. 1, 2 y 3 est´en a una altura de 50 metros y,
por ´ultimo, la meta (P.K. 4), est´e a 100 metros de altitud. Con esta configuraci´on de etapa, existen dos
kil´ometros llanos (que comienzan en los P.P.K.K. 1 y 2), por lo que el avituallamiento se colocar´a en el
P.K. 1, para que los corredores tengan dos kil´ometros completos para comer.
P.K. 0 P.K. 1 P.K. 2 P.K. 3 P.K. 4
50 m
100 m Avituallamiento
Entrada
La entrada est´a compuesta de m´ultiples etapas, cada una en una l´ınea. Una etapa est´a formada por
una secuencia de al menos dos n´umeros mayores o iguales que cero separados por espacios. Cada uno
de esos n´umeros representa la altura sobre el nivel del mar al principio del kil´ometro, siendo el primer
n´umero la altura al principio del primer kil´ometro (P.K. 0), el segundo al principio del segundo kil´ometro,
etc. Cada etapa termina con un -1 que no debe tenerse en cuenta en el c´alculo. La ´ultima altura v´alida
le´ıda es la altura a la que se encuentra la meta.
La entrada termina con una “etapa vac´ıa”, es decir una l´ınea que contiene ´unicamente un -1.
Salida
Para cada etapa se escribir´a una l´ınea donde aparecer´an dos n´umeros separados por un espacio: el
punto kilom´etrico donde colocar el punto de avituallamiento y el n´umero de kil´ometros llanos que tienen
por delante.
Ten en cuenta que:
• El principio de la etapa se considera el kil´ometro cero.
• Si hay m´as de una zona candidata, se elegir´a la que ocurra antes en la etapa.
• Si en la etapa no hay ninguna zona llana, se escribir´a HOY NO COMEN.

Entrada de ejemplo
0 50 50 50 100 -1
10 10 -1
0 5 -1
0 50 50 100 100 -1
0 50 50 3 3 3 -1
-1
Salida de ejemplo
1 2
0 1
HOY NO COMEN
1 1
3 2
