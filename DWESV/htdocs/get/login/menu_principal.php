<?php

if (isset($_GET['usuario']) && !empty($_GET['usuario'])) {
    $usuario = htmlspecialchars($_GET['usuario']);
    $mensaje = "¡Hola, " . $usuario . "!";
} else {
    header("Location: index.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>

</head>
<body>

    <h1>Menú Principal</h1>
    <div class="mensaje-bienvenida">
        <h2><?php echo $mensaje; ?></h2>
    </div>
    
    <div class="acciones">
        <a href="index.html">Cerrar Sesión</a>
    </div>

</body>
</html>