<html>
    <head>
        <meta charset="UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tablas de Multiplicar</title>
    </head>
    <body>
        <h1>Tablas de Multiplicar</h1>
        <?php
            $inicio = 2;
            $fin = 5;
            
            for ($numero = $inicio; $numero <= $fin; $numero++) {
                echo "<div class='tabla'>";
                echo "<h2 class='numero'>Tabla del $numero</h2>";
                
                for ($multiplicador = 1; $multiplicador <= 10; $multiplicador++) {
                    $resultado = $numero * $multiplicador;
                    echo "<div class='multiplicacion'>";
                    echo "$numero × $multiplicador = <span class='resultado'>$resultado</span>";
                    echo "</div>";
                }
                
                echo "</div>";
            }
        ?>
    </body>
</html>

