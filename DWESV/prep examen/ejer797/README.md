Problema n´umero 797
Anuncios de m´oviles

Cuando a Cayo Lograbo le propusieron grabar el anuncio para el lan-
zamiento del ´ultimo m´ovil de moda, no lo dud´o dos veces y acept´o. Ser´ıa
una buena l´ınea para a˜nadir en su curr´uculum como c´amara de televisi´on
y cine y le dar´ıa unos ingresos que le ven´ıan de perlas.
Cuando el director de la campa˜na le dijo qu´e tipo de tomas ten´ıa
en mente, la cosa ya no le pareci´o tan maravillosa. Quer´ıa colocar en
un campo de deportes un mont´on de m´oviles formando una cuadr´ıcula.
Teniendo algunos encendidos y otros apagados se formar´ıa, a modo de
pixel art, una imagen con el logotipo de la compa˜n´ıa.
Le dieron los m´oviles necesarios y muy poco tiempo, de modo que solo pudo cargar la bater´ıa de los
que pondr´ıa encendidos para formar la imagen. Luego coloc´o todos en el campo, formando una inmensa
matriz y pudo, por fin, grabar la toma.
Cuando estaban ya celebrando en un bar el trabajo bien hecho, vino la puntilla del director. ¡Bien! —
dijo — Ma˜nana grabamos la ´ultima, con los m´oviles configurados para formar un pulgar hacia arriba.
La papeleta que se encontr´o al d´ıa siguiente fue grande: ten´ıa un mont´on de m´oviles colocados,
algunos apagados sin bater´ıa, otros encendidos y otros colgados mostrando un mensaje de error pidiendo
un reinicio. Su tarea: conseguir la imagen final lo antes posible, teniendo en cuenta que lo que pod´ıa
hacer era:
• Apagar un m´ovil que estuviera encendido y dejarlo apagado.
• Apagar o reiniciar un m´ovil colgado mostrando el mensaje de error.
• Intercambiar dos m´oviles de posici´on.
¿Cu´antas de esas acciones ten´ıa que hacer como m´ınimo para conseguir la imagen final?
Entrada
La entrada est´a compuesta por distintos casos de prueba.
Cada caso de prueba comienza con una l´ınea indicando el n´umero de filas y de columnas (entre 1 y
1)   que tiene la cuadr´ıcula de m´oviles. A continuaci´on aparecen, uno a la derecha del otro, el estado
de los m´oviles por la ma˜nana y el deseado para grabar la toma. En particular, habr´a tantas l´ıneas como
filas tiene la cuadr´ıcula, cada una de ellas con dos bloques de caracteres, separados por un espacio. Cada
bloque est´a compuesto de tantas letras como columnas tiene la cuadr´ıcula.
Los m´oviles apagados (y sin bater´ıa) aparecen con un 0, los encendidos con un 1 y los colgados con
una R. La primera figura, a la izquierda, indica la configuraci´on inicial y la segunda, a la derecha, la
deseada. Se garantiza que en la deseada no habr´a ninguna posici´on con una R.
La entrada termina con dos ceros.
Salida
Por cada caso de prueba se escribir´a una ´unica l´ınea con el n´umero m´ınimo de operaciones que hay
que realizar para conseguir la imagen final. Si no se puede, se escribir´a IMPOSIBLE.
Un m´ovil encendido puede apagarse, un m´ovil colgado puede apagarse o reiniciarse para pasar a estar
encendido, y dos m´oviles pueden intercambiarse entre ellos. Ten en cuenta que los m´oviles apagados est´an
sin bater´ıa y no pueden encenderse.

Entrada de ejemplo
2 3
000 000
111 111
2 3
100 000
011 111
2 3
00R 011
001 100
0 0
Salida de ejemplo
0
1
IMPOSIBLE
