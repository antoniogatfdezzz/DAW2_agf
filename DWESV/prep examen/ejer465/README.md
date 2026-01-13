Problema n´umero 465
El cuello de los pilotos

Cualquiera con dos dedos de frente entiende que los ciclistas deben
cuidar sobre todo sus rodillas y los tenistas el codo. Lo que es menos
conocido es la importancia del cuello en los pilotos de F´ormula 1.
La necesidad de un cuello en forma en estos conductores se debe a
las fuertes aceleraciones y deceleraciones que sufren durante las carreras,
especialmente en las curvas.
Por eso cuando se incorpora un nuevo circuito en el calendario del
mundial sus curvas se analizan minuciosamente. Aunque en un estudio
real se miden muchos m´as factores, nosotros nos conformaremos con contar el n´umero de curvas a la
izquierda y a la derecha que tienen esos nuevos circuitos.
Entrada
La entrada est´a compuesta por distintos casos de prueba, cada uno representando el mapa de un
circuito cuyas curvas hay que contar.
La descripi´on de cada mapa comienza con una l´ınea con dos n´umeros tx y ty que indican su ancho y
alto (un m´ınimo de 3 y un m´aximo de 100 unidades por cada dimensi´on).
A continuaci´on aparecen ty l´ıneas, con tx caracteres cada una. Cada car´acter puede ser un punto (“.”)
que indica campo y una almohadilla (“#”) que indica una secci´on del circuito. Dentro de ´este, el car´acter
“O” (o may´uscula) marca la posici´on desde la que comienza la carrera, que nunca estar´a situada en una
curva.
En nuestros circuitos los coches siempre van en horizontal o vertical (nunca en diagonal) y recorren
el circuito en sentido de las agujas del reloj.
Salida
Por cada caso de prueba se escribir´a una ´unica l´ınea con dos n´umeros separados por un espacio. El
primero indica las curvas hacia la izquierda y el segundo las curvas hacia la derecha que deben hacer los
pilotos en el circuito.
Entrada de ejemplo
3 3
#O#
#.#
###
15 5
..#####..###...
..#...####.##..
###.........#..
O.....####..#..
#######..####..
Salida de ejemplo
0 4
6 10
