Problema n´umero 119
Escudos del ej´ercito romano

Son famosas las formaciones que el antiguo ej´ercito romano utilizaba para entrar en batalla. En
esas formaciones, los legionarios se agrupaban en una figura geom´etrica (normalmente un rect´angulo)
y proteg´ıan tanto los flancos como la parte superior utilizando escudos. Los legionarios que ocupaban
posiciones interiores cubr´ıan la parte superior colocando el escudo sobre su cabeza, mientras que los que
ocupaban los flancos llevaban dos y hasta tres escudos: uno para proteger la parte superior y uno o dos
escudos (si estaban en la esquina) para proteger los laterales. Con esta formaci´on, todos los legionarios
quedaban protegidos por los escudos y eran muy dif´ıciles de vencer.
Cuenta la historia1 que existi´o un general que estableci´o que la mejor figura para la formaci´on no era
la rectangular sino la cuadrada, de forma que el n´umero de filas y columnas de legionarios coincid´ıa. El
problema al que se enfrentaba este general era decidir en cu´antas formaciones (y de qu´e tama˜no) deb´ıa
separar su ej´ercito para que:
• No quedara ning´un legionario fuera de una formaci´on (aunque admit´ıa formaciones de un ´unico
legionario2).
• Se minimizara el n´umero de escudos necesarios para protegerlos.
Nuestro general, despu´es de hacer muchos c´alculos, decidi´o que la mejor manera de que estas dos
condiciones se cumpliesen era comenzar haciendo el cuadrado m´as grande posible con sus legionarios.
Con los que le quedasen libres volv´ıa a repetir la operaci´on, y as´ı hasta que no quedasen legionarios que
formar3.
Por ejemplo, si el n´umero de legionarios en el ej´ercito era 35, la manera utilizada por el general para
hacer la formaci´on consist´ıa en un cuadrado de 25 legionarios (5×5), otro de 9 (3×3) y otro de 1 (1×1):
* * * * *
* * * * * * * *
* * * * * * * * *
* * * * * * * *
* * * * *
Esta formaci´on requer´ıa un total de 71 escudos.
Entrada
La entrada estar´a compuesta de m´ultiples casos de prueba, cada uno en una l´ınea.
Cada caso de prueba indicar´a el n´umero de legionarios en el ej´ercito que se quiere poner en formaci´on
(un ej´ercito tiene como mucho diez millones de legionarios). La entrada terminar´a con un ej´ercito de
cero legionarios, que no provocar´a salida.
Salida
Para cada caso de prueba se escribir´a una l´ınea que indicar´a el n´umero de escudos m´ınimo que
necesitamos para cumplir las restricciones del general.
1Bueno, la historia inventada por los autores de este problema. . .
2No es de extra˜nar que ning´un legionario quisiera quedarse solo, ¡ten´ıa que acarrear un mont´on de escudos para estar
protegido!
3En una ocasi´on un legionario raso de su ej´ercito le mostr´o que su teor´ıa no era cierta. El pobre legionario termin´o
devorado por los leones, y su demostraci´on se perdi´o en la noche de los tiempos.

Entrada de ejemplo
35
20
10
0
Salida de ejemplo
71
44
26
