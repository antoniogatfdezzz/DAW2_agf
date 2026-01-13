Problema n´umero 778
Arreglando el algoritmo

Paula est´a empezando a estudiar algoritmia. Despu´es de aprender la sintaxis del lenguaje toca ahora
aprender los algoritmos m´as conocidos. Hoy le ha llegado el turno a los distintos m´etodos de ordenaci´on
entre los que se encuenta el algoritmo de selecci´on.
La idea del algoritmo es sencilla. Si queremos ordenar de menor a mayor un vector v de n elementos,
hacemos un primer recorrido del vector completo buscando el elemento m´as peque˜no y lo colocamos en
v[0]. Despu´es volvemos a recorrer el vector, esta vez empezando desde la posici´on 1, para encontrar el
elemento m´ınimo de nuevo y lo colocamos en v[1]. El proceso contin´ua iterativamente de forma que
en un determinado momento tenemos los i primeros n´umeros ya colocados (en las posiciones 0. . . i–1),
buscamos el elemento m´as peque˜no en las posiciones i. . . n–1 y lo colocamos en v[i].
El c´odigo en un lenguaje con sintaxis tipo C es algo as´ı:
Para practicar, Paula ha implementado una versi´on alternativa del algoritmo de selecci´on que en
lugar de ir ordenando el vector de izquierda a derecha buscando el m´ınimo cada vez, ordena de derecha
a izquierda buscando el m´aximo. Lamentablemente ha fallado algo, porque al ejecutarlo ha saltado una
excepci´on extra˜na y el algoritmo ha terminado de forma abrupta. Para buscar el error ha conseguido
ver c´omo estaba el vector en el momento del fallo y se pregunta cu´antos valores hab´ıa ordenado hasta
ese momento.
Entrada
La entrada estar´a compuesta de varios casos de prueba. Cada caso consistir´a en dos l´ıneas. La
primera contiene el n´umero n de elementos que tiene el vector que Paula estaba intentando ordenar. La
segunda l´ınea contiene los valores del vector en el momento del fallo separados por espacios. Los valores
son n´umeros enteros cuyo valor absoluto no supera 1018.
Tras el ´ultimo caso de prueba aparece una l´ınea con un 0 que no debe procesarse.
Salida
Por cada caso de prueba se escribir´a una l´ınea con un ´unico n´umero que indica cu´antos elementos,
como mucho, hab´ıa ordenado el algoritmo antes de fallar.
Entrada de ejemplo
4
1 2 2 3
6
5 4 6 7 8 9
0

Salida de ejemplo
4
4