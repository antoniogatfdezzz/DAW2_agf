Problema n´umero 103
Problemas de Herencia

Ca´ın y Abel han heredado el pedazo de tierra de su padre y deben proceder a dividirlo en dos partes
iguales. Sabiendo que la tierra es un cuadrado de una hect´area (un cuadrado de 1x1 hect´ometro), lo m´as
f´acil ser´ıa dividirlo directamente por la mitad.
Sin embargo, Ca´ın y Abel se han complicado un poco poniendo en pr´actica sus conocimientos
matem´aticos. En concreto, cada uno de ellos va proponiendo al otro una funci´on f(x) cuyo dibujo,
al evaluarse para x entre 0 y 1, divide la tierra de su padre en dos partes; la parte de abajo ir´a para Ca´ın
y la parte de arriba para Abel (figura 1.a).
1
1
Abel
Ca´ın
f (x) = x2
(a) Divisi´on de tierras
1
1
f (x) = x2
(b) C´alculo de Riemann
1
1
x3 + 2x2 − x
(c) Funci´on que toca tierras colindantes
Figura 1: Problema de herencia
Nuestro cometido es ayudarles a decidir si esas funciones dividen equitativamente el terreno (as´ı lo
consideraremos cuando el ´area que le queda a cada uno no excede en 0.001 hm2 la del otro). En una
palabra, deberemos decidir si sale ganando Ca´ın, Abel o el trato es justo.
Para poder realizar el c´alculo utilizaremos la soluci´on que aport´o el famoso matem´atico Riemann.
Riemann asegura que se puede aproximar el ´area que se encuentra limitada superiormente por una funci´on
por las llamadas sumas de Riemann. El m´etodo consiste en considerar peque˜nos rect´angulos todos del
mismo ancho y cuya altura se corresponde con el valor de f(x) de manera que el rect´angulo toque en
alg´un punto a la funci´on. En nuestro caso, consideraremos que la toca en el v´ertice superior izquierdo
(figura 1.b). Una buena aproximaci´on del ´area total que hay por debajo de la funci´on es la suma de
todos esos peque˜nos rect´angulos. Cuantos m´as rect´angulos utilicemos, mejor ser´a la aproximaci´on (y
m´as estrechos ser´an esos rect´angulos). Observa que si tenemos n rect´angulos, su anchura (base) es basei
= 1/n. Teniendo esto en cuenta, la aproximaci´on del ´area total de tierra ser´a:
A = ∑ areai = ∑ basei · alturai = ∑ 1
n · alturai =
n−1∑
i=0
1
n · f (i · 1
n )
El resultado de este c´alculo ser´a lo que mide el terreno de Ca´ın. El terreno que le corresponde a Abel
ser´a una hect´area menos lo que le corresponda a Ca´ın.
Ten presente que como Ca´ın y Abel utilizan todo tipo de polinomios de coeficientes enteros, es posible
que la funcion f(x) se salga del terreno que han heredado (eso ocurre cuando f(x) < 0 o f(x) > 1 ; ver la
figura 1.c). Para evitar problemas con los due˜nos de las tierras colindantes, hay que tener cuidado con
esos casos para no sumar nada a Ca´ın (si f(x) < 0 ) o sumarle s´olo el espacio de tierra que le corresponde
(si f(x) > 1 ).

Entrada
La entrada estar´a formada por un n´umero indeterminado de casos en los que se introducir´a el grado
del polinomio (entre 0 y 19, ambos inclusive), los coeficientes en orden decreciente respecto al grado
y el n´umero de rectangulos que queremos crear. Por ejemplo, la entrada (de coeficiente 3) 1 2 -1 1
representa el polinomio x 3 + 2x 2 – x + 1.
La entrada finalizar´a cuando el grado del polinomio sea 20.
Salida
Para cada caso de prueba, el programa indicar´a si el reparto es equitativo (escribiendo “JUSTO”), si
sale ganando el hermano que se queda con la secci´on inferior (“CAIN”) o si sale ganando el que opta por
la superior (“ABEL”). Recuerda que el reparto es justo si la diferencia de ´areas no excede 0.001 hm2.
Entrada de ejemplo
1
1 0
100
3
1 2 -1 0
1000
3
1 2 -1 1
1000
1
3 -1
10000
1
3 -1
2
20
Salida de ejemplo
ABEL
ABEL
CAIN
JUSTO
ABEL
