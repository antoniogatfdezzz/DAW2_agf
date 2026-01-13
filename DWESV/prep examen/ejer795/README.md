Problema n´umero 795
Carteles en papel continuo

Hace muchos, muchos a˜nos, cuando los ordenadores comenzaban a llegar
a las casas, las impresoras que hab´ıa en el mercado utilizaban una tecnolog´ıa
muy diferente a la de ahora. Se conoc´ıan como impresoras matriciales (o de
matriz de puntos). Ten´ıan muy poca versatilidad para imprimir gr´aficos (y
ninguna versatilidad para imprimir en color) y desde el punto de vista de su
programaci´on, estaban m´as cerca de las antiguas m´aquinas de escribir que de
las impresoras modernas.
Tanto es as´ı que el tipo de letra utilizado para imprimir los textos no era
responsabilidad del software del ordenador, sino que el conjunto de fuentes
ven´ıa en el firmware de la impresora por lo que la apariencia final del texto
depend´ıa de d´onde se imprimiera.
Lo bueno de aquellos viejos d´ıas era que hab´ıa poca diferencia entre im-
primir un texto en la pantalla o en la impresora. Utilizando el por aquel
entonces omnipresente lenguage BASIC, la diferencia era una ´unica L:
PRINT "Esto sale por la pantalla"
LPRINT "Esto se imprime en la impresora"
Otra ventaja era la posibilidad de utilizar papel continuo que consist´ıa en grupos de 500, 1000 o
incluso m´as hojas plegadas y unidas en una “´unica hoja sin fin” de forma que no hab´ıa que alimentar
continuamente la impresora con folios. Eso permit´ıa, adem´as, imprimir carteles en largas tiras de papel
que luego se pod´ıan colgar directamente en la pared sin tener que unir las p´aginas independientes.
Para esto ´ultimo, eso s´ı, el programa (en BASIC o cualquier otro lenguaje) ten´ıa que encargarse de
rotar el banner que se quer´ıa imprimir para mandar las l´ıneas a la impresora de forma que salieran en
vertical en lugar de en horizontal.
Entrada
La entrada est´a compuesta de distintos casos de prueba representando cada uno un cartel que hay
que mandar imprimir.
Cada cartel comienza con una l´ınea con dos n´umeros indicando el n´umero de columnas y l´ıneas del
cartel que se quiere imprimir (como mucho 40). A continuaci´on aparece el cartel rodeado de un “marco”
hecho con guiones y l´ıneas verticales (- y |) de forma que antes de la primera l´ınea del cartel y despu´es
de la ´ultima aparece una l´ınea con guiones y antes y despu´es del ´ultimo caracter de cada l´ınea aparece
una l´ınea vertical.
El cartel estar´a compuesto de los caracteres habituales en la ´epoca de las impresoras matriciales:
adem´as del espacio, podr´a haber letras del alfabeto ingl´es, n´umeros, signos de puntuaci´on, par´entesis y
operadores aritm´eticos, como el signo de suma, resta, multiplicaci´on o divisi´on.
Tras el ´ultimo caso de prueba aparece una l´ınea con dos ceros.
Salida
Por cada caso de prueba se escribir´a el cartel rotado 90 grados en sentido contrario al seguido por
las agujas del reloj. Se colocar´a tambi´en un marco a su alrededor siguiendo el mismo formato que en la
entrada.

Entrada de ejemplo
1 1
---
|*|
---
3 2
-----
|***|
|* |
-----
0 0
Salida de ejemplo
---
|*|
---
----
|* |
|* |
|**|
----
