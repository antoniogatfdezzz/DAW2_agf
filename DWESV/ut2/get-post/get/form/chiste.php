<?php
$nombre = $_GET['nombre'];
$ciudad = $_GET['ciudad'];
$animal = $_GET['animal'];

$chiste = " $nombre  $ciudad  $animal ";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiste</title>
</head>
<body>
    <p><?php echo $chiste; ?></p>
    <a href="puntua.html?nombre=<?php echo $nombre; ?>">Votar</a>
</body>
</html>