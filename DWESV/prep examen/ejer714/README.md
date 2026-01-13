Problema n´umero 714
Fase final

El formato de la competici´on de un mundial deportivo tiene normalmente dos fases. La primera es
la llamada fase de grupos. En ella los equipos se organizan en varios grupos de pocos equipos, en los que
juegan todos contra todos en peque˜nas liguillas que determinan qu´e equipos pasan a la segunda ronda.
La segunda ronda, o fase final, se organiza como un torneo cl´asico eliminatorio, desde octavos de final
hasta la final. El ganador de cada partido pasa a la siguiente fase y el perdedor queda eliminado. Gana
el equipo que m´as goles marca y si el partido termina empatado, se juega un tiempo suplementario o
incluso una ronda de tiros desde el punto de penalty. Por ejemplo, en la Copa Mundial Femenina de
F´utbol de 2023 hubo 16 equipos en la fase final, que compitieron en octavos, cuartos, semifinal y final.
El equipo espa˜nol gan´o sus cuatro partidos (uno de ellos durante el tiempo suplementario), y se alz´o con
el campeonato.Jamaica 0
Colombia 1
Nigeria 2
Morocco 0
England (p) 4
France 4
Denmark 0
Australia 2
United States 4
Sweden (p) 5
Norway 1
South Africa 0
Japan 3
Netherlands 2
Spain 5
Switzerland 1
Netherlands 1
Spain 2
Japan 1
Sweden 2
Australia (p) 7
France 6
England 2
Colombia 1
England 3
Australia 1
Spain 2
Sweden 1
England 0
Spain 1
Dados los equipos clasificados a la fase final y el resultado de todos los encuentros, ¿qui´en gana?
Entrada
Cada caso de prueba comienza con un n´umero 2 ≤ N ≤ 64, siempre potencia exacta de 2, con el
n´umero de equipos clasificados a la fase final de un torneo. A continuaci´on aparecen, quiz´a en varias
l´ıneas, los nombres de los N equipos. Cada nombre es una ´unica palabra de no m´as de 30 letras inglesas
may´usculas y min´usculas.
Tras los nombres vienen los resultados de los N /2 encuentros de la primera fase, en el mismo orden
que los equipos, de manera que, por ejemplo, el primer resultado se corresponde con el enfrentamiento de
los dos primeros equipos. Tras los resultados de la primera fase, se proporcionan los N /4 resultados de
la segunda fase, siguiendo el mismo orden, hasta llegar al resultado de la final. Se garantiza que ning´un
equipo marca m´as de 20 goles y que nunca hay empate.
La entrada termina con un torneo sin equipos, que no debe procesarse.

Salida
Por cada caso de prueba el programa escribir´a el nombre del equipo ganador.
Entrada de ejemplo
16
Switzerland Spain Netherlands SouthAfrica Japan Norway Sweden UnitedStates
Australia Denmark France Morocco England Nigeria Colombia Jamaica
1 5 2 0 3 1 5 4 2 0 4 0 4 2 1 0 2 1 1 2 7 6 2 1 2 1 1 3 1 0
0
Salida de ejemplo
Spain
