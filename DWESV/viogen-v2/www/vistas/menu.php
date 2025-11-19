<?php
session_start();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú principal - Viogen</title>
</head>
<body>
    <h1>Menú principal</h1>
    <p>Usuario: <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Anónimo') ?></p>
    <?php if ($flash): ?>
        <p style="color: green"><?= htmlspecialchars($flash) ?></p>
    <?php endif; ?>

    <nav>
        <ul>
            <li><a href="index.php?action=victim_form">Registrar víctima</a></li>
            <li><a href="index.php?action=agresion_form">Registrar agresión</a></li>
            <li><a href="index.php?action=report">Informe / Buscar agresiones</a></li>
            <li><a href="index.php?action=logout">Cerrar sesión</a></li>
        </ul>
    </nav>

    <p>Use el buscador del informe para buscar texto en todos los campos del sistema.</p>

</body>
</html>
