Problema n´umero 596
Codificaci´on l´ımite

El env´ıo de mensajes cifrados para evitar miradas indiscretas se lleva estudiando desde la antig¨uedad.
El m´etodo m´as simple consiste en manejar tablas de traducci´on que contienen, para cada letra, por qu´e
otra letra se sustituir´a en el mensaje cifrado.
Existe otro mecanismo simple que consiste en simplemente a˜nadir caracteres aleatorios entre las letras
del mensaje. El m´etodo que hoy proponemos utiliza este sistema. Adem´as, requiere un peque˜no esfuerzo
adicional por parte del lector pues el mensaje recompuesto no contiene espacios separando las palabras,
por lo que deber´a ser ´el el que infiera, en el momento de leer, d´onde empieza y termina cada una.
El procedimiento comienza con un mensaje cifrado como el siguiente: “xb..zu..t.u..”. Ese mensaje
lo interpretaremos como un ´arbol binario de caracteres donde el primer car´acter simboliza la ra´ız y a
continuaci´on aparecen el hijo izquierdo y el hijo derecho, teniendo en cuenta que el ´arbol vac´ıo est´a
representado por un punto ‘.’. El mensaje del ejemplo representa el siguiente ´arbol (donde se han
omitido los ´arboles vac´ıos):
x
b z
u t
u
El mecanismo de codificaci´on l´ımite lo que hace es quedarse con el l´ımite o frontera del ´arbol (las
hojas de izquierda a derecha), y escribirlas.
Entrada
La entrada consiste en diversas l´ıneas, cada una con un mensaje codificado utilizando la codificaci´on
l´ımite. Se garantiza que el mensaje ser´a un ´arbol binario v´alido, que no tendr´a m´as de 5.000 caracteres
y cuya altura no ser´a mayor de 3.000. Los nodos del ´arbol contienen caracteres de la ‘a’ a la ‘z’.
Salida
Para cada caso de prueba, escribir una l´ınea con el mensaje descifrado.
Entrada de ejemplo
abh...ko..nl..a..
xb..zu..t.u..
Salida de ejemplo
hola
buu
