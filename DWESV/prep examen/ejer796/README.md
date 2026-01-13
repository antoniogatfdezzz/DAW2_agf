Problema n´umero 796
Notaci´on Forsyth-Edwards

Antiguamente los peri´odicos en papel tra´ıan, en su secci´on de
pasatiempos, un entretenimiento para su p´ublico m´as intelectual.
En concreto, se trataba del estado de un tablero de ajedrez cerca del
final de alguna partida hist´orica (muchas veces inclu´ıan referencias
sobre los jugadores, en qu´e competici´on se vio ese tablero e incluso
alguna informaci´on adicional sobre la partida) para que el lector
intentara deducir el movimiento ganador; era habitual que debajo
pusiera cosas como “Las blancas juegan y ganan”.
Pero eso era, claro, cuando en los peri´odicos impresos se hab´ıa
generalizado el uso de fotograf´ıas, dibujos y gr´aficos. Si nos vamos
al siglo XIX la situaci´on era bien distinta y lo habitual era que
esos mismos tableros fueran representados en texto. El periodista
David Forsyth invent´o por aquel entonces una forma de describir un
tablero que despu´es se populariz´o tras la extensi´on que hizo Steven
J. Edwards para a˜nadir informaci´on adicional y utilizarla en ordenadores. Es la conocida notaci´on
Forsyth-Edwards o, usando las siglas en ingl´es, FEN.
En esa notaci´on la posici´on del tablero se representa con una ´unica l´ınea con 8 bloques de caracteres.
Cada uno de esos bloque representa una fila del tablero (comenzando con la fila superior) y se separan
por una barra (/). Cada una de las filas se describen de izquierda a derecha de forma que:
• Las letras que representan cada tipo de pieza son las iniciales de su nombre en ingl´es: p para
peones, n para caballos, r para torres, b para alfiles, q para las reinas y k para los reyes.
• Las piezas blancas se dan utilizando esas iniciales en may´uscula y las negras en min´uscula.
• La aparici´on en la fila de uno o m´as escaques (o casillas) vac´ıas se representan con n´umero del 1 al
8 indicando cu´antos hay seguidos.
Como ejemplo, la cadena rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR representa la posici´on
inicial de una partida utilizando la notaci´on FEN.
Sin embargo, s´olo con el estado del tablero no es suficiente para poder continuar una partida; sin
ir m´as lejos no se sabe qui´en es el siguiente en mover. De ah´ı que la notaci´on incorpore informaci´on
adicional sobre el transcurso de la partida (turno, enroques y alguna otra cosa).
Considerando, no obstante, ´unicamente la posici´on del tablero y asumiendo que todas las piezas
podr´ıan moverse, ¿cu´antos escaques del tablero no est´an atacadas por ninguna?
A modo de recordatorio los movimientos de las piezas del ajedrez que consideramos son los siguientes:
• El pe´on (p) puede avanzar ´unicamente una posici´on en vertical (hacia arriba si es blanco, hacia
abajo si es negro) a cualquiera de las dos columnas adyacentes movi´endose en diagonal. Tambi´en
puede avanzar sin cambiar de columna pero ´unicamente cuando esa casilla no tiene pieza, por lo
que no cuenta como casilla atacada.
• El caballo (n) puede saltar dos casillas en una direcci´on (horizontal o vertical) y una en la otra.
• La torre (r) puede moverse cualquier distancia tanto en horizontal como en vertical siempre y
cuando no haya ninguna pieza en su camino.
• El alfil (b) es similar a la torre pero movi´endose por diagonales en lugar de por filas y columnas.
• La reina (q) puede hacer los mismos movimientos que torres y alfiles.
• El rey (k) puede moverse en cualquier direcci´on, como la reina, pero solo a las casillas adyacentes.

Entrada
La entrada comienza con una l´ınea que indica el n´umero de tableros que vendr´an a continuaci´on.
Cada uno de ellos aparecer´a en una l´ınea independiente y contendr´a la representaci´on del tablero en la
notaci´on FEN descrita.
Ten en cuenta que los tableros no tienen por qu´e representar estados de partidas v´alidos y que se
permiten todos los movimientos de las fichas descritos incluso aunque eso conlleve que un jaque mate (o
salir de ´el).
Salida
Por cada tablero se escribir´a una l´ınea con el n´umero de casillas del tablero que no est´an atacadas
(ni ocupadas) por alguna pieza.
Entrada de ejemplo
4
8/8/8/8/8/8/8/8
r2p2P1/8/8/8/8/8/8/8
rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR
r4rk1/p2b1q2/2p2B1p/1pn1Ppp1/8/3B4/PPP3QP/4RKR1
Salida de ejemplo
64
50
16
4
Notas
El ´ultimo caso del ejemplo se corresponde con el tablero de la imagen, extra´ıdo de un antiguo peri´odico
en el que tambi´en dec´ıan:
“ Las blancas juegan y ganan.
Esta posici´on reproduce un momento de una partida Barton - Raindle. Londres,
1.    La debilidad del enroque negro es palpable y puede ser explotada por el
jugador son un en´ergico sacrificio.
La correcci´on de la entrega se demuestra en las dos principales variantes que pueden
producirse, por la existencia de sendos elegantes mates, muy similares en su eje-
cuci´on.
”
