Problema n´umero 110
Estrofas

Historial ayer borrado,
anteayer hubo pecado.
El texto anterior es un pareado: una estrofa con dos versos que riman entre s´ı con rima consonante.
¿Sabr´ıas hacer un programa que identifique distintos tipos de estrofa?
En concreto, nos bastar´a con identificar las rimas (no tendremos en cuenta el n´umero de s´ılabas de
cada verso), existiendo dos rimas distintas:
• Rima consonante: se dice que entre dos versos hay rima consonante cuando todos los sonidos, tanto
vocales como consonantes, riman. Para las comparaciones se tienen en cuenta todos los sonidos a
partir de la ´ultima vocal acentuada.
• Rima asonante: similar a la anterior pero ´unicamente riman las vocales.
Por ejemplo, el siguiente cuarteto de Diego de Silva y Mendoza:
Una, dos, tres estrellas, veinte, ciento, (A)
mil, un mill´on, millares de millares, (B)
¡v´algame Dios, que tienen mis pesares (B)
su retrato en el alto firmamento ! (A)
tiene esquema ABBA consonante, pues coinciden las vocales y consonantes del primer y ´ultimo verso,
as´ı como las del segundo y tercero.
Nos piden ser capaces de identificar los siguientes tipos de estrofa:
• De dos versos:
– Pareado: rima consonante AA.
• De tres versos:
– Terceto: rima consonante en el primer y ´ultimo verso (A-A). Ten en cuenta que AAA no se
considerar´a terceto.
• De cuatro versos:
– Cuarteto: rima consonante ABBA.
– Cuarteta: rima consonante ABAB.
– Seguidilla: rima asonante en los pares (-a-a). Ten en cuenta que otras combinaciones con m´as
rimas o con rima consonante en lugar de asonante (por ejemplo -aaa o -A-A) no se consideran
seguidillas.
– Cuaderna via: rima consonante igual en todos los versos (AAAA).

Entrada
La entrada estar´a formada por un n´umero indeterminado de casos de prueba. Cada caso de prueba
comienza con una l´ınea que contiene un ´unico entero con el n´umero de versos del siguiente poema. A
continuaci´on aparecen tantas l´ıneas como versos contiene la estrofa a analizar. Podemos asumir que la
´ultima palabra de cada verso es llana (la vocal acentuada est´a en la pen´ultima s´ılaba), y que ninguno
tendr´a m´as de 70 letras. La entrada no contendr´a tildes para facilitar la programaci´on, aunque esto
signifique cometer errores ortogr´aficos. Tampoco tendremos en cuenta que distintos elementos gr´aficos
pueden tener el mismo sonido. Es decir, un verso terminado en -aba, no rimar´a de forma consonante con
un verso terminado en -ava.
La entrada termina cuando el siguiente caso de prueba contiene 0 versos. Para ese caso de prueba
no se generar´a ninguna salida.
Salida
Para cada caso de prueba el programa indicar´a el nombre de la estrofa, utilizando may´usculas
(PAREADO, TERCETO, CUARTETO, CUARTETA, SEGUIDILLA, CUADERNA VIA) o la palabra DESCONOCIDO si
no conoce la estrofa dada.
Entrada de ejemplo
2
Historial ayer borrado
anteayer hubo pecado
2
Esto no pega
ni con cola.
4
Era un simple clerigo, pobre de clerecia,
dicie cutiano missa de la sancta Maria;
non sabie decir otra, diciela cada dia,
mas la sabie por uso qe por sabiduria.
3
Un manotazo duro, un golpe helado,
un hachazo invisible y homicida,
un empujon brutal te ha derribado.
0
Salida de ejemplo
PAREADO
DESCONOCIDO
CUADERNA VIA
TERCETO
Notas
El enunciado ha hecho simplificaciones en las definiciones de las estrofas encaminadas a hacer el
ejercicio m´as sencillo; ejemplos de esto son no considerar el n´umero de s´ılabas, manejar s´olo palabras
llanas, tener faltas de ortograf´ıa, etc. El resultado ha sido unas definiciones que poco tienen que ver con
las aceptadas en la literatura. Por favor, no utilices el programa final delante de un experto en poes´ıa.
