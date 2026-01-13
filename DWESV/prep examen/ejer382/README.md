Problema n´umero 382
Internet en el metro

Para fomentar el uso del transporte p´ublico, el alcalde de mi ciudad
ha pedido a la empresa concesionaria del servicio de metro que instale
antenas wifi en los t´uneles para dar acceso gratuito a Internet en
toda la red. De esta forma, los ciudadanos no tendr´an excusa para
no utilizar este medio de transporte, ya que podr´an aprovechar sus
desplazamientos para navegar.
Para asegurarse de que los ciudadanos estar´an satisfechos con el
sistema, ha pedido a la empresa que le indique la localizaci´on de las
antenas y la cobertura de cada una de ellas para poder comprobar
que a nadie se le cortar´a la conexi´on.
Entrada
La entrada comienza con el n´umero de casos de prueba. Para cada caso de prueba se indica la longitud
del t´unel seguida del n´umero de antenas que se han instalado. En la l´ınea siguiente se indica, para cada
antena, la distancia a la que se encuentra del comienzo del t´unel seguida de su radio de cobertura (es
decir, la antena cubre esa distancia en cada uno de los sentidos).
Se garantiza que el n´umero de antenas est´a entre 1 y 1.000 y tanto la longitud del t´unel como los
radios de cobertura est´an entre 1 y 109, aunque ninguna antena cubrir´a m´as all´a del punto 109. Adem´as,
las antenas aparecen en la entrada ordenadas por distancia al inicio del t´unel.
Salida
Para cada caso de prueba el programa debe escribir en una l´ınea distinta SI si el t´unel est´a comple-
tamente cubierto o NO en caso contrario.
Entrada de ejemplo
4
1500 2
500 500 1000 500
50 2
10 10 40 10
50 3
10 5 30 5 30 25
100 3
30 30 70 5 75 25
Salida de ejemplo
SI
NO
NO
SI
