<?php
$nombre = $_GET['nombre'];
$ciudad = $_GET['ciudad'];
$animal = isset($_GET['animal']) ? implode(", ", $_GET['animal']) : 'Ninguno';

header("Location: chiste.php?nombre=$nombre&ciudad=$ciudad&animal=$animal");
exit();
?>