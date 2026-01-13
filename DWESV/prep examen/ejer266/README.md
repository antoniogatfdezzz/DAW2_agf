Problema n´umero 266
Copistas dalt´onicos
Tiempo m´aximo: 1,000-5,000 s Memoria m´axima: 4096 KiB
http://www.aceptaelreto.com/problem/statement.php?id=266
El daltonismo es un transtorno hereditario que ocasiona dificultad para distinguir ciertos colores.
Hay distintos tipos de daltonismo que hacen que los colores que esas personas no distinguen var´ıen. Lo
habitual, no obstante, es no distinguir algunos matices de verde y rojo.
Aunque el defecto gen´etico no suele suponer ning´un problema en la vida diaria de los afectados, la
realidad es que les imposibilita para realizar algunos trabajos puntuales, como militares de carrera o
pilotos.
Otro de los trabajos dif´ıciles de realizar por los dalt´onicos es el de duplicador de obras de arte. El
problema al hacer la copia de la obra es que esos dos colores que cualquier otro ver´ıa distintos terminan
siendo el mismo en la copia. Si esa copia cae en las manos de un segundo duplicador dalt´onico que hace
una nueva copia, y luego otro, y luego otro, el resultado final puede no parecerse en nada al original,
sobre todo si el tipo de daltonismo de cada uno difiere1.
Como ejemplo, en la figura aparece la transformaci´on que puede sufrir un cuadro de pixel art con uno
de los personajes del Pacman tras el paso por varios copistas dalt´onicos. Los colores originales son azul,
rojo, blanco y negro. En la primera reproducci´on el copista sufr´ıa un tipo de daltonismo que le hac´ıa
ver igual los colores azul y rojo lo que hace que todos los azules terminen siendo rojos2. En la segunda
reproducci´on el copista convirti´o todos los rojos en amarillo. El ´ultimo de la serie ve´ıa todos los negros
grises.
Lo que haremos ser´a, precisamente, simular esa transformaci´on de la obra original tras pasar por las
manos de numerosos copistas dalt´onicos.
Entrada
La entrada estar´a compuesta por distintos casos de prueba. Cada caso de prueba comienza con la
descripci´on de un cuadro que ser´a copiado en serie por distintos dalt´onicos.
Los cuadros se representar´an mediante letras may´usculas, cada uno representando un color. Para
eso una primera l´ınea contendr´a el tama˜no del cuadro: dos n´umeros entre 1 y 500 indicando el n´umero
de filas y el n´umero de columnas respectivamente, a lo que seguir´a el cuadro. Acto seguido aparecer´a
una l´ınea con el n´umero de dalt´onicos que copiar´an el cuadro. Por ´ultimo por cada uno de los copistas
aparecer´a una l´ınea con dos caracteres; el primero indica el c´odigo del color que no es capaz de distinguir
y que es sustituido por el c´odigo del color marcado por el segundo car´acter.
La salida terminar´a con un cuadro de tama˜no 0×0 que no debe procesarse.
Salida
Para cada caso de prueba se escribir´a el cuadro tal y como lo dibuja el ´ultimo copista utilizando la
misma representaci´on usada en la entrada.
1Aunque sorprendente, eso no resulta tan inveros´ımil. Hay estudiosos que afirman que Vincent Van Gogh, el famoso
pintor postimpresionsta, era dalt´onico.
2Los colores del ejemplo est´an elegidos para minimizar las posibilidades de que un lector dalt´onico sufra el daltonismo
indicado y cualquiera pueda entender el ejemplo sin confusi´on.

Entrada de ejemplo
1 4
ABCD
1
C D
9 9
AAAAAAAAA
AARRRRRAA
ARRRRRRRA
ARBBRBBRA
ARNBRNBRA
ARRRRRRRA
ARRRRRRRA
ARARARARA
AAAAAAAAA
3
A R
R Y
N G
0 0
Salida de ejemplo
ABDD
YYYYYYYYY
YYYYYYYYY
YYYYYYYYY
YYBBYBBYY
YYGBYGBYY
YYYYYYYYY
YYYYYYYYY
YYYYYYYYY
YYYYYYYYY
