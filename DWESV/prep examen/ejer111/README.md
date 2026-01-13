Problema n´umero 111
Aprobar qu´ımica
Tiempo m´aximo: 1,000 s Memoria m´axima: 4096 KiB
http://www.aceptaelreto.com/problem/statement.php?id=111
A Jonathan le han mandado en el instituto que realice un problema de configuraci´on electr´onica.
La verdad es que con qu´ımica est´a bastante perdido, y ha decidido que no va a ser ´esta la asignatura
que le deje fuera de la universidad. Teniendo en cuenta que en el examen no es crucial para la nota la
realizaci´on de un ejercicio de este tipo, pero que no se les permite aprobar si no entregan bien realizados
todas las actividades propuestas en clase, ha decidido pedirle ayuda a su hermano que est´a estudiando
un Ciclo Formativo de Grado Superior de Inform´atica.
Lo primero que hace Jonathan es explicarle a su hermano el ejercicio, que consiste en indicar de
qu´e forma se distribuyen los electrones de los ´atomos o elementos qu´ımicos. Para eso, le cuenta, se
utiliza el diagrama de Mo¨eller que determina en qu´e orden se van completando los subniveles de cada
orbital. La idea intuitiva es que para saber c´omo se distribuyen los N electrones de un ´atomo concreto
debemos pensar d´onde se coloca el primer electr´on, despu´es d´onde se coloca el segundo, etc., hasta llegar
al ´ultimo. La tabla 1.a muestra todos los “huecos” posibles donde se pueden colocar. Sus nombres son
una combinaci´on del n´umero nivel (1. . . 7) y del orbital (s, p, d o f).
s p d f
n=1 1s
n=2 2s 2p
n=3 3s 3p 3d
n=4 4s 4p 4d 4f
n=5 5s 5p 5d 5f
n=6 6s 6p 6d
n=7 7s 7p
(a) Nombres de los subniveles
1s
2s 2p
3s 3p 3d
4s 4p 4d 4f
5s 5p 5d 5f
6s 6p 6d
7s 7p

 

 



















(b) Orden en el que se completan
Figura 1: Diagrama de Mo¨eller
La figura 1.b muestra en qu´e orden se van completando los subniveles. Como se ve, consiste en
recorrer los subniveles de forma diagonal de arriba a abajo y de derecha a izquierda:
1s 2s 2p 3s 3p 4s 3d 4p 5s 4d 5p 6s 4f 5d 6p 7s 5f 6d 7p
Lo ´ultimo que hay que tener en cuenta es el n´umero de electrones que entran en cada subnivel: el
subnivel s puede llenarse con 1 ´o 2 electrones. El subnivel p puede contener de 1 a 6 electrones, el d de
1 a 10 electrones y el subnivel f de 1 a 14 electrones:
Orbital s p d f
Electrones 2 6 10 14
Para que todo le quede m´as claro, Jonathan le ense˜na a su hermano un ejercicio que han realizado
en clase: la configuraci´on electr´onica del Rubidio que tiene 37 electrones (en la terminolog´ıa qu´ımica se
dice que el n´umero at´omico del Rubidio es Z =37).
Siguiendo el diagrama de Mo¨eller, los dos primeros electrones ir´an en el subnivel 1s y los dos siguientes
en 2s. A continuaci´on se completar´a el 2p con seis electrones m´as (son los que entran en los orbitales
p). Los dos siguientes ir´an a parar a 3s y los siguientes seis a 3p. En este momento hemos colocado ya
18 electrones. Si continuamos con el proceso hasta colocar los 37 veremos que los 36 primeros electrones
completan todos los subniveles hasta el 4p y por tanto que el ´ultimo electr´on termina en el subnivel 5s.
El n´umero de electrones que quedan en cada subnivel es:

s p d
n=1 2
n=2 2 6
n=3 2 6 10
n=4 2 6
n=5 1
La forma de indicar la configuraci´on electr´onica es mostrar uno tras otro todos los subniveles que
tienen electrones utilizando el orden en el que se han ido rellenando. Adem´as, para cada subnivel se
indica el n´umero de electrones que han ca´ıdo en ´el. Para nuestro ejemplo ser´a:
1s2 2s2 2p6 3s2 3p6 4s2 3d10 4p6 5s1
El problema consiste en obtener la configuraci´on electr´onica de los elementos que nos vaya diciendo
Jonathan.
Entrada
La entrada consistir´a en una secuencia de casos de prueba, donde cada caso de prueba est´a formado
por dos l´ıneas: el nombre del elemento qu´ımico y su n´umero at´omico Z. El nombre del elemento qu´ımico
estar´a compuesto por una ´unica palabra y no tendr´a nunca m´as de 50 letras; por su parte, el n´umero
at´omico estar´a entre 0 y 118.
El programa terminar´a de recibir valores cuando el nombre del elemento sea “Exit”.
Salida
Para cada caso de prueba, el programa indicar´a la configuraci´on electr´onica del elemento introducido.
La configuraci´on electr´onica ser´a la lista de los subniveles en el orden en el que se van rellenando seguido
del n´umero de electrones que hay en ese subnivel. Cada subnivel se separar´a del anterior por un espacio
en blanco.
Si por un casual nos preguntan por el is´otopo del Hidr´ogeno que no tiene ning´un electr´on (Z =0),
escribiremos 1s0.
Entrada de ejemplo
Cloro
17
Calcio
20
Rubidio
37
Hierro
26
Exit
Salida de ejemplo
1s2 2s2 2p6 3s2 3p5
1s2 2s2 2p6 3s2 3p6 4s2
1s2 2s2 2p6 3s2 3p6 4s2 3d10 4p6 5s1
1s2 2s2 2p6 3s2 3p6 4s2 3d6
