<?php

$usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
if ($usuario === '') {
	header('Location: index.html');
	exit;
}

$plantillaPath = __DIR__ . '/menu_principal.html';
if (!file_exists($plantillaPath)) {
	http_response_code(500);
	echo 'No se encontró la plantilla de menú principal.';
	exit;
}

$html = file_get_contents($plantillaPath);

$usuarioSeguro = htmlspecialchars($usuario, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$salida = str_replace('{{usuario}}', $usuarioSeguro, $html);

header('Content-Type: text/html; charset=UTF-8');
echo $salida;
?>
