<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo 'Método no permitido. Debes enviar el formulario por POST.';
	exit;
}

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? (string)$_POST['password'] : '';

if ($usuario === '' || $password === '') {
	http_response_code(400);
	echo 'Faltan campos obligatorios.';
	exit;
}

$usuarioParam = urlencode($usuario);
header("Location: menu_principal.php?usuario={$usuarioParam}");
exit;
?>
