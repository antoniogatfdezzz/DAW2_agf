<!DOCTYPE html>
<html lang=es>

<head>
	<meta charset=utf-8 />
	<meta name=viewport content='width=device-width, initial-scale=1' />
	<meta name=author content='Antonio Gat  <me@antoniogatfdez.me' />
	<link rel=icon type=image/x-icon href=/img/logo.png />
	<title>Hola Mundo</title>
</head>
<body>

<?php
     //phpinfo();

    echo "<h1>¡Hola Mundo!</h1>";

    $edad = 18;
    $precio = 19.99;
    $nombre = "Antonio";
    $es_estudiante = true;

    echo "<p>Mi nombre es $nombre, tengo $edad años y el precio es $$precio.</p>";
    echo "<p>La variable $edad tiene el valor.</p>";


    /*COMENTAIO DE MUCHAS LETRAS
    $edad = 20;SDJFNSADFSANFNSAOIF  Ç
    FSAFPISMADFMSAPFMSADF
    Ç*/

    /** Arrays */
    $frutas = array("Manzana", "Banana", "Cereza");
    echo "<p>La primera fruta es: " . $frutas[1] . "</p>";

    /* Arrays Asociativos */
    $persona = array("nombre" => "Antonio",
                     "edad" => 18,
                      "ciudad" => "Madrid"
                    );
    echo "<p>La persona se llama " . $persona["nombre"] . " y tiene " . $persona["edad"] . " años.</p>";

    /* Objetos */
    $objeto = new stdClass();
    $objeto->tipo = "Mineral";
    $objeto->peso = 3.5;

    /* Tipos esenciales */
    $variableNula = null;
    $variableNoDefinida;
    
    echo "La variable $variableNula vale";
    var_dump($variableNula);
    echo "<br>";

    echo "<p>El tipo de $edad es: " . gettype($edad) . "</p>";
    echo "<p>¿Es $nombre un string? " . is_string($nombre) . "</p>";

    /*Estructuras de control */
    
?>
    
</body>


</html>
