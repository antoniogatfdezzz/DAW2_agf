<html>
    <head>
        <meta charset="UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Paso por Valor o por Referencia</title>
    </head>
    <body>
        <h1>Paso por Valor o por Referencia</h1>
        <?php
            $a = 7;
            $b = 5;

            echo '$a = ' . $a . '$b = ' . $b . '<br>';
            cambiar($a, $b);

            $c = $a;
            $a = $b;
            $b = $c;

            echo 'Ahora $a = ' . $a . '$b = ' . $b . '<br>';

            function cambiar ($a, $b) {
                $c = $a;
                $a = $b;
                $b = $c;            
            }
        ?>
    </body>
</html>

