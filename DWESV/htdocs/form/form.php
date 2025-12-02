<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    echo "<pre>"; 
    print_r($_POST);
    print_r($_FILES);
    echo "<pre>";

    if (empty($_POST)){
        include('index.html');
        die();
    }
    echo "Proceso el Formulario";
    if (!move_uploaded_file($_FILES['fichero1']['tmp_file'], 'uploads'.DIRECTORY_SEPARATOR.'mi_fichero')){
        echo "LA LIAMOS PARDA";
    } else {
        echo "Todo OK";
        echo "<br /> <a href='uploads/mi_fichero' download>Descargar</a>";
    }

?>