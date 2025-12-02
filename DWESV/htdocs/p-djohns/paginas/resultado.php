<?php
// Reiniciar cookies si se solicita
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
	setcookie('karma', '', time() - 3600, '/');
	setcookie('acciones', '', time() - 3600, '/');
	setcookie('dead', '', time() - 3600, '/');
	// Redirigir al índice relativo (../index.php) dentro del proyecto
	header('Location: ../index.php');
	exit;
}

$karma = 0;
$acciones = [];
$isDead = false;

if (isset($_COOKIE['karma'])) {
	$karma = intval($_COOKIE['karma']);
}

// Determinar destino según el karma
$destino = 'Limbo';
if ($karma > -10) {
	$destino = 'Cielo';
} elseif ($karma < -10) {
	$destino = 'Infierno';
}

if (isset($_COOKIE['dead']) && $_COOKIE['dead'] == '1') {
	$isDead = true;
}

if (isset($_COOKIE['acciones'])) {
	// La cookie fue codificada por JS con encodeURIComponent(JSON.stringify(...))
	$raw = urldecode($_COOKIE['acciones']);
	$decoded = json_decode($raw, true);
	if (is_array($decoded)) {
		$acciones = $decoded;
		// No dependemos de una entrada 'morir' dentro de acciones para marcar muerte;
		// usamos únicamente la cookie 'dead'. Además, cuando mostremos las acciones
		// filtraremos cualquier entrada 'morir' para no mostrarla.
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Resultado - Karma</title>


	<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header class="site-title">
        <?php if ($isDead): ?>
			<h2 class="dead">Has muerto ☠️</h2>
		<?php endif; ?>
    </header>

	<div class="container">
		<div class="card">
			<div class="header">
				<h1>Resultado</h1>
			</div>
			<div class="karma">
				<div class="value"><?php echo htmlspecialchars((string)$karma, ENT_QUOTES, 'UTF-8'); ?></div>
				<div class="hint">Karma total</div>
			</div>

		

		<?php ?>
		<?php $cl = strtolower($destino); ?>
		<div class="destiny <?php echo ($cl === 'cielo' ? 'cielo' : ($cl === 'infierno' ? 'infierno' : 'limbo')); ?>">
			<?php if ($destino === 'Cielo'): ?>
				<span class="emoji">✨</span> Vas al CIELO
			<?php elseif ($destino === 'Infierno'): ?>
				<span class="emoji">🔥</span> Vas al INFIERNO
			<?php else: ?>
				<span class="emoji">🌓</span> Estás en el LIMBO
			<?php endif; ?>
		</div>

			<h3>Acciones realizadas:</h3>
			<?php if (empty($acciones)): ?>
				<p>No se han recogido acciones.</p>
			<?php else: ?>
				<div class="actions">
					<ul>
				<?php foreach ($acciones as $a): 
					$nombre = isset($a['accion']) ? $a['accion'] : '(sin nombre)';
					if ($nombre === 'morir') continue;
					$valor = isset($a['valor']) ? intval($a['valor']) : 0;
					$ts = isset($a['ts']) ? intval($a['ts']) : 0;
					$fecha = $ts ? date('Y-m-d H:i:s', intval($ts/1000)) : '—';
				?>
					<li>
						<strong><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></strong>
						(<?php echo $valor >= 0 ? '+' . $valor : $valor; ?>) — <small><?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?></small>
					</li>
				<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="meta">
				<a class="btn primary" href="?reset=1">Volver a jugar</a>
			</div>
		</div>
	</div>
</body>
</html>
