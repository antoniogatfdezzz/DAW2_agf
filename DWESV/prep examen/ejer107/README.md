Problema n´umero 107
Aproximaci´on de Gauss

Si hay un tipo de n´umeros importante y que es base de las matem´aticas ese es el de los n´umeros
primos.
Se dice que un n´umero es primo cuando s´olo es divisible por ´el mismo y la unidad1. Es decir, cuando
no puede descomponerse en producto de otros n´umeros.
Estos n´umeros han interesado a los matem´aticos desde el inicio de los tiempos, habiendo pruebas de
que se conoc´ıa su existencia antes del a˜no 1000 a.C. En la antigua Grecia se crearon las primeras tablas
de n´umeros primos.
Cuando Gauss era joven, recibi´o como regalo un libro que conten´ıa una lista de n´umeros primos. Pero
algo en la lista los hac´ıa desconcertantes: no hab´ıa una manera de, dado un n´umero primo, encontrar
el siguiente de la serie. Parec´ıa que hab´ıan sido elegidos al azar, as´ı que se decidi´o a buscar un modelo
que pudieran cumplir. En un mundo sin ordenadores, donde las cuentas se ten´ıan que hacer de forma
manual, era evidente la ventaja de encontrar ese modelo. Cuando Gauss lleg´o a la conclusi´on de que no
pod´ıa encontrar la respuesta que buscaba, pens´o en cambiar la pregunta. . . y su nueva cuesti´on fue:
“Si no puedo predecir cu´al ser´a el siguiente n´umero primo, quiz´a s´ı pueda contar cu´antos hay
antes de un n´umero natural dado.”
Una vez que se plante´o esto, lleg´o a realizar una aproximaci´on que a´un hoy, con las herramientas que
tenemos, sigue consider´andose buena. Esta aproximaci´on dice que el n´umero de primos entre 1 y N es
de N
ln(N ) , donde ln() es el logaritmo natural.
Esto se concreta en el Teorema de los N´umeros Primos, que dice lo siguiente:
π(n)
n → 1
ln(n) para n suficientemente grandes
donde π(n) representa el n´umero de primos entre 1 y n, y el “→” significa “tiende a”. De esta manera
consideraremos el error producido por esta aproximaci´on como:
error = π(n)
n − 1
ln(n)
Entrada
El programa recibir´a una serie de casos de prueba.
Cada caso de prueba se especificar´a en una l´ınea con dos enteros positivos. El primero, n, ser´a un
n´umero natural positivo, menor que 100.000, para el que se quiere poner a prueba la aproximaci´on de
Gauss. El segundo, m ser´a un valor entre 0 y 5 que servir´a para calcular el m´aximo error permitido
mediante la f´ormula:
error = 1
10m siendo m un entero
El caso de prueba 0 0 ser´a especial y marcar´a el final de la entrada.
Salida
El programa indicar´a Mayor si el error (en valor absoluto) de la aproximaci´on de Gauss es mayor que
el m´aximo permitido, Igual si es el mismo, y Menor si es menor.
1Ten en cuenta que el 1 no se considera primo.

Entrada de ejemplo
10 3
750 2
65535 2
65535 3
10000 2
99999 1
0 0
Salida de ejemplo
Mayor
Mayor
Menor
Mayor
Mayor
Menor