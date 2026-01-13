Problema n´umero 727
Cumplea˜nos en el jard´ın de infancia
Tiempo m´aximo: 1,000-4,000 s Memoria m´axima: 32768 KiB
http://www.aceptaelreto.com/problem/statement.php?id=727
Las din´amicas que se crean en las aulas de las escuelas infantiles alrededor
de los cumplea˜nos de los cr´ıos son una locura. Para que no haya envidias
ni se creen conflictos entre ellos, todos los ni˜nos celebran su cumplea˜nos e
invitan a todos los dem´as de la clase. Eso supone un caos de regalos que
crece de forma exponencial con su tama˜no.
Para poner fin a esta locura, Justo Reparto, el padre de una de las ni˜nas
que asiste al jard´ın de infancia “Acepto el reto˜no” ha tenido una idea para
el curso que viene. En lugar de tanta celebraci´on, ha propuesto hacer solo
una, aunque sea a lo grande, en la que todo el mundo se d´e por celebrado, felicitado y regalado. Para
eso, cada ni˜no aportar´a a la celebraci´on un regalo. Luego los regalos se repartir´an entre todos, de modo
que a cada peque le toque uno, sea el que sea. De esa forma tambi´en aprender´an que no hay juguetes de
ni˜no y juguetes de ni˜na, porque podr´ıa ocurrir que su hija terminara llev´andose un cami´on de bomberos,
y el peque˜no Mambr´u Tote una mu˜neca.
Aceptada la idea, se ha decidido que para repartir los regalos entre los ni˜nos, se usar´a un amidakuji (o
Ghost leg), un juego japon´es de reparto de n objetos entre n personas. Se comienza con una hoja de papel
en la que se escriben en la parte superior los nombres de las personas, y, alineados en la parte inferior,
los objetos, conectando con una l´ınea vertical un objeto con una persona. A continuaci´on se incorporan,
de forma arbitraria, l´ıneas horizontales para conectar dos l´ıneas verticales adyacentes, garantizando, eso
s´ı, que no haya l´ıneas horizontales que se toquen.
Para saber qu´e regalo le toca a cada uno, se siguen las l´ıneas de arriba hacia abajo sabiendo que
cada vez que se alcanza una l´ınea horizontal hay que seguirla para cambiar de columna de descenso.
Ana Blanca Carlos Mambr´u
Libro Disfraz Mu˜neca Cami´on
Paso 1: l´ıneas verticales
Ana Blanca Carlos Mambr´u
Libro Disfraz Mu˜neca Cami´on
Paso 2: l´ıneas horizontales
Ana Blanca Carlos Mambr´u
Libro Disfraz Mu˜neca Cami´on
Paso 3: resoluci´on
Para evitar que se haga trampa, Justo Reparto pondr´a las l´ıneas verticales, los nombres y los regalos
en el orden que le parezca, y los tapar´a. Luego los ni˜nos pondr´an las l´ıneas horizontales sin saber qu´e
est´an uniendo. Justo tiene la idea, aunque no lo haya dicho, de colococar en la misma columna a cada
ni˜no y el regalo que ha tra´ıdo, de modo que, si no se ponen l´ıneas horizontales, todos se vayan a casa
con su propio regalo. Adem´as, colocar´a los regalos ordenados por su valor empezando, a la izquierda,
con el m´as barato.
Entrada
La entrada comienza con un n´umero que indica cu´antos casos de prueba tendr´an que procesarse.
Cada uno comienza con un n´umero 2 ≤ N ≤ 200.000 indicando cu´antos ni˜nos asisten al cumplea˜nos,
cada uno con un regalo.
Despu´es aparecen N –1 l´ıneas, una por cada columna del amidakuji, de izquierda a derecha, salvo
para la ´ultima. Cada una comienza con el n´umero de l´ıneas horizontales que salen de esa columna hacia
la siguiente a su derecha. A continuaci´on aparece un n´umero por cada conexi´on, indicando la distancia
a la que est´a de la parte superior de la figura.

En ning´un caso hay m´as de 200.000 l´ıneas horizontales, y ninguna est´a a una distancia mayor de
109 del inicio de las columnas. Todas las conexiones son completamente horizontales y se garantiza que
no existir´a ninguna columna con dos o m´as conexiones a la misma altura (hacia la izquierda o hacia la
derecha).
Salida
Por cada caso de prueba se escribir´a una l´ınea con tres n´umeros separados por espacio. El primero
indica cu´antos ni˜nos obtienen un regalo peor que el que aportaron (queda a la izquierda de su posici´on),
el segundo indica cu´antos se llevan el regalo que llevaron a la celebraci´on, y el tercero cu´antos se llevan
un regalo mejor que el que entregaron (queda a la derecha).
Entrada de ejemplo
3
4
1 1
2 2 3
3 5 1 4
2
1 1
4
1 1
1 2
0
Salida de ejemplo
2 0 2
1 0 1
2 1 1
