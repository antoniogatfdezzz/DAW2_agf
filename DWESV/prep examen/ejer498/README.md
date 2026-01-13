Problema n´umero 498
Entrenamiento de los monjes de Hanoi

Todo el mundo (bueno, m´as o menos) sabe que los monjes de Hanoi deben efectuar 264 – 1 movimientos
para llevar los 64 discos del primero al segundo de los tres postes respetando, naturalmente, la regla de
no poner un disco encima de otro que tenga un di´ametro menor. Para ello basta mover recursivamente:
• 63 discos del poste 1 al 3 usando el poste 2 como auxiliar,
• 1 disco del poste 1 al 2,
• 63 discos del poste 3 al 2 usando el poste 1 como auxiliar.
Antes de pasar al monasterio de Hanoi, los monjes deben practicar en el monasterio Ada-Byron,
donde se trabaja con solo 32 discos. Para facilitar el aprendizaje, los monjes han numerado los discos
del 1 al 32, y se han repartido en 32 grupos donde cada grupo se encarga en exclusiva de mover uno
de los discos cuando le llega el turno al disco en cuesti´on. Adem´as, y para estar atentos, los monjes del
disco k han elaborado una lista con los movimientos espec´ıficos donde tocar´a mover el disco k -´esimo.
En esa lista hay dos movimientos destacados: cuando el disco k se mueve por k -´esima vez y cuando,
tras moverlo, se entra en los ´ultimos k movimientos del disco. Tras ambos movimientos (naturalmente,
si se dan), los monjes deben tocar una campana.
Para entrenarse a fondo, los monjes del disco k van a practicar el toque de campana trabajando con
un n´umero n de discos con k ≤ n ≤ 32. Para uno de estos n, ¿en qu´e movimientos m1, m2 tocar´an los
monjes del disco k la campana?
Entrada
La entrada est´a formada por varias l´ıneas con dos enteros k, n. Se garantiza 1 ≤ k ≤ n ≤ 32. El final
de la entrada se indica con una l´ınea con dos ceros que no se debe procesar.
Salida
Para cada caso de prueba debe escribirse una l´ınea con los valores k, n, m1 y m2, donde k y n son
los datos de entrada, m1 es un entero con el movimiento tras el que se toca la campana por primera vez
(–1 si no es posible) y m2 es un entero con el movimiento tras el que se toca la campana por segunda
vez (–1 si no es posible).
Entrada de ejemplo
1 3
2 6
2 3
0 0

Salida de ejemplo
1 3 1 5
2 6 6 54
2 3 6 -1
