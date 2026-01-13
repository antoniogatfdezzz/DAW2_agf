Problema n´umero 108
De nuevo en el bar de Javier

Tras las medidas tomadas, Javier ha visto que las ventas de su bar han mejorado bastante, as´ı que ha
decidido seguir adelante con su estudio. Ahora le gustar´ıa investigar con qu´e productos gana m´as dinero
y con cu´ales gana menos. Adem´as, tambi´en le gustar´ıa saber si las ventas en comidas superan la media.
Para ello ha establecido varias categor´ıas:
C´odigo Categor´ıa
D Desayunos
A Comidas
M Meriendas
I Cenas
C Copas
Javier encuadra cada venta que realiza dentro de una de esas categor´ıas. Cuando tiene un momento,
pasa los datos de todas las ventas al ordenador, y le gustar´ıa que le devolviese los siguientes valores: la
categor´ıa que m´as dinero ha recaudado, la que menos, y si el dinero conseguido con las comidas supera
la media. No es demasiado constante registrando datos, pero nunca deja un d´ıa a medias de introducir.
Realiza un programa que ayude a Javier en su cometido.
Entrada
El programa recibir´a una lista de ventas realizadas. Cada una constar´a de una categor´ıa (D, A, M, I
o C) y un valor (real). Cuando el d´ıa termina, Javier introduce una categor´ıa inexistente (N) con valor
cero (es decir, N 0).
Salida
Para cada d´ıa, el programa generar´a una l´ınea que contendr´a tres valores separados por la almohadilla
(“#”). Los dos primeros indicar´an el nombre de las categor´ıas que han supuesto m´as y menos beneficios
respectivamente (ten en cuenta que si de una categor´ıa no se ha vendido nada, su beneficio es cero);
las categor´ıas se indicar´an con sus nombres, DESAYUNOS, COMIDAS, MERIENDAS, CENAS o COPAS. El tercer
valor de la l´ınea indicar´a “SI” si la media gastada por los clientes en las comidas super´o a la media de
ventas del d´ıa, y “NO” en caso contrario.
En caso de que existan varias categor´ıas que hayan conseguido el m´aximo o m´ınimo de ventas, se
especificar´a “EMPATE”.
Entrada de ejemplo
D 2.80
C 48.00
A 8.00
N 0
D 15.33
A 60.00
M 12.00
I 25.00
N 0
Salida de ejemplo
COPAS#EMPATE#NO
COMIDAS#COPAS#SI
