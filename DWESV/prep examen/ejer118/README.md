Problema n´umero 118
Apuesta con recetas

Pedro ha recibido el siguiente correo:
“ Has sido invitad@ a un intercambio de recetas.
Espero que participes, escog´ı a quien me pareci´o que se iba a divertir con esto.
1. Por favor, env´ıa una receta a la persona cuyo nombre figura como n´umero
1 aqu´ı abajo (aunque no la conozcas). Debe ser algo r´apido, f´acil y con in-
gredientes corrientes. De hecho, la mejor receta es aquella que conoces de
memoria y que eres capaz de escribir y enviar de inmediato. No te preocupes
mucho y env´ıa aquella que haces cuando tienes poco tiempo para entretenerte.
1. Despu´es de enviar la receta a la persona con el n´umero 1 de aqu´ı abajo, y s´olo
a esa persona, copia esta carta en un nuevo correo, coloca mi nombre en la
primera posici´on y pon el tuyo en el n´umero 2. S´olo deben aparecer tu nombre
y el m´ıo cuando env´ıes tu mensaje.
1. Env´ıa esto a 8 amigos. Si no consigues hacerlo en 5 d´ıas, comun´ıcamelo para
ser justo con los participantes.
Deber´ıas recibir muchas recetas. ¡Es divertido ver de d´onde vienen!
Raramente las personas desisten ya que todos agradecemos nuevas ideas. El retorno
es r´apido ya que ´unicamente hay 2 nombres en la lista y cada uno s´olo lo tiene que
hacer una vez.
POSICI ´ON 1 : javier@acmicpc.org
POSICI ´ON 2 : luis@swerc.eu
”
Pedro se lo cuenta a sus amigos Pilar y Marco un d´ıa tomando caf´e y, casualmente, ellos tambi´en
han recibido el mismo mensaje. Tienen curiosidad por saber cu´antas recetas podr´ıan recibir si env´ıan
los correos a las 8 personas que dicta el mensaje.
No se ponen de acuerdo en si les llegar´ıan muchas o pocas recetas, de modo que deciden hacer una
apuesta al respecto. Cada uno debe aventurar cual ser´a la media de recetas recibidas por los tres. Para
que no haya ventaja por parte de ninguno de los amigos, cada uno de ellos escribir´a en un papel, de forma
secreta, su apuesta. Cuando se han anotado las tres, se hacen p´ublicas. Al cabo de un mes realizar´an un
recuento de las recetas recibidas y calcular´an la media. Aqu´el cuya apuesta se aleje m´as de dicha media
tendr´a que hacer una cena a los otros dos con algunas de las recetas conseguidas.
En cuanto lo ha pensado un poco, nuestro amigo Pedro se ha dado cuenta de una cosa: en este juego
ganar no sirve de nada, porque no hay premio; lo importante es no perder, para no tener que cocinar.
A Pedro le pone nervioso tener que cocinar para otros, as´ı que, a la vista de las apuestas, quiere evitar
perder a toda costa.
Afortunadamente, puede hacer c´omplice a muchos amigos (y amigos de amigos) para que le ayuden
a recibir, de acuerdo a las reglas del mensaje, el n´umero de recetas que ´el les pida. Para evitar que
Pilar y Marco noten la trampa, les quiere pedir un n´umero de recetas que garantice que no pierde nunca
(independientemente de las recetas recibidas por Pilar y Marco), pero que le deje lo m´as cerca posible
de la media, sin perder, para no despertar sospechas.
Los tres amigos son lo suficientemente avispados para no realizar apuestas imposibles, por encima
del l´ımite que les impone el n´umero de amigos a los que env´ıan el correo.
Realiza un programa que ayude a Pedro a solucionar su problema.

Entrada
Como entrada se recibir´an m´ultiples casos de prueba. Cada uno estar´a compuesto de los tres valores
enteros mayores o iguales que 0 que forman la apuesta. El primero de ellos ser´a el n´umero de recetas
apostado por Pilar, el segundo por Marco y el ´ultimo por Pedro. La entrada finalizar´a cuando la triada
comience por un valor negativo.
Salida
Para cada caso de prueba, el programa escribir´a el n´umero de recetas que deber´a recibir Pedro para,
sin levantar sospechas, no perder. Si es imposible que Pedro pierda, o el juego se considera nulo porque
hay coincidencia en las tres apuestas, no molestar´a a ninguno de sus amigos, por lo que el programa
deber´a escribir 0 (es decir, un cero). Si no existe un n´umero de recetas que permita no perder nunca a
Pedro, se mostrar´a la letra I. Se tendr´a en cuenta que un empate entre dos de los jugadores significar´a
perder, pues ambos tendr´an que cocinar para el tercero. As´ı, por ejemplo, si otro participante hace
la misma apuesta que Pedro, ´este tendr´a que intentar que pierda el participante que ha introducido la
apuesta distinta.
Entrada de ejemplo
5 17 32
5 32 17
31 17 5
60 55 50
-1 -1 -1
Salida de ejemplo
56
0
I
36
