<?php

if (isset($_GET['usuario']) && isset($_GET['password'])) {
    $usuario = $_GET['usuario'];
    $password = $_GET['password'];
    
    if (!empty($usuario) && !empty($password)) {
        header("Location: menu_principal.php?usuario=" . urlencode($usuario));
        exit();
    } else {
        $error = "Por favor, complete todos los campos.";
    }
} else {
    $error = "Acceso no autorizado.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Login</title>

</head>
<body>

    <h1>Error en el Login</h1>
    <p><?php echo isset($error) ? $error : 'Error desconocido'; ?></p>
    <a href="index.html">Volver al formulario de login</a>
    
</body>
</html>