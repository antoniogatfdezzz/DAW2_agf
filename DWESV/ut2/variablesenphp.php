<html>
    <head>
        <meta charset="UTF-8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Variables en PHP</title>
    </head>
    <body>
        <?php
            $legionarios;
            $escudos;

            for($i = 0; $i < 7; $i++){
                $legionarios = $i;
                if ($i < 4){
                    $escudos = $legionarios * 5;
                }else{
                    $escudos = 12 + 5 * ($i - 4);
                }
                echo "<p> Para $legionarios legionarios hacen falta $escudos escudos (cálculo original). </p>";
            } 

            function escudosCuadrado($lado){
                return $lado**2 + $lado * 4;
            }

            function formarEjercitoRomano($numLegionarios) {
                $escudosTotales = 0;
                $legionariosRestantes = $numLegionarios;
                $formacionNum = 1;
                
                echo "<h3>Formación del ejército con $numLegionarios legionarios:</h3>";
                
                while ($legionariosRestantes > 0) {
                    $lado = (int)sqrt($legionariosRestantes);
                    $legionariosEnCuadrado = $lado * $lado;
                    $escudosNecesarios = escudosCuadrado($lado);
                    
                    $escudosTotales += $escudosNecesarios;
                    $legionariosRestantes -= $legionariosEnCuadrado;
                    
                    echo "<p>Formación $formacionNum: Cuadrado de {$lado}×{$lado} = $legionariosEnCuadrado legionarios, $escudosNecesarios escudos</p>";
                    $formacionNum++;
                }
                
                echo "<p><strong>Total de escudos necesarios: $escudosTotales</strong></p>";
                echo "<br>";
            }

            formarEjercitoRomano(35);
            formarEjercitoRomano(12);
            formarEjercitoRomano(50);
            formarEjercitoRomano(71);
        ?>
    </body>
</html>

